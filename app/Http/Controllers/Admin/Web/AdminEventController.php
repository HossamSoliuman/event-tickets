<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class AdminEventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::withCount('orders')->latest('date');
        if ($search = $request->query('search')) {
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('venue', 'like', "%{$search}%");
        }
        $events = $query->paginate(15)->withQueryString();
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'required|string',
            'venue'         => 'required|string|max:255',
            'date'          => 'required|date|after:now',
            'total_tickets' => 'required|integer|min:1',
            'price'         => 'required|numeric|min:0',  // input in dollars
            'image_url'     => 'nullable|url',
            'is_active'     => 'nullable|boolean',
        ]);

        Event::create([
            'title'             => $validated['title'],
            'description'       => $validated['description'],
            'venue'             => $validated['venue'],
            'date'              => $validated['date'],
            'total_tickets'     => $validated['total_tickets'],
            'available_tickets' => $validated['total_tickets'],
            'price'             => (int) round($validated['price'] * 100), // convert to cents
            'image_url'         => $validated['image_url'] ?? null,
            'is_active'         => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event created successfully.');
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'venue'       => 'required|string|max:255',
            'date'        => 'required|date',
            'price'       => 'required|numeric|min:0',
            'image_url'   => 'nullable|url',
            'is_active'   => 'nullable|boolean',
        ]);

        $event->update([
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'venue'       => $validated['venue'],
            'date'        => $validated['date'],
            'price'       => (int) round($validated['price'] * 100),
            'image_url'   => $validated['image_url'] ?? null,
            'is_active'   => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        if ($event->orders()->exists()) {
            $event->update(['is_active' => false]);
            return redirect()->route('admin.events.index')
                ->with('warning', 'Event has orders — it has been deactivated instead of deleted.');
        }

        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event deleted successfully.');
    }
}
