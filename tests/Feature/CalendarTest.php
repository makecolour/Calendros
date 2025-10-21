<?php

namespace Tests\Feature;

use App\Models\Calendar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticatedUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        return $user;
    }

    public function test_user_can_list_their_calendars(): void
    {
        $user = $this->authenticatedUser();
        $calendar1 = Calendar::factory()->create(['user_id' => $user->id, 'name' => 'Personal']);
        $calendar2 = Calendar::factory()->create(['user_id' => $user->id, 'name' => 'Work']);

        // Create calendar for another user
        $otherCalendar = Calendar::factory()->create(['name' => 'Other User Calendar']);

        $response = $this->getJson('/api/calendars');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['name' => 'Personal'])
            ->assertJsonFragment(['name' => 'Work'])
            ->assertJsonMissing(['name' => 'Other User Calendar']);
    }

    public function test_user_can_create_calendar(): void
    {
        $user = $this->authenticatedUser();

        $response = $this->postJson('/api/calendars', [
            'name' => 'My New Calendar',
            'description' => 'This is a test calendar',
            'color' => '#ff0000',
            'timezone' => 'America/New_York',
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'My New Calendar',
                'description' => 'This is a test calendar',
                'color' => '#ff0000',
            ]);

        $this->assertDatabaseHas('calendars', [
            'user_id' => $user->id,
            'name' => 'My New Calendar',
        ]);
    }

    public function test_user_can_view_their_calendar(): void
    {
        $user = $this->authenticatedUser();
        $calendar = Calendar::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/calendars/' . $calendar->id);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $calendar->id,
                'name' => $calendar->name,
            ]);
    }

    public function test_user_cannot_view_others_calendar(): void
    {
        $this->authenticatedUser();
        $otherCalendar = Calendar::factory()->create();

        $response = $this->getJson('/api/calendars/' . $otherCalendar->id);

        $response->assertStatus(403);
    }

    public function test_user_can_update_their_calendar(): void
    {
        $user = $this->authenticatedUser();
        $calendar = Calendar::factory()->create(['user_id' => $user->id]);

        $response = $this->putJson('/api/calendars/' . $calendar->id, [
            'name' => 'Updated Calendar Name',
            'description' => 'Updated description',
            'color' => '#00ff00',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Calendar Name']);

        $this->assertDatabaseHas('calendars', [
            'id' => $calendar->id,
            'name' => 'Updated Calendar Name',
        ]);
    }

    public function test_user_cannot_update_others_calendar(): void
    {
        $this->authenticatedUser();
        $otherCalendar = Calendar::factory()->create();

        $response = $this->putJson('/api/calendars/' . $otherCalendar->id, [
            'name' => 'Hacked Name',
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_their_calendar(): void
    {
        $user = $this->authenticatedUser();
        $calendar = Calendar::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson('/api/calendars/' . $calendar->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('calendars', [
            'id' => $calendar->id,
        ]);
    }

    public function test_user_cannot_delete_others_calendar(): void
    {
        $this->authenticatedUser();
        $otherCalendar = Calendar::factory()->create();

        $response = $this->deleteJson('/api/calendars/' . $otherCalendar->id);

        $response->assertStatus(403);

        $this->assertDatabaseHas('calendars', [
            'id' => $otherCalendar->id,
        ]);
    }

    public function test_calendar_validation_requires_name(): void
    {
        $this->authenticatedUser();

        $response = $this->postJson('/api/calendars', [
            'description' => 'Calendar without name',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    public function test_default_calendar_created_on_registration(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'newuser@example.com')->first();

        $this->assertDatabaseHas('calendars', [
            'user_id' => $user->id,
            'is_default' => true,
        ]);
    }
}
