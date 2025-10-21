<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCalendarRequest;
use App\Http\Requests\UpdateCalendarRequest;
use App\Http\Resources\CalendarResource;
use App\Models\Calendar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CalendarController extends Controller
{
    /**
     * Display a listing of the user's calendars.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $calendars = $request->user()
            ->calendars()
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get();

        return CalendarResource::collection($calendars);
    }

    /**
     * Store a newly created calendar.
     */
    public function store(StoreCalendarRequest $request): CalendarResource
    {
        $calendar = $request->user()->calendars()->create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'color' => $request->input('color', '#4F46E5'),
            'timezone' => $request->input('timezone', config('app.timezone')),
            'is_default' => $request->input('is_default', false),
        ]);

        return new CalendarResource($calendar);
    }

    /**
     * Display the specified calendar.
     */
    public function show(Request $request, Calendar $calendar): CalendarResource
    {
        if ($calendar->user_id !== $request->user()->id) {
            abort(403);
        }
        
        return new CalendarResource($calendar);
    }

    /**
     * Update the specified calendar.
     */
    public function update(UpdateCalendarRequest $request, Calendar $calendar): CalendarResource
    {
        if ($calendar->user_id !== $request->user()->id) {
            abort(403);
        }
        
        $calendar->update($request->only([
            'name',
            'description',
            'color',
            'timezone',
            'is_default'
        ]));

        return new CalendarResource($calendar);
    }

    /**
     * Remove the specified calendar.
     */
    public function destroy(Request $request, Calendar $calendar): JsonResponse
    {
        if ($calendar->user_id !== $request->user()->id) {
            abort(403);
        }
        
        $calendar->delete();

        return response()->json(null, 204);
    }
}
