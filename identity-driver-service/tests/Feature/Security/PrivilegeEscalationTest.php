<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class PrivilegeEscalationTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_cannot_perform_admin_actions(): void
    {
        // Arrange
        $adminUser = User::factory()->create(['role' => 'ADMIN']);
        $passengerUser = User::factory()->create(['role' => 'PASSENGER']);

        // Act
        // Tentando deletar a conta de outro usuário
        $responseAdmin = Gate::forUser($adminUser)->allows('delete', $passengerUser);
        $responsePassenger = Gate::forUser($passengerUser)->allows('delete', $adminUser);

        // Assert
        $this->assertTrue($responseAdmin, 'Admin should be able to delete users');
        $this->assertFalse($responsePassenger, 'Passenger should not be able to delete other users');
    }
}
