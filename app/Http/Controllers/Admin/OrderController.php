<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\OrderController as BaseOrderController;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * GET /api/admin/orders
     * List ALL orders with filtering, searching, and pagination.
     *
     * Query params:
     *   - status     (pending|paid|failed|refunded)
     *   - event_id   (integer)
     *   - user_email (string, partial match)
     *   - date_from  (Y-m-d)
     *   - date_to    (Y-m-d)
     *   - per_page   (default 20)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['user', 'event'])->latest();

        // Filter by payment status
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        // Filter by event
        if ($eventId = $request->query('event_id')) {
            $query->where('event_id', $eventId);
        }

        // Filter by user email
        if ($email = $request->query('user_email')) {
            $query->whereHas('user', fn ($q) => $q->where('email', 'like', "%{$email}%"));
        }

        // Filter by date range
        if ($dateFrom = $request->query('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->query('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $orders = $query->paginate($request->query('per_page', 20));

        return response()->json([
            'data' => $orders->map(fn ($o) => $this->formatAdminOrder($o)),
            'pagination' => [
                'total'        => $orders->total(),
                'per_page'     => $orders->perPage(),
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
            ],
        ]);
    }

    /**
     * GET /api/admin/orders/{id}
     * Full order detail for admin.
     */
    public function show(int $id): JsonResponse
    {
        $order = Order::with(['user', 'event'])->findOrFail($id);

        return response()->json([
            'data' => $this->formatAdminOrder($order),
        ]);
    }

    private function formatAdminOrder(Order $order): array
    {
        return [
            'id'                       => $order->id,
            'quantity'                 => $order->quantity,
            'total_amount'             => $order->total_amount,
            'total_amount_display'     => '$' . number_format($order->total_amount_in_dollars, 2),
            'status'                   => $order->status,
            'stripe_payment_intent_id' => $order->stripe_payment_intent_id,
            'stripe_payment_status'    => $order->stripe_payment_status,
            'created_at'               => $order->created_at->toIso8601String(),
            'user'                     => $order->user ? [
                'id'    => $order->user->id,
                'name'  => $order->user->name,
                'email' => $order->user->email,
            ] : null,
            'event'                    => $order->event ? [
                'id'    => $order->event->id,
                'title' => $order->event->title,
                'date'  => $order->event->date->toIso8601String(),
                'venue' => $order->event->venue,
            ] : null,
        ];
    }
}
