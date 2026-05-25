<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_register_a_new_user(): void
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'role' => 'PASSENGER',
        ];

        $response = $this->postJson('/api/users/register', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'role' => 'PASSENGER',
        ]);
        
        // Assert ID is a valid UUID
        $userId = $response->json('data.id');
        $this->assertTrue(Str::isUuid($userId));
    }

    public function test_fails_registration_if_email_already_exists(): void
    {
        $existingEmail = 'existing@example.com';
        
        User::create([
            'id' => (string) Str::uuid(),
            'name' => 'Existing User',
            'email' => $existingEmail,
            'password' => bcrypt('password'),
        ]);

        $payload = [
            'name' => 'Jane Doe',
            'email' => $existingEmail,
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/users/register', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_fails_registration_if_required_fields_are_missing(): void
    {
        $response = $this->postJson('/api/users/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }
}
