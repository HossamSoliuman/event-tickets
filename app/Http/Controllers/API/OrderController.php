<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use ApiResponse;
    /**
     * GET /api/orders
     * List the authenticated user's own orders.
     */
    public function index(Request $request): JsonResponse
    {
        $orders = Order::with('event')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return $this->apiResponse([
            'orders'     => $orders->map(fn ($o) => $this->formatOrder($o)),
            'pagination' => [
                'total'        => $orders->total(),
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
            ],
        ]);
    }

    /**
     * POST /api/orders
     * Create a new pending order (before payment).
     * Returns the order ID so the frontend can proceed to payment.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event_id' => 'required|integer|exists:events,id',
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $event = Event::findOrFail($validated['event_id']);

        if ($event->isSoldOut()) {
            return $this->apiResponse(null, 'This event is sold out.', 0, 422);
        }

        if ($event->available_tickets < $validated['quantity']) {
            return $this->apiResponse(null, "Only {$event->available_tickets} ticket(s) remaining.", 0, 422);
        }

        if ($event->date->isPast()) {
            return $this->apiResponse(null, 'This event has already passed.', 0, 422);
        }

        $totalAmount = $event->price * $validated['quantity']; // in cents

        $order = DB::transaction(function () use ($request, $event, $validated, $totalAmount) {
            $event->decrement('available_tickets', $validated['quantity']);

            return Order::create([
                'user_id'      => $request->user()->id,
                'event_id'     => $event->id,
                'quantity'     => $validated['quantity'],
                'total_amount' => $totalAmount,
                'status'       => Order::STATUS_PENDING,
            ]);
        });

        return $this->apiResponse(
            ['order' => $this->formatOrder($order->load('event'))],
            'Order created. Proceed to payment.',
            1,
            201
        );
    }

    /**
     * GET /api/orders/{id}
     * Get a specific order (must belong to authenticated user).
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $order = Order::with(['event', 'user'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return $this->apiResponse(['order' => $this->formatOrder($order, detailed: true)]);
    }

    public static function formatOrder(Order $order, bool $detailed = false): array
    {
        $data = [
            'id'                       => $order->id,
            'quantity'                 => $order->quantity,
            'total_amount'             => $order->total_amount,
            'total_amount_display'     => '$' . number_format($order->total_amount_in_dollars, 2),
            'status'                   => $order->status,
            'stripe_payment_intent_id' => $order->stripe_payment_intent_id,
            'stripe_payment_status'    => $order->stripe_payment_status,
            'created_at'               => $order->created_at->toIso8601String(),
            'event'                    => $order->event ? [
                'id'    => $order->event->id,
                'title' => $order->event->title,
                'date'  => $order->event->date->toIso8601String(),
                'venue' => $order->event->venue,
            ] : null,
        ];

        if ($detailed && $order->user) {
            $data['user'] = [
                'id'    => $order->user->id,
                'name'  => $order->user->name,
                'email' => $order->user->email,
            ];
        }

        return $data;
    }
}