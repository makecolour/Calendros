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
     * @OA\Get(
     *     path="/calendars",
     *     tags={"Calendars"},
     *     summary="List user's calendars",
     *     description="Get all calendars owned by the authenticated user, ordered by default status and name",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of calendars",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Personal Calendar"),
     *                     @OA\Property(property="description", type="string", example="My personal events"),
     *                     @OA\Property(property="color", type="string", example="#4F46E5"),
     *                     @OA\Property(property="timezone", type="string", example="UTC"),
     *                     @OA\Property(property="is_default", type="boolean", example=true),
     *                     @OA\Property(property="user_id", type="integer", example=1)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
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
     * @OA\Post(
     *     path="/calendars",
     *     tags={"Calendars"},
     *     summary="Create a new calendar",
     *     description="Create a new calendar for the authenticated user",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Work Calendar"),
     *             @OA\Property(property="description", type="string", example="Work-related events"),
     *             @OA\Property(property="color", type="string", example="#10B981"),
     *             @OA\Property(property="timezone", type="string", example="America/New_York"),
     *             @OA\Property(property="is_default", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Calendar created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=2),
     *                 @OA\Property(property="name", type="string", example="Work Calendar"),
     *                 @OA\Property(property="description", type="string", example="Work-related events"),
     *                 @OA\Property(property="color", type="string", example="#10B981"),
     *                 @OA\Property(property="timezone", type="string", example="America/New_York"),
     *                 @OA\Property(property="is_default", type="boolean", example=false),
     *                 @OA\Property(property="user_id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=422, description="Validation error")
     * )
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
     * @OA\Get(
     *     path="/calendars/{id}",
     *     tags={"Calendars"},
     *     summary="Get calendar details",
     *     description="Retrieve details of a specific calendar (must be owned by authenticated user)",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Calendar ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Calendar details",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Personal Calendar"),
     *                 @OA\Property(property="description", type="string", example="My personal events"),
     *                 @OA\Property(property="color", type="string", example="#4F46E5"),
     *                 @OA\Property(property="timezone", type="string", example="UTC"),
     *                 @OA\Property(property="is_default", type="boolean", example=true),
     *                 @OA\Property(property="user_id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden - Not your calendar"),
     *     @OA\Response(response=404, description="Calendar not found")
     * )
     */
    public function show(Request $request, Calendar $calendar): CalendarResource
    {
        if ($calendar->user_id !== $request->user()->id) {
            abort(403);
        }
        
        return new CalendarResource($calendar);
    }

    /**
     * @OA\Put(
     *     path="/calendars/{id}",
     *     tags={"Calendars"},
     *     summary="Update calendar",
     *     description="Update an existing calendar (must be owned by authenticated user)",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Calendar ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated Calendar"),
     *             @OA\Property(property="description", type="string", example="Updated description"),
     *             @OA\Property(property="color", type="string", example="#EF4444"),
     *             @OA\Property(property="timezone", type="string", example="Europe/London"),
     *             @OA\Property(property="is_default", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Calendar updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Updated Calendar")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Calendar not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
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
     * @OA\Delete(
     *     path="/calendars/{id}",
     *     tags={"Calendars"},
     *     summary="Delete calendar",
     *     description="Delete a calendar (must be owned by authenticated user). All associated events will also be deleted.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Calendar ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Calendar deleted successfully"
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Calendar not found")
     * )
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
