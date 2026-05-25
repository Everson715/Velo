<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_user_profile_without_vehicles_if_passenger(): void
    {
        $user = User::create([
            'id' => (string) Str::uuid(),
            'name' => 'Passenger User',
            'email' => 'passenger@example.com',
            'password' => bcrypt('password123'),
            'role' => 'PASSENGER',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'role'
                ]
            ])
            ->assertJsonMissing(['vehicles']);
    }

    public function test_can_get_user_profile_with_vehicles_if_driver(): void
    {
        $user = User::create([
            'id' => (string) Str::uuid(),
            'name' => 'Driver User',
            'email' => 'driver@example.com',
            'password' => bcrypt('password123'),
            'role' => 'DRIVER',
        ]);

        Vehicle::create([
            'id' => (string) Str::uuid(),
            'plate' => 'ABC-1234',
            'model' => 'Sedan',
            'color' => 'Black',
            'userId' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'id',
                    'role',
                    'vehicles' => [
                        '*' => ['id', 'plate', 'model', 'color']
                    ]
                ]
            ]);
    }

    public function test_can_soft_delete_account(): void
    {
        $user = User::create([
            'id' => (string) Str::uuid(),
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson('/api/me/account');

        $response->assertStatus(200);

        // Verifica o soft delete usando os métodos nativos do framework
        $this->assertSoftDeleted('users', [
            'id' => $user->id,
            'email' => 'test@example.com',
        ]);
    }

    public function test_can_list_active_sessions(): void
    {
        $user = User::create([
            'id' => (string) Str::uuid(),
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Cria um token real associado ao usuário
        $token = $user->createToken('test-device')->plainTextToken;
        
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/me/sessions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => ['id', 'name', 'tokenable_id']
                ]
            ]);
    }

    public function test_can_revoke_specific_session(): void
    {
        $user = User::create([
            'id' => (string) Str::uuid(),
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $user->createToken('test-device-1');
        $token2 = $user->createToken('test-device-2');
        
        $tokenId = $token2->accessToken->id;

        Sanctum::actingAs($user);

        $response = $this->deleteJson('/api/me/sessions/' . $tokenId);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $tokenId,
        ]);
    }
}
