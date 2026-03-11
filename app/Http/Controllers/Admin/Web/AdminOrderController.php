<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'event'])->latest();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($eventId = $request->query('event_id')) {
            $query->where('event_id', $eventId);
        }

        if ($search = $request->query('search')) {
            $query->whereHas('user', fn ($q) =>
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
            );
        }

        if ($dateFrom = $request->query('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->query('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $orders = $query->paginate(20)->withQueryString();
        $events  = Event::orderBy('title')->get(['id', 'title']);

        return view('admin.orders.index', compact('orders', 'events'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'event']);
        return view('admin.orders.show', compact('order'));
    }
}
