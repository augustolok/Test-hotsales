<?php

namespace Tests\Feature;

use App\Models\Produto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProdutoTest extends TestCase
{
    use RefreshDatabase;

    private function createUser($role)
    {
        return User::factory()->create(['role' => $role]);
    }

    public function test_admin_can_view_products()
    {
        $user = $this->createUser('admin');
        Produto::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/produtos');

        $response->assertStatus(200)
                 ->assertJsonCount(1);
    }

    public function test_atendente_cannot_create_product()
    {
        $user = $this->createUser('atendente');

        $response = $this->actingAs($user)->postJson('/api/produtos', [
            'codigo' => 'TEST001',
            'descricao' => 'Teste Produto'
        ]);

        $response->assertStatus(403);
    }

    public function test_operacional_can_create_product()
    {
        $user = $this->createUser('operacional');

        $response = $this->actingAs($user)->postJson('/api/produtos', [
            'codigo' => 'TEST001',
            'descricao' => 'Teste Produto'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('produtos', ['codigo' => 'TEST001']);
    }

    public function test_atendente_cannot_update_product()
    {
        $user = $this->createUser('atendente');
        $produto = Produto::factory()->create();

        $response = $this->actingAs($user)->putJson("/api/produtos/{$produto->id}", [
            'codigo' => 'UPDATED',
            'descricao' => 'Updated Desc'
        ]);

        $response->assertStatus(403);
    }

    public function test_operacional_can_update_product()
    {
        $user = $this->createUser('operacional');
        $produto = Produto::factory()->create();

        $response = $this->actingAs($user)->putJson("/api/produtos/{$produto->id}", [
            'codigo' => 'UPDATED',
            'descricao' => 'Updated Desc'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('produtos', ['codigo' => 'UPDATED']);
    }

    public function test_operacional_cannot_delete_product()
    {
        $user = $this->createUser('operacional');
        $produto = Produto::factory()->create();

        $response = $this->actingAs($user)->deleteJson("/api/produtos/{$produto->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('produtos', ['id' => $produto->id, 'deleted_at' => null]);
    }

    public function test_admin_can_delete_product_soft_delete()
    {
        $user = $this->createUser('admin');
        $produto = Produto::factory()->create();

        $response = $this->actingAs($user)->deleteJson("/api/produtos/{$produto->id}");

        $response->assertStatus(200);
        
        // Verifica se o registro ainda existe no banco mas com deleted_at preenchido (Soft Delete)
        $this->assertSoftDeleted('produtos', [
            'id' => $produto->id,
            'deleted_by' => $user->id
        ]);
    }

    public function test_gestor_can_delete_product_soft_delete()
    {
        $user = $this->createUser('gestor');
        $produto = Produto::factory()->create();

        $response = $this->actingAs($user)->deleteJson("/api/produtos/{$produto->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('produtos', [
            'id' => $produto->id,
            'deleted_by' => $user->id
        ]);
    }
}
