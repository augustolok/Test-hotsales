<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produto;
use App\Models\User;

class ProdutoSeeder extends Seeder
{
    public function run()
    {
        $admin = User::where('role', 'admin')->first();

        // Se não houver usuários (caso o UserSeeder não tenha rodado), cria um temporário ou retorna
        if (!$admin) {
            return; 
        }

        Produto::create([
            'codigo' => 'PROD001',
            'descricao' => 'Produto Teste 1',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        Produto::create([
            'codigo' => 'PROD002',
            'descricao' => 'Produto Teste 2',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
    }
}
