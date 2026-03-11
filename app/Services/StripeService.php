<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Webhook;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\SignatureVerificationException;
use App\Models\Order;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create a PaymentIntent for the given order.
     * Returns the client_secret needed by the frontend.
     */
    public function createPaymentIntent(Order $order): array
    {
        try {
            $intent = PaymentIntent::create([
                'amount'               => $order->total_amount, // in cents
                'currency'             => 'usd',
                'automatic_payment_methods' => ['enabled' => true],
                'metadata'             => [
                    'order_id'  => $order->id,
                    'event_id'  => $order->event_id,
                    'user_id'   => $order->user_id,
                ],
            ]);

            // Save the PaymentIntent ID on the order
            $order->update([
                'stripe_payment_intent_id' => $intent->id,
                'stripe_payment_status'    => $intent->status,
            ]);

            return [
                'client_secret'      => $intent->client_secret,
                'payment_intent_id'  => $intent->id,
            ];
        } catch (ApiErrorException $e) {
            throw new \Exception('Stripe error: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve a PaymentIntent by ID and check its status.
     */
    public function retrievePaymentIntent(string $paymentIntentId): PaymentIntent
    {
        try {
            return PaymentIntent::retrieve($paymentIntentId);
        } catch (ApiErrorException $e) {
            throw new \Exception('Stripe error: ' . $e->getMessage());
        }
    }

    /**
     * Validate and construct a webhook event from Stripe's raw payload.
     * Throws SignatureVerificationException on invalid signature.
     */
    public function constructWebhookEvent(string $payload, string $sigHeader): \Stripe\Event
    {
        $secret = config('services.stripe.webhook_secret');

        return Webhook::constructEvent($payload, $sigHeader, $secret);
    }
}
