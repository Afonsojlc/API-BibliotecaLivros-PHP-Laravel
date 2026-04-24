<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\LivroController;
use App\Http\Controllers\ReservaController;

// --- Rotas Públicas ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// --- Rotas Protegidas (Requerem Token) ---
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth base
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // 1. Catálogo (Acessível a todos os autenticados)
    Route::get('/livros',        [LivroController::class, 'index']);
    Route::get('/livros/{id}',   [LivroController::class, 'show']);
    Route::get('/autores',       [AutorController::class, 'index']);

    // 2. Ações do Leitor (Apenas role:leitor)
    Route::middleware('role:leitor')->group(function () {
        Route::get('/reservas/minhas',  [ReservaController::class, 'minhas']);
        Route::post('/reservas',        [ReservaController::class, 'store']);
    });

    // 3. Gestão (Apenas role:admin)
    Route::middleware('role:admin')->group(function () {
        // Rotas de escrita para Autores e Livros
        Route::apiResource('/autores', AutorController::class)->except(['index', 'show']);
        Route::apiResource('/livros', LivroController::class)->except(['index', 'show']);
        
        // Gestão de Reservas
        Route::get('/reservas',         [ReservaController::class, 'index']);
        Route::patch('/reservas/{id}',  [ReservaController::class, 'updateEstado']);
    });
});