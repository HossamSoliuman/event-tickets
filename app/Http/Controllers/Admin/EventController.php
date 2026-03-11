<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * GET /api/admin/events
     * List all events (including past and inactive).
     */
    public function index(Request $request): JsonResponse
    {
        $events = Event::latest('date')->paginate($request->query('per_page', 20));

        return response()->json([
            'data' => $events->map(fn ($e) => $this->formatEvent($e)),
            'pagination' => [
                'total'        => $events->total(),
                'current_page' => $events->currentPage(),
                'last_page'    => $events->lastPage(),
            ],
        ]);
    }

    /**
     * POST /api/admin/events
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'venue'          => 'required|string|max:255',
            'date'           => 'required|date|after:now',
            'total_tickets'  => 'required|integer|min:1',
            'price'          => 'required|integer|min:0', 
            'image_url'      => 'nullable|url',
            'is_active'      => 'boolean',
        ]);

        $event = Event::create([
            ...$validated,
            'available_tickets' => $validated['total_tickets'],
            'is_active'         => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'message' => 'Event created successfully.',
            'data'    => $this->formatEvent($event),
        ], 201);
    }

    /**
     * GET /api/admin/events/{id}
     */
    public function show(int $id): JsonResponse
    {
        $event = Event::withCount('orders')->findOrFail($id);

        return response()->json([
            'data' => $this->formatEvent($event, detailed: true),
        ]);
    }

    /**
     * PUT /api/admin/events/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $event = Event::findOrFail($id);

        $validated = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'venue'       => 'sometimes|string|max:255',
            'date'        => 'sometimes|date',
            'price'       => 'sometimes|integer|min:0',
            'image_url'   => 'nullable|url',
            'is_active'   => 'sometimes|boolean',
        ]);

        $event->update($validated);

        return response()->json([
            'message' => 'Event updated successfully.',
            'data'    => $this->formatEvent($event->fresh()),
        ]);
    }

    /**
     * DELETE /api/admin/events/{id}
     * Soft-deletes by deactivating (cannot delete if orders exist).
     */
    public function destroy(int $id): JsonResponse
    {
        $event = Event::findOrFail($id);

        if ($event->orders()->exists()) {
            $event->update(['is_active' => false]);
            return response()->json(['message' => 'Event deactivated (orders exist, cannot hard delete).']);
        }

        $event->delete();
        return response()->json(['message' => 'Event deleted successfully.']);
    }

    private function formatEvent(Event $event, bool $detailed = false): array
    {
        $data = [
            'id'                => $event->id,
            'title'             => $event->title,
            'venue'             => $event->venue,
            'date'              => $event->date->toIso8601String(),
            'price'             => $event->price,
            'price_display'     => '$' . number_format($event->price_in_dollars, 2),
            'total_tickets'     => $event->total_tickets,
            'available_tickets' => $event->available_tickets,
            'tickets_sold'      => $event->total_tickets - $event->available_tickets,
            'is_active'         => $event->is_active,
            'image_url'         => $event->image_url,
            'created_at'        => $event->created_at->toIso8601String(),
        ];

        if ($detailed) {
            $data['description']   = $event->description;
            $data['orders_count']  = $event->orders_count ?? 0;
        }

        return $data;
    }
}
