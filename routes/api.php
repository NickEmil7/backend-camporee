<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\SanctionController;
use App\Http\Controllers\UserController;

// --- RUTAS PÚBLICAS ---
Route::post('/login', [AuthController::class, 'login']);

// --- RUTAS PROTEGIDAS ---
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [UserController::class, 'get_user']);

    // RUTAS SOLO PARA ADMIN
    Route::middleware('role:Admin')->group(function () {
        Route::post('/register', [AuthController::class, 'register']); // Registro ahora es privado
        
        // Gestión de Clubes
        Route::apiResource('clubs', ClubController::class); // Crea index, store, show, update, destroy
        
        // Gestión de Eventos
        Route::apiResource('events', EventController::class);
        
        // Gestión de Usuarios/Jueces
        Route::get('/jueces', [UserController::class, 'get_jueces']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'delete']);
    });

    // RUTAS PARA JUECES (Y ADMIN)
    Route::middleware('role:Juez')->group(function () {
        // Puntajes (Corregido: Ahora apuntan a ScoreController)
        Route::post('/scores', [ScoreController::class, 'store']);
        Route::put('/scores/{score}', [ScoreController::class, 'update']);
        Route::delete('/scores/{score}', [ScoreController::class, 'destroy']);

        // Sanciones
        Route::post('/sanctions', [SanctionController::class, 'store']);
        Route::put('/sanctions/{sanction}', [SanctionController::class, 'update']);
        Route::delete('/sanctions/{sanction}', [SanctionController::class, 'destroy']);
    });

    // RUTAS DE CONSULTA (Para todos los logueados: Admin, Juez, Director)
    // Route::get('/ranking', [ScoreController::class, 'index']); // Ejemplo de ruta para ver resultados
});

