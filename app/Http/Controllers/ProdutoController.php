<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProdutoController extends Controller
{
    public function index(Request $request)
    {
        $query = Produto::query();

        // Lista de campos permitidos para filtragem
        $filterableFields = [
            'id', 'codigo', 'descricao', 
            'created_by', 'updated_by', 'deleted_by',
            'created_at', 'updated_at'
        ];

        foreach ($filterableFields as $field) {
            if ($request->has($field)) {
                $query->where($field, 'like', '%' . $request->input($field) . '%');
            }
        }
        
        // Suporte especial para filtrar deletados se solicitado explicitamente
        if ($request->has('deleted_at')) {
            $query->withTrashed();
            $val = $request->input('deleted_at');
            // Se não for apenas uma flag "true", aplica filtro de texto/data
            if ($val !== 'true' && $val !== '1') {
                $query->where('deleted_at', 'like', '%' . $val . '%');
            }
        }

        $produtos = $query->get();
        return response()->json($produtos);
    }

    public function show($id)
    {
        $produto = Produto::findOrFail($id);
        return response()->json($produto);
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:30|unique:produtos,codigo',
            'descricao' => 'required|string|max:60',
        ]);

        $produto = Produto::create($request->only(['codigo', 'descricao']));

        return response()->json($produto, 201);
    }

    public function update(Request $request, $id)
    {
        $produto = Produto::findOrFail($id);

        $validated = $request->validate([
            'codigo' => 'sometimes|required|string|max:30|unique:produtos,codigo,' . $produto->id,
            'descricao' => 'sometimes|required|string|max:60',
        ]);

        $produto->update($validated);

        return response()->json($produto);
    }

    public function destroy($id)
    {
        $produto = Produto::findOrFail($id);
        $produto->delete();

        return response()->json(['message' => 'Produto excluído com sucesso']);
    }
}
