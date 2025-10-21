<?php

namespace App\Policies;

use App\Models\Invite;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InvitePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Invite $invite): bool
    {
        // User can view if they're the invitee or the event owner
        return $invite->user_id === $user->id
            || $invite->event->calendar->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Invite $invite): bool
    {
        // Only the invitee can update (accept/reject) their invite
        return $invite->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Invite $invite): bool
    {
        // Event owner can delete invites
        return $invite->event->calendar->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Invite $invite): bool
    {
        return $invite->event->calendar->user_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Invite $invite): bool
    {
        return $invite->event->calendar->user_id === $user->id;
    }
}
