@php
    $map = [
        'paid'     => 'bg-green-100 text-green-700',
        'pending'  => 'bg-yellow-100 text-yellow-700',
        'failed'   => 'bg-red-100 text-red-700',
        'refunded' => 'bg-gray-100 text-gray-600',
    ];
    $classes = $map[$status] ?? 'bg-gray-100 text-gray-600';
@endphp
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $classes }}">
    {{ ucfirst($status) }}
</span>
