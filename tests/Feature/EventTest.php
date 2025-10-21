<?php

namespace Tests\Feature;

use App\Models\Calendar;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticatedUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        return $user;
    }

    public function test_user_can_list_events_in_their_calendar(): void
    {
        $user = $this->authenticatedUser();
        $calendar = Calendar::factory()->create(['user_id' => $user->id]);
        
        Event::factory()->count(3)->create(['calendar_id' => $calendar->id]);

        // Create event in another user's calendar
        $otherCalendar = Calendar::factory()->create();
        Event::factory()->create(['calendar_id' => $otherCalendar->id]);

        $response = $this->getJson("/api/calendars/{$calendar->id}/events");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_user_can_create_event_in_their_calendar(): void
    {
        $user = $this->authenticatedUser();
        $calendar = Calendar::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson("/api/calendars/{$calendar->id}/events", [
            'title' => 'Team Meeting',
            'description' => 'Weekly sync',
            'start_time' => '2025-10-25 10:00:00',
            'end_time' => '2025-10-25 11:00:00',
            'location' => 'Conference Room A',
            'is_all_day' => false,
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'Team Meeting']);

        $this->assertDatabaseHas('events', [
            'calendar_id' => $calendar->id,
            'title' => 'Team Meeting',
        ]);
    }

    public function test_user_cannot_create_event_in_others_calendar(): void
    {
        $this->authenticatedUser();
        $otherCalendar = Calendar::factory()->create();

        $response = $this->postJson("/api/calendars/{$otherCalendar->id}/events", [
            'title' => 'Unauthorized Event',
            'start_time' => '2025-10-25 10:00:00',
            'end_time' => '2025-10-25 11:00:00',
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_view_event_in_their_calendar(): void
    {
        $user = $this->authenticatedUser();
        $calendar = Calendar::factory()->create(['user_id' => $user->id]);
        $event = Event::factory()->create(['calendar_id' => $calendar->id]);

        $response = $this->getJson("/api/events/{$event->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $event->id]);
    }

    public function test_user_cannot_view_event_in_others_calendar(): void
    {
        $this->authenticatedUser();
        $otherCalendar = Calendar::factory()->create();
        $otherEvent = Event::factory()->create(['calendar_id' => $otherCalendar->id]);

        $response = $this->getJson("/api/events/{$otherEvent->id}");

        $response->assertStatus(403);
    }

    public function test_user_can_update_event_in_their_calendar(): void
    {
        $user = $this->authenticatedUser();
        $calendar = Calendar::factory()->create(['user_id' => $user->id]);
        $event = Event::factory()->create(['calendar_id' => $calendar->id]);

        $response = $this->putJson("/api/events/{$event->id}", [
            'title' => 'Updated Event Title',
            'start_time' => '2025-10-26 10:00:00',
            'end_time' => '2025-10-26 11:00:00',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Updated Event Title']);

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => 'Updated Event Title',
        ]);
    }

    public function test_user_cannot_update_event_in_others_calendar(): void
    {
        $this->authenticatedUser();
        $otherCalendar = Calendar::factory()->create();
        $otherEvent = Event::factory()->create(['calendar_id' => $otherCalendar->id]);

        $response = $this->putJson("/api/events/{$otherEvent->id}", [
            'title' => 'Hacked Title',
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_event_from_their_calendar(): void
    {
        $user = $this->authenticatedUser();
        $calendar = Calendar::factory()->create(['user_id' => $user->id]);
        $event = Event::factory()->create(['calendar_id' => $calendar->id]);

        $response = $this->deleteJson("/api/events/{$event->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('events', [
            'id' => $event->id,
        ]);
    }

    public function test_user_cannot_delete_event_from_others_calendar(): void
    {
        $this->authenticatedUser();
        $otherCalendar = Calendar::factory()->create();
        $otherEvent = Event::factory()->create(['calendar_id' => $otherCalendar->id]);

        $response = $this->deleteJson("/api/events/{$otherEvent->id}");

        $response->assertStatus(403);
    }

    public function test_can_filter_events_by_date_range(): void
    {
        $user = $this->authenticatedUser();
        $calendar = Calendar::factory()->create(['user_id' => $user->id]);

        // Create events at different times
        Event::factory()->create([
            'calendar_id' => $calendar->id,
            'title' => 'Past Event',
            'start_time' => '2025-10-01 10:00:00',
            'end_time' => '2025-10-01 11:00:00',
        ]);

        Event::factory()->create([
            'calendar_id' => $calendar->id,
            'title' => 'Current Event',
            'start_time' => '2025-10-25 10:00:00',
            'end_time' => '2025-10-25 11:00:00',
        ]);

        Event::factory()->create([
            'calendar_id' => $calendar->id,
            'title' => 'Future Event',
            'start_time' => '2025-11-01 10:00:00',
            'end_time' => '2025-11-01 11:00:00',
        ]);

        $response = $this->getJson("/api/calendars/{$calendar->id}/events?" . http_build_query([
            'filter[start_time][gte]' => '2025-10-20',
            'filter[end_time][lte]' => '2025-10-31',
        ]));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['title' => 'Current Event']);
    }

    public function test_event_validation_requires_title_and_times(): void
    {
        $user = $this->authenticatedUser();
        $calendar = Calendar::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson("/api/calendars/{$calendar->id}/events", [
            'description' => 'Event without required fields',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'start_time', 'end_time']);
    }
}
