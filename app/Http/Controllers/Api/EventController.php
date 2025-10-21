<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Models\Calendar;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class EventController extends Controller
{
    /**
     * Display a listing of events in the calendar.
     */
    public function index(Request $request, Calendar $calendar): AnonymousResourceCollection
    {
        if ($calendar->user_id !== $request->user()->id) {
            abort(403);
        }

        $query = $calendar->events();
        
        // Apply date range filters if provided
        if ($request->has('filter.start_time.gte')) {
            $query->where('start_time', '>=', $request->input('filter.start_time.gte'));
        }
        if ($request->has('filter.end_time.lte')) {
            $query->where('end_time', '<=', $request->input('filter.end_time.lte'));
        }
        
        $events = $query->orderBy('start_time')->get();

        return EventResource::collection($events);
    }

    /**
     * Store a newly created event.
     */
    public function store(StoreEventRequest $request, Calendar $calendar): EventResource
    {
        if ($calendar->user_id !== $request->user()->id) {
            abort(403);
        }

        $event = $calendar->events()->create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'location' => $request->input('location'),
            'is_all_day' => $request->input('is_all_day', false),
        ]);

        return new EventResource($event);
    }

    /**
     * Display the specified event.
     */
    public function show(Request $request, Event $event): EventResource
    {
        if ($event->calendar->user_id !== $request->user()->id) {
            abort(403);
        }
        
        return new EventResource($event->load(['calendar', 'invites']));
    }

    /**
     * Update the specified event.
     */
    public function update(UpdateEventRequest $request, Event $event): EventResource
    {
        if ($event->calendar->user_id !== $request->user()->id) {
            abort(403);
        }
        
        $event->update($request->only([
            'title',
            'description',
            'start_time',
            'end_time',
            'location',
            'is_all_day'
        ]));

        return new EventResource($event);
    }

    /**
     * Remove the specified event.
     */
    public function destroy(Request $request, Event $event): JsonResponse
    {
        if ($event->calendar->user_id !== $request->user()->id) {
            abort(403);
        }
        
        $event->delete();

        return response()->json(null, 204);
    }
}
