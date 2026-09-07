<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\LivroController;
use App\Http\Controllers\ReservaController;

// ========================================================
// 🌐 PUBLIC ROUTES (Registration & Authentication)
// ========================================================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// ========================================================
// 🛡️ AUTHENTICATED ROUTES (Protected by Laravel Sanctum)
// ========================================================
Route::middleware('auth:sanctum')->group(function () {

    // --- User Profile & Session ---
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // --- 1. Library Catalog (Accessible to all authenticated users) ---
    Route::get('/livros',        [LivroController::class, 'index']);
    Route::get('/livros/{id}',   [LivroController::class, 'show']);
    Route::get('/autores',       [AutorController::class, 'index']);

    // --- 2. Reader Actions (Restricted to role:leitor) ---
    Route::middleware('role:leitor')->group(function () {
        Route::get('/reservas/minhas',  [ReservaController::class, 'minhas']);
        Route::post('/reservas',        [ReservaController::class, 'store']);
    });

    // --- 3. Library Administration (Restricted to role:admin) ---
    Route::middleware('role:admin')->group(function () {
        // Author & Book Management (Create, Update, Delete)
        Route::apiResource('/autores', AutorController::class)->except(['index', 'show']);
        Route::apiResource('/livros', LivroController::class)->except(['index', 'show']);

        // Reservation & Loan Management
        Route::get('/reservas',         [ReservaController::class, 'index']);
        Route::patch('/reservas/{id}',  [ReservaController::class, 'updateEstado']);
    });
});
