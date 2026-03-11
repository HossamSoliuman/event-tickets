@extends('admin.layout')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')

{{-- Stat Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

    {{-- Total Revenue --}}
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <p class="text-sm font-medium text-gray-500">Total Revenue</p>
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-900">${{ number_format($stats['total_revenue'] / 100, 2) }}</p>
        <p class="text-xs text-gray-400 mt-1">From paid orders</p>
    </div>

    {{-- Total Orders --}}
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <p class="text-sm font-medium text-gray-500">Total Orders</p>
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_orders'] }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $stats['paid_orders'] }} paid</p>
    </div>

    {{-- Active Events --}}
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <p class="text-sm font-medium text-gray-500">Active Events</p>
            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-900">{{ $stats['active_events'] }}</p>
        <p class="text-xs text-gray-400 mt-1">of {{ $stats['total_events'] }} total</p>
    </div>

    {{-- Users --}}
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <p class="text-sm font-medium text-gray-500">Registered Users</p>
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Customers</p>
    </div>

</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Recent Orders Table --}}
    <div class="xl:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">Recent Orders</h2>
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                View all →
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">
                        <th class="px-6 py-3">#</th>
                        <th class="px-6 py-3">Customer</th>
                        <th class="px-6 py-3">Event</th>
                        <th class="px-6 py-3">Amount</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentOrders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-3 text-gray-400">#{{ $order->id }}</td>
                        <td class="px-6 py-3">
                            <p class="font-medium text-gray-800">{{ $order->user?->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-400">{{ $order->user?->email }}</p>
                        </td>
                        <td class="px-6 py-3 text-gray-600 max-w-[160px] truncate">
                            {{ $order->event?->title ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-3 font-medium text-gray-800">
                            ${{ number_format($order->total_amount / 100, 2) }}
                        </td>
                        <td class="px-6 py-3">
                            @include('admin.partials.status-badge', ['status' => $order->status])
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">No orders yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Right column --}}
    <div class="space-y-6">

        {{-- Orders by Status --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-semibold text-gray-800 mb-4">Orders by Status</h2>
            <div class="space-y-3">
                @foreach(['paid' => ['green', 'Paid'], 'pending' => ['yellow', 'Pending'], 'failed' => ['red', 'Failed'], 'refunded' => ['gray', 'Refunded']] as $key => [$color, $label])
                @php $count = $ordersByStatus[$key] ?? 0; $total = $stats['total_orders'] ?: 1; @endphp
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">{{ $label }}</span>
                        <span class="font-medium text-gray-800">{{ $count }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-{{ $color }}-500 h-2 rounded-full transition-all"
                             style="width: {{ round($count / $total * 100) }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Top Events --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-semibold text-gray-800 mb-4">Top Events</h2>
            <div class="space-y-3">
                @forelse($topEvents as $i => $event)
                <div class="flex items-center gap-3">
                    <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold flex items-center justify-center flex-shrink-0">
                        {{ $i + 1 }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $event->title }}</p>
                        <p class="text-xs text-gray-400">{{ $event->paid_orders_count }} orders</p>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">
                        ${{ number_format(($event->total_revenue ?? 0) / 100, 2) }}
                    </span>
                </div>
                @empty
                <p class="text-sm text-gray-400">No sales yet.</p>
                @endforelse
            </div>
        </div>

    </div>
</div>

@endsection
