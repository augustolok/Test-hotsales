<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProdutoController;

// Rotas Públicas
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Rotas Autenticadas
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // === Rotas de Produtos ===
    
    // Leitura (Todos os níveis autenticados têm acesso)
    Route::get('/produtos', [ProdutoController::class, 'index']);
    Route::get('/produtos/{id}', [ProdutoController::class, 'show']);

    // Escrita: Criar e Editar (Admin, Gestor, Operacional)
    Route::middleware('role:admin,gestor,operacional')->group(function () {
        Route::post('/produtos', [ProdutoController::class, 'store']);
        Route::put('/produtos/{id}', [ProdutoController::class, 'update']);
        Route::patch('/produtos/{id}', [ProdutoController::class, 'update']);
    });

    // Exclusão (Apenas Admin e Gestor)
    Route::middleware('role:admin,gestor')->group(function () {
        Route::delete('/produtos/{id}', [ProdutoController::class, 'destroy']);
    });

    // === Rotas de Usuários ===
    // Apenas Admin pode criar usuários
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [\App\Http\Controllers\UserController::class, 'index']); // Listagem com filtros
        Route::post('/users', [\App\Http\Controllers\UserController::class, 'store']);
    });

});
