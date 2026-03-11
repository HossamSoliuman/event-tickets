{{--
    Shared form fields for event create/edit.
    Include with: @include('admin.events._form', ['event' => $event ?? null])
--}}

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Title --}}
    <div class="lg:col-span-2">
        <label for="title" class="block text-sm font-medium text-gray-700 mb-1.5">Event Title *</label>
        <input type="text" id="title" name="title"
               value="{{ old('title', $event->title ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('title') border-red-400 @enderror"
               placeholder="e.g. Cairo Jazz Night">
        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Venue --}}
    <div>
        <label for="venue" class="block text-sm font-medium text-gray-700 mb-1.5">Venue *</label>
        <input type="text" id="venue" name="venue"
               value="{{ old('venue', $event->venue ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('venue') border-red-400 @enderror"
               placeholder="e.g. Cairo Opera House">
        @error('venue') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Date --}}
    <div>
        <label for="date" class="block text-sm font-medium text-gray-700 mb-1.5">Date & Time *</label>
        <input type="datetime-local" id="date" name="date"
               value="{{ old('date', isset($event) ? $event->date->format('Y-m-d\TH:i') : '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('date') border-red-400 @enderror">
        @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Price --}}
    <div>
        <label for="price" class="block text-sm font-medium text-gray-700 mb-1.5">Ticket Price (USD) *</label>
        <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
            <input type="number" id="price" name="price" step="0.01" min="0"
                   value="{{ old('price', isset($event) ? number_format($event->price / 100, 2) : '') }}"
                   class="w-full border border-gray-300 rounded-lg pl-7 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('price') border-red-400 @enderror"
                   placeholder="0.00">
        </div>
        @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Total Tickets (only on create) --}}
    @unless(isset($event))
    <div>
        <label for="total_tickets" class="block text-sm font-medium text-gray-700 mb-1.5">Total Tickets *</label>
        <input type="number" id="total_tickets" name="total_tickets" min="1"
               value="{{ old('total_tickets') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('total_tickets') border-red-400 @enderror"
               placeholder="e.g. 200">
        @error('total_tickets') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    @endunless

    {{-- Image URL --}}
    <div class="{{ isset($event) ? '' : 'lg:col-span-2' }} ">
        <label for="image_url" class="block text-sm font-medium text-gray-700 mb-1.5">Image URL</label>
        <input type="url" id="image_url" name="image_url"
               value="{{ old('image_url', $event->image_url ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('image_url') border-red-400 @enderror"
               placeholder="https://...">
        @error('image_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Description --}}
    <div class="lg:col-span-2">
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">Description *</label>
        <textarea id="description" name="description" rows="4"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('description') border-red-400 @enderror"
                  placeholder="Describe the event...">{{ old('description', $event->description ?? '') }}</textarea>
        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Active toggle --}}
    <div class="lg:col-span-2">
        <label class="inline-flex items-center gap-3 cursor-pointer">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1"
                   @checked(old('is_active', $event->is_active ?? true))
                   class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
            <span class="text-sm font-medium text-gray-700">Event is active & visible to users</span>
        </label>
    </div>

</div>
