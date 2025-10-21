<?php

namespace Tests\Feature;

use App\Models\Calendar;
use App\Models\Event;
use App\Models\Invite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_regular_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
    }

    public function test_admin_dashboard_shows_correct_statistics(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        
        // Create test data
        $users = User::factory()->count(5)->create();
        $calendar = Calendar::factory()->create(['user_id' => $users[0]->id]);
        $events = Event::factory()->count(3)->create(['calendar_id' => $calendar->id]);
        Invite::factory()->count(2)->create([
            'event_id' => $events[0]->id,
            'status' => 'pending',
        ]);
        Invite::factory()->count(1)->create([
            'event_id' => $events[0]->id,
            'status' => 'accepted',
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        
        // Verify that stats are present with correct structure
        $response->assertViewHas('stats');
        $stats = $response->viewData('stats');
        
        // Assert that keys exist and values are numeric
        $this->assertArrayHasKey('total_users', $stats);
        $this->assertArrayHasKey('total_calendars', $stats);
        $this->assertArrayHasKey('total_events', $stats);
        $this->assertArrayHasKey('total_invites', $stats);
        $this->assertArrayHasKey('pending_invites', $stats);
        $this->assertArrayHasKey('accepted_invites', $stats);
        $this->assertArrayHasKey('rejected_invites', $stats);
        
        // Verify we have at least the data we created
        $this->assertGreaterThanOrEqual(6, $stats['total_users']);
        $this->assertGreaterThanOrEqual(1, $stats['total_calendars']);
        $this->assertGreaterThanOrEqual(3, $stats['total_events']);
        $this->assertGreaterThanOrEqual(3, $stats['total_invites']);
        $this->assertGreaterThanOrEqual(2, $stats['pending_invites']);
        $this->assertGreaterThanOrEqual(1, $stats['accepted_invites']);
    }

    public function test_admin_dashboard_shows_recent_users(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $recentUsers = User::factory()->count(5)->create();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('recentUsers', function ($users) {
            return $users->count() >= 5;
        });
    }

    public function test_admin_dashboard_shows_recent_events(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        $calendar = Calendar::factory()->create(['user_id' => $user->id]);
        $events = Event::factory()->count(3)->create(['calendar_id' => $calendar->id]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('recentEvents', function ($events) {
            return $events->count() >= 3;
        });
    }

    public function test_is_admin_helper_method(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['is_admin' => false]);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($user->isAdmin());
    }
}
