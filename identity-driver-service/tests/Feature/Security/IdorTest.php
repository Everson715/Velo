<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class IdorTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_update_other_users_profile(): void
    {
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Act
        // Emulando uma tentativa de bypass (neste caso a API atual usa o token do proprio user, 
        // mas vamos testar a policy diretamente para garantir que se a rota aceitasse um ID, falharia)
        $response = Gate::forUser($user1)->allows('update', $user2);

        // Assert
        $this->assertFalse($response);
    }

    public function test_user_can_update_own_profile(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = Gate::forUser($user)->allows('update', $user);

        // Assert
        $this->assertTrue($response);
    }
}
