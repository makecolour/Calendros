<?php

namespace Tests\Feature;

use App\Jobs\SendEventInvitation;
use App\Models\Calendar;
use App\Models\Event;
use App\Models\Invite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class InviteTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticatedUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        return $user;
    }

    public function test_user_can_invite_registered_user_to_event(): void
    {
        Queue::fake();

        $user = $this->authenticatedUser();
        $calendar = Calendar::factory()->create(['user_id' => $user->id]);
        $event = Event::factory()->create(['calendar_id' => $calendar->id]);
        $invitee = User::factory()->create(['email' => 'invitee@example.com']);

        $response = $this->postJson("/api/events/{$event->id}/invite", [
            'invitee_email' => 'invitee@example.com',
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'invitee_email' => 'invitee@example.com',
                'status' => 'pending',
            ]);

        $this->assertDatabaseHas('invites', [
            'event_id' => $event->id,
            'user_id' => $invitee->id,
            'invitee_email' => 'invitee@example.com',
            'status' => 'pending',
        ]);

        // Verify email job was queued
        Queue::assertPushed(SendEventInvitation::class);
    }

    public function test_user_can_invite_unregistered_user_by_email(): void
    {
        Queue::fake();

        $user = $this->authenticatedUser();
        $calendar = Calendar::factory()->create(['user_id' => $user->id]);
        $event = Event::factory()->create(['calendar_id' => $calendar->id]);

        $response = $this->postJson("/api/events/{$event->id}/invite", [
            'invitee_email' => 'newuser@example.com',
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'invitee_email' => 'newuser@example.com',
                'status' => 'pending',
            ]);

        $this->assertDatabaseHas('invites', [
            'event_id' => $event->id,
            'user_id' => null, // Unregistered user
            'invitee_email' => 'newuser@example.com',
            'status' => 'pending',
        ]);

        // Verify email job was queued
        Queue::assertPushed(SendEventInvitation::class);
    }

    /**
     * CRITICAL TEST: Verify invitation email is queued, NOT sent synchronously
     */
    public function test_invitation_email_is_queued_not_sent_synchronously(): void
    {
        Mail::fake();
        Queue::fake();

        $user = $this->authenticatedUser();
        $calendar = Calendar::factory()->create(['user_id' => $user->id]);
        $event = Event::factory()->create(['calendar_id' => $calendar->id]);

        $response = $this->postJson("/api/events/{$event->id}/invite", [
            'invitee_email' => 'guest@example.com',
        ]);

        $response->assertStatus(201);

        // Assert NO emails were sent immediately (synchronously)
        Mail::assertNothingSent();

        // Assert email job WAS queued for background processing
        Queue::assertPushed(SendEventInvitation::class, function ($job) use ($event) {
            return $job->invite->event_id === $event->id;
        });
    }

    public function test_cannot_invite_same_email_twice_to_event(): void
    {
        $user = $this->authenticatedUser();
        $calendar = Calendar::factory()->create(['user_id' => $user->id]);
        $event = Event::factory()->create(['calendar_id' => $calendar->id]);

        // First invite
        Invite::factory()->create([
            'event_id' => $event->id,
            'invitee_email' => 'duplicate@example.com',
        ]);

        // Try to invite again
        $response = $this->postJson("/api/events/{$event->id}/invite", [
            'invitee_email' => 'duplicate@example.com',
        ]);

        $response->assertStatus(422);
    }

    public function test_user_cannot_invite_to_others_event(): void
    {
        $this->authenticatedUser();
        $otherCalendar = Calendar::factory()->create();
        $otherEvent = Event::factory()->create(['calendar_id' => $otherCalendar->id]);

        $response = $this->postJson("/api/events/{$otherEvent->id}/invite", [
            'invitee_email' => 'someone@example.com',
        ]);

        $response->assertStatus(403);
    }

    public function test_invited_user_can_accept_invite(): void
    {
        $organizer = User::factory()->create();
        $calendar = Calendar::factory()->create(['user_id' => $organizer->id]);
        $event = Event::factory()->create(['calendar_id' => $calendar->id]);

        $invitee = $this->authenticatedUser();
        $invite = Invite::factory()->create([
            'event_id' => $event->id,
            'user_id' => $invitee->id,
            'invitee_email' => $invitee->email,
            'status' => 'pending',
        ]);

        $response = $this->putJson("/api/invites/{$invite->id}/accept");

        $response->assertStatus(200)
            ->assertJsonFragment(['status' => 'accepted']);

        $this->assertDatabaseHas('invites', [
            'id' => $invite->id,
            'status' => 'accepted',
        ]);
    }

    public function test_invited_user_can_reject_invite(): void
    {
        $organizer = User::factory()->create();
        $calendar = Calendar::factory()->create(['user_id' => $organizer->id]);
        $event = Event::factory()->create(['calendar_id' => $calendar->id]);

        $invitee = $this->authenticatedUser();
        $invite = Invite::factory()->create([
            'event_id' => $event->id,
            'user_id' => $invitee->id,
            'invitee_email' => $invitee->email,
            'status' => 'pending',
        ]);

        $response = $this->putJson("/api/invites/{$invite->id}/reject");

        $response->assertStatus(200)
            ->assertJsonFragment(['status' => 'rejected']);

        $this->assertDatabaseHas('invites', [
            'id' => $invite->id,
            'status' => 'rejected',
        ]);
    }

    public function test_user_can_list_their_invites(): void
    {
        $invitee = $this->authenticatedUser();

        // Create invites for this user
        $invite1 = Invite::factory()->create([
            'user_id' => $invitee->id,
            'invitee_email' => $invitee->email,
        ]);
        $invite2 = Invite::factory()->create([
            'user_id' => $invitee->id,
            'invitee_email' => $invitee->email,
        ]);

        // Create invite for another user
        Invite::factory()->create();

        $response = $this->getJson('/api/invites');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_user_can_list_invites_for_their_event(): void
    {
        $user = $this->authenticatedUser();
        $calendar = Calendar::factory()->create(['user_id' => $user->id]);
        $event = Event::factory()->create(['calendar_id' => $calendar->id]);

        Invite::factory()->count(3)->create(['event_id' => $event->id]);

        // Create invite for another event
        Invite::factory()->create();

        $response = $this->getJson("/api/events/{$event->id}/invites");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_guest_cannot_accept_invite_without_auth(): void
    {
        $invite = Invite::factory()->create();

        $response = $this->putJson("/api/invites/{$invite->id}/accept");

        $response->assertStatus(401);
    }

    public function test_user_cannot_accept_others_invite(): void
    {
        $this->authenticatedUser();
        
        $otherUser = User::factory()->create();
        $invite = Invite::factory()->create([
            'user_id' => $otherUser->id,
            'invitee_email' => $otherUser->email,
        ]);

        $response = $this->putJson("/api/invites/{$invite->id}/accept");

        $response->assertStatus(403);
    }

    public function test_invite_validation_requires_email(): void
    {
        $user = $this->authenticatedUser();
        $calendar = Calendar::factory()->create(['user_id' => $user->id]);
        $event = Event::factory()->create(['calendar_id' => $calendar->id]);

        $response = $this->postJson("/api/events/{$event->id}/invite", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('invitee_email');
    }
}
