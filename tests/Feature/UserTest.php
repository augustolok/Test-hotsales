<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private function createUser($role)
    {
        return User::factory()->create(['role' => $role]);
    }

    public function test_admin_can_create_user()
    {
        $admin = $this->createUser('admin');

        $response = $this->actingAs($admin)->postJson('/api/users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password',
            'role' => 'gestor',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com', 'role' => 'gestor']);
    }

    public function test_gestor_cannot_create_user()
    {
        $gestor = $this->createUser('gestor');

        $response = $this->actingAs($gestor)->postJson('/api/users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password',
            'role' => 'operacional',
        ]);

        $response->assertStatus(403);
    }

    public function test_operacional_cannot_create_user()
    {
        $operacional = $this->createUser('operacional');

        $response = $this->actingAs($operacional)->postJson('/api/users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password',
            'role' => 'atendente',
        ]);

        $response->assertStatus(403);
    }
}
