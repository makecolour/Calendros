<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SingleDeviceLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_logging_in_revokes_all_previous_tokens(): void
    {
        // Create a user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // First login - simulate device 1
        $response1 = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response1->assertStatus(200);
        $token1 = $response1->json('token');

        // Verify device 1 can access protected routes
        $this->withToken($token1)
            ->getJson('/api/me')
            ->assertStatus(200);

        // Verify user has 1 token after first login
        $this->assertEquals(1, $user->fresh()->tokens()->count());

        // Second login - simulate device 2
        $response2 = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response2->assertStatus(200);
        $token2 = $response2->json('token');

        // Verify tokens are different
        $this->assertNotEquals($token1, $token2);

        // Verify device 2 can access protected routes
        $this->withToken($token2)
            ->getJson('/api/me')
            ->assertStatus(200);

        // Critical assertion: User should STILL have only 1 token (old deleted, new created)
        $this->assertEquals(1, $user->fresh()->tokens()->count());

        // Verify the first token ID no longer exists in database
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'id' => 1, // First token created should have ID 1
        ]);

        // Verify second token exists
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'id' => 2, // Second token should have ID 2
        ]);
    }

    public function test_multiple_logins_only_keep_latest_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $tokens = [];

        // Simulate 5 different devices/apps logging in
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/login', [
                'email' => 'test@example.com',
                'password' => 'password123',
            ]);

            $response->assertStatus(200);
            $tokens[] = $response->json('token');

            // After EACH login, verify only 1 token exists (critical assertion)
            $this->assertEquals(1, $user->fresh()->tokens()->count(),
                "After login #{$i}, user should have exactly 1 token");
        }

        // Only the last token should be valid
        $latestToken = end($tokens);
        
        $this->withToken($latestToken)
            ->getJson('/api/me')
            ->assertStatus(200);

        // Final verification: only 1 token in database
        $this->assertEquals(1, $user->fresh()->tokens()->count());

        // Verify only the latest token ID exists (should be ID 5)
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'id' => 5, // After 5 logins, only the 5th token should remain
        ]);

        // Verify earlier tokens were deleted
        for ($id = 1; $id <= 4; $id++) {
            $this->assertDatabaseMissing('personal_access_tokens', [
                'tokenable_id' => $user->id,
                'id' => $id,
            ]);
        }
    }
}
