@extends('admin.layout')
@section('title', 'Edit Event')
@section('heading', 'Edit Event')

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.events.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to events
    </a>
</div>

<div class="max-w-3xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Event preview header --}}
        @if($event->image_url)
        <div class="h-32 w-full overflow-hidden relative">
            <img src="{{ $event->image_url }}" alt="{{ $event->title }}"
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/40"></div>
        </div>
        @endif

        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-800">Edit: {{ $event->title }}</h2>
            <div class="flex items-center gap-3 text-xs text-gray-400">
                <span>{{ $event->total_tickets - $event->available_tickets }} / {{ $event->total_tickets }} sold</span>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.events.update', $event) }}" class="p-6">
            @csrf
            @method('PUT')
            @include('admin.events._form', ['event' => $event])
            <div class="mt-6 flex items-center gap-3 pt-6 border-t border-gray-100">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition">
                    Save Changes
                </button>
                <a href="{{ route('admin.events.index') }}"
                   class="text-gray-500 hover:text-gray-800 px-4 py-2.5 text-sm transition">
                    Cancel
                </a>

                {{-- Danger zone --}}
                <div class="ml-auto">
                    <form method="POST" action="{{ route('admin.events.destroy', $event) }}"
                          onsubmit="return confirm('Are you sure you want to delete this event?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="text-red-500 hover:text-red-700 text-sm font-medium transition">
                            Delete Event
                        </button>
                    </form>
                </div>
            </div>
        </form>

    </div>
</div>

@endsection
