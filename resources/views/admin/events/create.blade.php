@extends('admin.layout')
@section('title', 'Create Event')
@section('heading', 'Create New Event')

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
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">Event Details</h2>
        </div>
        <form method="POST" action="{{ route('admin.events.store') }}" class="p-6">
            @csrf
            @include('admin.events._form')
            <div class="mt-6 flex items-center gap-3 pt-6 border-t border-gray-100">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition">
                    Create Event
                </button>
                <a href="{{ route('admin.events.index') }}"
                   class="text-gray-500 hover:text-gray-800 px-4 py-2.5 text-sm transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
