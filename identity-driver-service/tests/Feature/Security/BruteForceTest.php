<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class BruteForceTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_endpoint_has_rate_limiting(): void
    {
        // Arrange
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        // Act & Assert
        // A taxa de login permitida é de 5 por minuto. Vamos exceder isso.
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/auth/login', [
                'email' => $user->email,
                'password' => 'wrongpassword',
            ]);
            $response->assertStatus(422); // Validation error
        }

        // A 6ª tentativa deve ser bloqueada
        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(429); // Too Many Requests
    }
}
