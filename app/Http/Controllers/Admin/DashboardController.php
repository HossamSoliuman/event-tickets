<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * GET /api/admin/stats
     * Returns high-level statistics for the admin dashboard.
     */
    public function stats(): JsonResponse
    {
        $totalRevenue = Order::where('status', Order::STATUS_PAID)->sum('total_amount');

        $recentOrders = Order::with(['user', 'event'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($o) => [
                'id'           => $o->id,
                'user_name'    => $o->user?->name,
                'user_email'   => $o->user?->email,
                'event_title'  => $o->event?->title,
                'quantity'     => $o->quantity,
                'total_amount' => '$' . number_format($o->total_amount / 100, 2),
                'status'       => $o->status,
                'created_at'   => $o->created_at->toIso8601String(),
            ]);

        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $topEvents = Event::withCount(['orders as paid_orders_count' => fn ($q) => $q->where('status', Order::STATUS_PAID)])
            ->withSum(['orders as total_revenue' => fn ($q) => $q->where('status', Order::STATUS_PAID)], 'total_amount')
            ->orderByDesc('paid_orders_count')
            ->limit(5)
            ->get()
            ->map(fn ($e) => [
                'id'            => $e->id,
                'title'         => $e->title,
                'orders_count'  => $e->paid_orders_count,
                'revenue'       => '$' . number_format(($e->total_revenue ?? 0) / 100, 2),
            ]);

        return response()->json([
            'overview' => [
                'total_events'   => Event::count(),
                'active_events'  => Event::where('is_active', true)->where('date', '>', now())->count(),
                'total_orders'   => Order::count(),
                'paid_orders'    => Order::where('status', Order::STATUS_PAID)->count(),
                'total_revenue'  => '$' . number_format($totalRevenue / 100, 2),
                'total_users'    => User::where('role', 'user')->count(),
            ],
            'orders_by_status' => $ordersByStatus,
            'recent_orders'    => $recentOrders,
            'top_events'       => $topEvents,
        ]);
    }
}
