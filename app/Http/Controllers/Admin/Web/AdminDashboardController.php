<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_events'   => Event::count(),
            'active_events'  => Event::where('is_active', true)->where('date', '>', now())->count(),
            'total_orders'   => Order::count(),
            'paid_orders'    => Order::where('status', Order::STATUS_PAID)->count(),
            'total_revenue'  => Order::where('status', Order::STATUS_PAID)->sum('total_amount'),
            'total_users'    => User::where('role', 'user')->count(),
        ];

        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $recentOrders = Order::with(['user', 'event'])
            ->latest()
            ->limit(8)
            ->get();

        $topEvents = Event::withCount(['orders as paid_orders_count' => fn ($q) => $q->where('status', Order::STATUS_PAID)])
            ->withSum(['orders as total_revenue' => fn ($q) => $q->where('status', Order::STATUS_PAID)], 'total_amount')
            ->orderByDesc('paid_orders_count')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'ordersByStatus', 'recentOrders', 'topEvents'));
    }
}
