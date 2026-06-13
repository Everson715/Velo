<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TokenReuseTest extends TestCase
{
    use RefreshDatabase;

    public function test_token_is_invalidated_after_refresh(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        // Act - Call refresh endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/auth/refresh');

        $response->assertStatus(200);
        $newToken = $response->json('data.token');

        // Assert - Old token should no longer work
        $failedResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/auth/verify');

        $failedResponse->assertStatus(401);

        // New token should work
        $successResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $newToken,
        ])->getJson('/api/auth/verify');

        $successResponse->assertStatus(200);
    }
}
