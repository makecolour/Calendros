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
     * List invites for an event (event owner only)
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
     * List invites for the authenticated user
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
     * Invite a user to an event
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
     * Accept an invitation
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
     * Reject an invitation
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
     * Delete an invitation
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
