<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\SanctionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\RankingController;

// --- RUTAS PÚBLICAS ---
Route::post('/login', [AuthController::class, 'login']);
Route::get('/ranking', [RankingController::class, 'index']);

Route::post('/sanctions', [SanctionController::class, 'store']);
Route::put('/sanctions/{sanction}', [SanctionController::class, 'update']);
Route::delete('/sanctions/{sanction}', [SanctionController::class, 'destroy']);



// --- RUTAS PROTEGIDAS (Cualquier usuario logueado) ---
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [UserController::class, 'get_user']);
    
    // MOVIDO AQUÍ: Todos pueden VER eventos y clubes para saber qué evaluar
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{event}', [EventController::class, 'show']);
    Route::get('/clubs', [ClubController::class, 'index']);
    Route::get('/clubs/{club}', [ClubController::class, 'show']);
    Route::get('/judge/events', [EventController::class, 'myEvents']);

    // --- RUTAS SOLO PARA ADMIN ---
    Route::middleware('role:Admin')->group(function () {
        Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
        Route::get('/audit-logs', [AuditLogController::class, 'index']);


        Route::post('/register', [AuthController::class, 'register']);
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        
        // Gestión de Clubes (Crear, Editar, Borrar)
        Route::post('/clubs', [ClubController::class, 'store']);
        Route::put('/clubs/{club}', [ClubController::class, 'update']);
        Route::delete('/clubs/{club}', [ClubController::class, 'destroy']);
        Route::get('/clubs/{club}/stats', [ClubController::class, 'stats']);
        
        // Gestión de Eventos (Crear, Editar, Borrar)
        Route::post('/events', [EventController::class, 'store']);
        Route::put('/events/{id}', [EventController::class, 'update']);
        Route::delete('/events/{id}', [EventController::class, 'destroy']);
        
        // Gestión de Usuarios
        Route::get('/jueces', [UserController::class, 'get_jueces']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'delete']);

        Route::get('/events/{id}', [EventController::class, 'show']);
        
        // Rutas para asignar jueces
        Route::post('/events/{id}/judges', [EventController::class, 'assignJudges']);
        Route::get('/events/{id}/judges', [EventController::class, 'getJudges']);
    });

    // --- RUTAS PARA JUECES (Y ADMIN) ---
    Route::middleware('role:Juez')->group(function () {
        // Puntajes
        Route::post('/scores', [ScoreController::class, 'store']);
        Route::put('/scores/{score}', [ScoreController::class, 'update']);
        Route::delete('/scores/{score}', [ScoreController::class, 'destroy']);

        // Sanciones
        Route::post('/sanctions', [SanctionController::class, 'store']);
        Route::put('/sanctions/{sanction}', [SanctionController::class, 'update']);
        Route::delete('/sanctions/{sanction}', [SanctionController::class, 'destroy']);



    });
});

