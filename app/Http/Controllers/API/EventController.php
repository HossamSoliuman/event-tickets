<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    use ApiResponse;
    /**
     * GET /api/events
     * List all upcoming, active events.
     * Supports optional search and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Event::upcoming()->available();

        // Optional keyword search
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('venue', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $events = $query->paginate($request->query('per_page', 12));

        return $this->apiResponse([
            'events'     => $events->map(fn($e) => $this->formatEvent($e)),
            'pagination' => [
                'total'        => $events->total(),
                'per_page'     => $events->perPage(),
                'current_page' => $events->currentPage(),
                'last_page'    => $events->lastPage(),
            ],
        ]);
    }

    /**
     * GET /api/events/{id}
     * Get full details for a single event.
     */
    public function show(int $id): JsonResponse
    {
        $event = Event::findOrFail($id);
        return $this->apiResponse(['event' => $this->formatEvent($event, detailed: true)]);
    }

    private function formatEvent(Event $event, bool $detailed = false): array
    {
        $data = [
            'id'                 => $event->id,
            'title'              => $event->title,
            'venue'              => $event->venue,
            'date'               => $event->date->toIso8601String(),
            'price'              => $event->price,            
            'price_display'      => '$' . number_format($event->price_in_dollars, 2),
            'available_tickets'  => $event->available_tickets,
            'total_tickets'      => $event->total_tickets,
            'is_sold_out'        => $event->isSoldOut(),
            'image_url'          => $event->image_url,
        ];

        if ($detailed) {
            $data['description'] = $event->description;
        }

        return $data;
    }
}
