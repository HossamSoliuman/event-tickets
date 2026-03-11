<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\StripeService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    use ApiResponse;
    public function __construct(private StripeService $stripe) {}

    /**
     * POST /api/payments/create-intent
     *
     * Creates a Stripe PaymentIntent for an existing pending order.
     * Returns the client_secret for the frontend Stripe.js to use.
     *
     * Flow:
     *   1. User creates order (POST /api/orders) → gets order_id
     *   2. Frontend calls this endpoint with order_id
     *   3. Frontend uses client_secret with Stripe.js to collect card
     *   4. Frontend calls POST /api/payments/confirm after Stripe confirms
     */
    public function createIntent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
        ]);

        $order = Order::where('id', $validated['order_id'])
            ->where('user_id', $request->user()->id)
            ->where('status', Order::STATUS_PENDING)
            ->firstOrFail();

        try {
            $result = $this->stripe->createPaymentIntent($order);

            return $this->apiResponse([
                'client_secret'     => $result['client_secret'],
                'payment_intent_id' => $result['payment_intent_id'],
                'order_id'          => $order->id,
                'amount'            => $order->total_amount,
                'currency'          => 'usd',
            ]);
        } catch (\Exception $e) {
            Log::error('Stripe createIntent failed', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
            return $this->apiResponse(null, 'Payment initiation failed.', 0, 500);
        }
    }

    /**
     * POST /api/payments/confirm
     *
     * Called by the frontend after Stripe.js confirms the payment on the client side.
     * We verify the PaymentIntent status server-side before marking the order as paid.
     */
    public function confirmPayment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'payment_intent_id' => 'required|string',
            'order_id'          => 'required|integer|exists:orders,id',
        ]);

        $order = Order::where('id', $validated['order_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        try {
            $intent = $this->stripe->retrievePaymentIntent($validated['payment_intent_id']);

            if ($intent->metadata['order_id'] != $order->id) {
                return $this->apiResponse(null, 'Payment intent mismatch.', 0, 422);
            }

            DB::transaction(function () use ($order, $intent) {
                if ($intent->status === 'succeeded') {
                    $order->update([
                        'status'                => Order::STATUS_PAID,
                        'stripe_payment_status' => $intent->status,
                    ]);
                } else {
                    $order->event->increment('available_tickets', $order->quantity);
                    $order->update([
                        'status'                => Order::STATUS_FAILED,
                        'stripe_payment_status' => $intent->status,
                    ]);
                }
            });

            if ($order->status === Order::STATUS_PAID) {
                return $this->apiResponse(
                    ['order' => ['id' => $order->id, 'status' => $order->status]],
                    'Payment confirmed. Enjoy the event!'
                );
            }

            return $this->apiResponse(null, 'Payment not completed.', 0, 422);

        } catch (\Exception $e) {
            Log::error('Payment confirmation failed', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
            return $this->apiResponse(null, 'Payment verification failed.', 0, 500);
        }
    }

    /**
     * POST /api/webhook/stripe  (no auth middleware)
     *
     * Handles Stripe webhook events as a backup confirmation mechanism.
     * Critical: always verify the Stripe-Signature header.
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = $this->stripe->constructWebhookEvent($payload, $sigHeader);
        } catch (\Exception $e) {
            Log::warning('Stripe webhook signature verification failed: ' . $e->getMessage());
            return $this->apiResponse(null, 'Invalid signature.', 0, 400);
        }

        match ($event->type) {
            'payment_intent.succeeded'              => $this->handlePaymentSucceeded($event->data->object),
            'payment_intent.payment_failed'         => $this->handlePaymentFailed($event->data->object),
            default                                 => Log::info("Unhandled Stripe event: {$event->type}"),
        };

        return $this->apiResponse(null, 'Webhook received.');
    }

    private function handlePaymentSucceeded(\Stripe\PaymentIntent $intent): void
    {
        $orderId = $intent->metadata['order_id'] ?? null;
        if (!$orderId) return;

        $order = Order::find($orderId);
        if (!$order || $order->isPaid()) return;

        $order->update([
            'status'                => Order::STATUS_PAID,
            'stripe_payment_status' => $intent->status,
        ]);

        Log::info("Order #{$order->id} marked as PAID via webhook.");
    }

    private function handlePaymentFailed(\Stripe\PaymentIntent $intent): void
    {
        $orderId = $intent->metadata['order_id'] ?? null;
        if (!$orderId) return;

        $order = Order::find($orderId);
        if (!$order || !$order->isPending()) return;

        DB::transaction(function () use ($order, $intent) {
            $order->event->increment('available_tickets', $order->quantity);
            $order->update([
                'status'                => Order::STATUS_FAILED,
                'stripe_payment_status' => $intent->status,
            ]);
        });

        Log::info("Order #{$order->id} marked as FAILED via webhook.");
    }
}