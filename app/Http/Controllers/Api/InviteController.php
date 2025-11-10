<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InviteUserRequest;
use App\Http\Resources\InviteResource;
use App\Jobs\SendEventInvitation;
use App\Models\Event;
use App\Models\Invite;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InviteController extends Controller
{
    /**
     * @OA\Get(
     *     path="/events/{event_id}/invites",
     *     tags={"Invites"},
     *     summary="List event invites",
     *     description="Get all invites for an event (event owner only)",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="event_id",
     *         in="path",
     *         required=true,
     *         description="Event ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of invites",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="invitee_email", type="string", example="invitee@example.com"),
     *                     @OA\Property(property="status", type="string", enum={"pending", "accepted", "rejected"}, example="pending"),
     *                     @OA\Property(property="user_id", type="integer", example=2),
     *                     @OA\Property(property="event_id", type="integer", example=1)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden - Not event owner"),
     *     @OA\Response(response=404, description="Event not found")
     * )
     */
    public function index(Request $request, Event $event): AnonymousResourceCollection
    {
        if ($event->calendar->user_id !== $request->user()->id) {
            abort(403);
        }

        $invites = $event->invites()
            ->with(['user', 'event'])
            ->get();

        return InviteResource::collection($invites);
    }

    /**
     * @OA\Get(
     *     path="/invites/me",
     *     tags={"Invites"},
     *     summary="List my invites",
     *     description="Get all event invites for the authenticated user",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of user's invites",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="invitee_email", type="string", example="me@example.com"),
     *                     @OA\Property(property="status", type="string", example="pending"),
     *                     @OA\Property(property="event", type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="Team Meeting"),
     *                         @OA\Property(property="start_time", type="string", format="datetime")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function myInvites(Request $request): AnonymousResourceCollection
    {
        $invites = $request->user()
            ->invites()
            ->with(['event.calendar'])
            ->orderBy('created_at', 'desc')
            ->get();

        return InviteResource::collection($invites);
    }

    /**
     * @OA\Post(
     *     path="/events/{event_id}/invites",
     *     tags={"Invites"},
     *     summary="Invite user to event",
     *     description="Send an invitation to a user for an event (event owner only). Queues an email notification.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="event_id",
     *         in="path",
     *         required=true,
     *         description="Event ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"invitee_email"},
     *             @OA\Property(property="invitee_email", type="string", format="email", example="colleague@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Invitation sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=3),
     *                 @OA\Property(property="invitee_email", type="string", example="colleague@example.com"),
     *                 @OA\Property(property="status", type="string", example="pending"),
     *                 @OA\Property(property="event", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(InviteUserRequest $request, Event $event): InviteResource
    {
        if ($event->calendar->user_id !== $request->user()->id) {
            abort(403);
        }

        // Check if user exists
        $user = User::where('email', $request->input('invitee_email'))->first();

        $invite = $event->invites()->create([
            'user_id' => $user?->id,
            'invitee_email' => $request->input('invitee_email'),
            'status' => 'pending',
        ]);

        // Queue the invitation email
        SendEventInvitation::dispatch($invite);

        return new InviteResource($invite->load(['user', 'event']));
    }

    /**
     * @OA\Post(
     *     path="/invites/{id}/accept",
     *     tags={"Invites"},
     *     summary="Accept invitation",
     *     description="Accept an event invitation. Only the invitee can accept.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Invite ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Invitation accepted",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="accepted")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden - Not your invite"),
     *     @OA\Response(response=404, description="Invite not found")
     * )
     */
    public function accept(Request $request, Invite $invite): InviteResource
    {
        if ($invite->user_id !== $request->user()->id) {
            abort(403);
        }

        $invite->accept();

        return new InviteResource($invite);
    }

    /**
     * @OA\Post(
     *     path="/invites/{id}/reject",
     *     tags={"Invites"},
     *     summary="Reject invitation",
     *     description="Reject an event invitation. Only the invitee can reject.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Invite ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Invitation rejected",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="rejected")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden - Not your invite"),
     *     @OA\Response(response=404, description="Invite not found")
     * )
     */
    public function reject(Request $request, Invite $invite): InviteResource
    {
        if ($invite->user_id !== $request->user()->id) {
            abort(403);
        }

        $invite->reject();

        return new InviteResource($invite);
    }

    /**
     * @OA\Delete(
     *     path="/invites/{id}",
     *     tags={"Invites"},
     *     summary="Delete invitation",
     *     description="Delete an event invitation (event owner only)",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Invite ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Invitation deleted successfully"
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden - Not event owner"),
     *     @OA\Response(response=404, description="Invite not found")
     * )
     */
    public function destroy(Request $request, Invite $invite): JsonResponse
    {
        if ($invite->event->calendar->user_id !== $request->user()->id) {
            abort(403);
        }

        $invite->delete();

        return response()->json(null, 204);
    }
}
