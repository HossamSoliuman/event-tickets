@extends('admin.layout')
@section('title', 'Orders')
@section('heading', 'Orders')

@section('content')

{{-- Filters --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
    <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap gap-3 items-end">

        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-medium text-gray-500 mb-1">Search user</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Name or email..."
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div class="min-w-[140px]">
            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All statuses</option>
                @foreach(['paid', 'pending', 'failed', 'refunded'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>

        <div class="min-w-[160px]">
            <label class="block text-xs font-medium text-gray-500 mb-1">Event</label>
            <select name="event_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All events</option>
                @foreach($events as $event)
                    <option value="{{ $event->id }}" @selected(request('event_id') == $event->id)>{{ $event->title }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Date from</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Date to</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div class="flex gap-2">
            <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                Filter
            </button>
            <a href="{{ route('admin.orders.index') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition">
                Clear
            </a>
        </div>

    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <p class="text-sm text-gray-500">
            Showing <span class="font-medium text-gray-800">{{ $orders->firstItem() }}–{{ $orders->lastItem() }}</span>
            of <span class="font-medium text-gray-800">{{ $orders->total() }}</span> orders
        </p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">
                    <th class="px-6 py-3">Order</th>
                    <th class="px-6 py-3">Customer</th>
                    <th class="px-6 py-3">Event</th>
                    <th class="px-6 py-3">Qty</th>
                    <th class="px-6 py-3">Amount</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Date</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-gray-400 font-mono text-xs">#{{ $order->id }}</td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-800">{{ $order->user?->name ?? '—' }}</p>
                        <p class="text-xs text-gray-400">{{ $order->user?->email }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-gray-700 max-w-[180px] truncate">{{ $order->event?->title ?? '—' }}</p>
                        <p class="text-xs text-gray-400">{{ $order->event?->date->format('M j, Y') }}</p>
                    </td>
                    <td class="px-6 py-4 text-gray-700 text-center">{{ $order->quantity }}</td>
                    <td class="px-6 py-4 font-semibold text-gray-800">
                        ${{ number_format($order->total_amount / 100, 2) }}
                    </td>
                    <td class="px-6 py-4">
                        @include('admin.partials.status-badge', ['status' => $order->status])
                    </td>
                    <td class="px-6 py-4 text-gray-400 text-xs whitespace-nowrap">
                        {{ $order->created_at->format('M j, Y H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.orders.show', $order) }}"
                           class="text-indigo-600 hover:text-indigo-800 font-medium text-xs">
                            View →
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                        No orders found matching your filters.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($orders->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $orders->links() }}
    </div>
    @endif

</div>

@endsection
