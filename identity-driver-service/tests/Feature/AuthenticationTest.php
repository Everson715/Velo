<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_login_with_valid_credentials(): void
    {
        $password = 'password123';
        $user = User::create([
            'id' => (string) Str::uuid(),
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt($password),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'user' => ['id', 'name', 'email'],
                    'token',
                ],
            ]);
    }

    public function test_cannot_login_with_invalid_credentials(): void
    {
        $user = User::create([
            'id' => (string) Str::uuid(),
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        // A ValidationException lança status 422 nativamente no Laravel (similar ao erro 401 no quesito de rejeição)
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_can_logout_when_authenticated(): void
    {
        $user = User::create([
            'id' => (string) Str::uuid(),
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);
    }

    public function test_cannot_logout_without_authentication(): void
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(401);
    }
}
