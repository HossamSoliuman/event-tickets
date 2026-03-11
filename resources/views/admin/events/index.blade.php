@extends('admin.layout')
@section('title', 'Events')
@section('heading', 'Events')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div></div>
    <a href="{{ route('admin.events.create') }}"
       class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        New Event
    </a>
</div>

{{-- Search --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
    <form method="GET" action="{{ route('admin.events.index') }}" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search by title or venue..."
               class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            Search
        </button>
        @if(request('search'))
        <a href="{{ route('admin.events.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition">
            Clear
        </a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">
                    <th class="px-6 py-3">Event</th>
                    <th class="px-6 py-3">Date</th>
                    <th class="px-6 py-3">Price</th>
                    <th class="px-6 py-3">Tickets</th>
                    <th class="px-6 py-3">Orders</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($events as $event)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if($event->image_url)
                            <img src="{{ $event->image_url }}" alt="{{ $event->title }}"
                                 class="w-10 h-10 object-cover rounded-lg flex-shrink-0">
                            @else
                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            @endif
                            <div>
                                <p class="font-medium text-gray-800 max-w-[200px] truncate">{{ $event->title }}</p>
                                <p class="text-xs text-gray-400 truncate max-w-[200px]">{{ $event->venue }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                        {{ $event->date->format('M j, Y') }}
                        <p class="text-xs text-gray-400">{{ $event->date->format('g:i A') }}</p>
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-800">
                        ${{ number_format($event->price / 100, 2) }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-gray-700">{{ $event->available_tickets }} / {{ $event->total_tickets }}</div>
                        @php $sold = $event->total_tickets - $event->available_tickets; $pct = $event->total_tickets > 0 ? round($sold / $event->total_tickets * 100) : 0; @endphp
                        <div class="w-20 bg-gray-100 rounded-full h-1.5 mt-1">
                            <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-700">{{ $event->orders_count }}</td>
                    <td class="px-6 py-4">
                        @if(!$event->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Inactive</span>
                        @elseif($event->date->isPast())
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Ended</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.events.edit', $event) }}"
                               class="text-indigo-600 hover:text-indigo-800 font-medium text-xs">Edit</a>

                            <form method="POST" action="{{ route('admin.events.destroy', $event) }}"
                                  onsubmit="return confirm('Delete this event?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 font-medium text-xs">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">No events found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($events->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $events->links() }}
    </div>
    @endif
</div>

@endsection
