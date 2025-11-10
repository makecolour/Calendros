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
     * @OA\Get(
     *     path="/calendars/{calendar_id}/events",
     *     tags={"Events"},
     *     summary="List events in a calendar",
     *     description="Get all events for a specific calendar with optional date range filtering",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="calendar_id",
     *         in="path",
     *         required=true,
     *         description="Calendar ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="filter[start_time][gte]",
     *         in="query",
     *         description="Filter events starting from this date (ISO 8601 format)",
     *         @OA\Schema(type="string", format="datetime", example="2025-11-01T00:00:00Z")
     *     ),
     *     @OA\Parameter(
     *         name="filter[end_time][lte]",
     *         in="query",
     *         description="Filter events ending before this date (ISO 8601 format)",
     *         @OA\Schema(type="string", format="datetime", example="2025-11-30T23:59:59Z")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of events",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Team Meeting"),
     *                     @OA\Property(property="description", type="string", example="Weekly sync"),
     *                     @OA\Property(property="start_time", type="string", format="datetime", example="2025-11-15T10:00:00Z"),
     *                     @OA\Property(property="end_time", type="string", format="datetime", example="2025-11-15T11:00:00Z"),
     *                     @OA\Property(property="location", type="string", example="Conference Room A"),
     *                     @OA\Property(property="is_all_day", type="boolean", example=false),
     *                     @OA\Property(property="calendar_id", type="integer", example=1)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Calendar not found")
     * )
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
     * @OA\Post(
     *     path="/calendars/{calendar_id}/events",
     *     tags={"Events"},
     *     summary="Create a new event",
     *     description="Create a new event in a specific calendar",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="calendar_id",
     *         in="path",
     *         required=true,
     *         description="Calendar ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","start_time","end_time"},
     *             @OA\Property(property="title", type="string", example="Product Launch"),
     *             @OA\Property(property="description", type="string", example="Launch event details"),
     *             @OA\Property(property="start_time", type="string", format="datetime", example="2025-12-01T09:00:00Z"),
     *             @OA\Property(property="end_time", type="string", format="datetime", example="2025-12-01T17:00:00Z"),
     *             @OA\Property(property="location", type="string", example="Main Auditorium"),
     *             @OA\Property(property="is_all_day", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Event created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=5),
     *                 @OA\Property(property="title", type="string", example="Product Launch"),
     *                 @OA\Property(property="calendar_id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=422, description="Validation error")
     * )
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
     * @OA\Get(
     *     path="/events/{id}",
     *     tags={"Events"},
     *     summary="Get event details",
     *     description="Retrieve details of a specific event including calendar and invites",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Event ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Event details",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Team Meeting"),
     *                 @OA\Property(property="description", type="string", example="Weekly sync"),
     *                 @OA\Property(property="start_time", type="string", format="datetime"),
     *                 @OA\Property(property="end_time", type="string", format="datetime"),
     *                 @OA\Property(property="location", type="string", example="Conference Room A"),
     *                 @OA\Property(property="is_all_day", type="boolean", example=false),
     *                 @OA\Property(property="calendar", type="object"),
     *                 @OA\Property(property="invites", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Event not found")
     * )
     */
    public function show(Request $request, Event $event): EventResource
    {
        if ($event->calendar->user_id !== $request->user()->id) {
            abort(403);
        }
        
        return new EventResource($event->load(['calendar', 'invites']));
    }

    /**
     * @OA\Put(
     *     path="/events/{id}",
     *     tags={"Events"},
     *     summary="Update event",
     *     description="Update an existing event",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Event ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Updated Meeting"),
     *             @OA\Property(property="description", type="string", example="Updated description"),
     *             @OA\Property(property="start_time", type="string", format="datetime", example="2025-11-16T10:00:00Z"),
     *             @OA\Property(property="end_time", type="string", format="datetime", example="2025-11-16T11:30:00Z"),
     *             @OA\Property(property="location", type="string", example="Room B"),
     *             @OA\Property(property="is_all_day", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Event updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Updated Meeting")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Event not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
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
     * @OA\Delete(
     *     path="/events/{id}",
     *     tags={"Events"},
     *     summary="Delete event",
     *     description="Delete an event. All associated invites will also be deleted.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Event ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Event deleted successfully"
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Event not found")
     * )
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
