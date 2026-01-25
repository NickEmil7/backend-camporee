<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\SanctionController;


use App\Models\Role;
Route::get('/roles', function () {
    $roles = Role::all(); // Obtener todos los roles
    return response()->json($roles); // Retornar los roles en formato JSON
});

//En el register el unico que puede registrar nuevo usuario es el admin
Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/user', [UserController::class, 'get_user']) -> middleware('auth:sanctum');

//Obtener jueces
Route::get('/jueces', [UserController::class, 'get_jueces']);

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::put('/jueces/{id}', [UserController::class, 'update'])-> middleware('auth:sanctum', 'role:Admin');
Route::delete('/jueces/{id}', [UserController::class, 'delete'])-> middleware('auth:sanctum', 'role:Admin');



// Eventos
Route::middleware(['auth:sanctum'])->group(function () {
    Route::middleware('role:Admin')->group(function () {
        Route::post('/events', [EventController::class, 'store']);
        Route::put('/events/{id}', [EventController::class, 'update']);
        Route::delete('/events/{id}', [EventController::class, 'destroy']);
    });
});

Route::get('/events', [EventController::class, 'index']);

// Clubes
Route::get('/clubs', [ClubController::class, 'index']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::middleware('role:Admin')->group(function () {
        Route::post('/clubs', [ClubController::class, 'store']);
        Route::put('/clubs/{id}', [ClubController::class, 'update']);
        Route::delete('/clubs/{id}', [ClubController::class, 'destroy']);
    });
});


//Puntajes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::middleware('role:Juez')->group(function () {
        Route::post('/puntajes', [EventController::class, 'store']);
        Route::put('/puntajes/{id}', [EventController::class, 'update']);
        Route::delete('/puntajes/{id}', [EventController::class, 'destroy']);
    });
});
Route::get('/puntajes', [EventController::class, 'index']);

//Sanciones
// Rutas protegidas por el middleware de autenticación y rol de Juez
Route::middleware(['auth:sanctum', 'role:Juez'])->group(function () {
    Route::post('/sanctions', [SanctionController::class, 'store']); // Crear sanción
    Route::put('/sanctions/{sanction}', [SanctionController::class, 'update']); // Modificar sanción
    Route::delete('/sanctions/{sanction}', [SanctionController::class, 'destroy']); // Eliminar sanción
});

// Ruta pública para consultar sanciones (opcional)
Route::get('/sanctions', [SanctionController::class, 'index']); // Ver todas las sanciones




Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/admin', function () {
        return 'Admin Access';
    })->middleware('role:Admin');
    
    Route::get('/juez', function () {
        return 'Juez Access';
    })->middleware('role:Juez');


});

