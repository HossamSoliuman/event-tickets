@extends('admin.layout')
@section('title', 'Order #' . $order->id)
@section('heading', 'Order #' . $order->id)

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.orders.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to orders
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Main Info --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Order Summary --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-semibold text-gray-800">Order Summary</h2>
                @include('admin.partials.status-badge', ['status' => $order->status])
            </div>
            <div class="p-6 grid grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Order ID</p>
                    <p class="font-mono text-gray-800">#{{ $order->id }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Date</p>
                    <p class="text-gray-800">{{ $order->created_at->format('M j, Y \a\t H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Quantity</p>
                    <p class="text-gray-800">{{ $order->quantity }} ticket(s)</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Total Amount</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($order->total_amount / 100, 2) }}</p>
                </div>
            </div>
        </div>

        {{-- Payment Info --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800">Payment Details</h2>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Stripe Payment Intent</p>
                    <p class="font-mono text-sm text-gray-700 bg-gray-50 px-3 py-2 rounded-lg break-all">
                        {{ $order->stripe_payment_intent_id ?? '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Stripe Status</p>
                    <p class="text-gray-700">{{ $order->stripe_payment_status ?? '—' }}</p>
                </div>
                @if($order->stripe_payment_intent_id)
                <a href="https://dashboard.stripe.com/payments/{{ $order->stripe_payment_intent_id }}"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    View in Stripe Dashboard
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
                @endif
            </div>
        </div>

    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">

        {{-- Customer --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800">Customer</h2>
            </div>
            <div class="p-6">
                @if($order->user)
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm">
                        {{ strtoupper(substr($order->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">{{ $order->user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $order->user->email }}</p>
                    </div>
                </div>
                <div class="text-xs text-gray-400">User ID: #{{ $order->user->id }}</div>
                @else
                <p class="text-gray-400 text-sm">User not found.</p>
                @endif
            </div>
        </div>

        {{-- Event --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800">Event</h2>
            </div>
            <div class="p-6">
                @if($order->event)
                @if($order->event->image_url)
                    <img src="{{ $order->event->image_url }}" alt="{{ $order->event->title }}"
                         class="w-full h-28 object-cover rounded-lg mb-3">
                @endif
                <p class="font-semibold text-gray-800 mb-1">{{ $order->event->title }}</p>
                <p class="text-sm text-gray-500 mb-1">
                    <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    </svg>
                    {{ $order->event->venue }}
                </p>
                <p class="text-sm text-gray-500 mb-3">
                    <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ $order->event->date->format('M j, Y \a\t g:i A') }}
                </p>
                <a href="{{ route('admin.events.edit', $order->event) }}"
                   class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                    Edit event →
                </a>
                @else
                <p class="text-gray-400 text-sm">Event not found.</p>
                @endif
            </div>
        </div>

    </div>

</div>

@endsection
