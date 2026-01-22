<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Produto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_filter_produtos_by_codigo()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Produto::factory()->create(['codigo' => 'PROD-123']);
        Produto::factory()->create(['codigo' => 'PROD-456']);

        $response = $this->actingAs($user)->getJson('/api/produtos?codigo=123');

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['codigo' => 'PROD-123']);
    }

    public function test_can_filter_produtos_by_deleted_at()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $produto = Produto::factory()->create();
        $produto->delete();

        // Sem filtro deleted_at, não deve aparecer
        $response1 = $this->actingAs($user)->getJson('/api/produtos');
        $response1->assertJsonMissing(['id' => $produto->id]);

        // Com filtro deleted_at, deve aparecer
        $response2 = $this->actingAs($user)->getJson('/api/produtos?deleted_at=true');
        $response2->assertJsonFragment(['id' => $produto->id]);
    }

    public function test_admin_can_filter_users_by_email()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create(['email' => 'target@example.com']);
        User::factory()->create(['email' => 'other@example.com']);

        $response = $this->actingAs($admin)->getJson('/api/users?email=target');

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['email' => 'target@example.com']);
    }
}
