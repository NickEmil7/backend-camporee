<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; 


class AuthController extends Controller
{
public function register(Request $request)
    {
        // Log::info('👀 Petición de registro recibida', $request->all());
        // 1. Validar (Agregamos 'role' a la validación)
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6', // confirmed(ver si hace falta agregarlo) exige password_confirmation
            'role_id' => 'required|integer|exists:roles,id' // <--- Importante validar el rol
        ]);

        // 2. Crear el Usuario
        $user = \App\Models\User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'role_id' => $fields['role_id'] // <--- Asignamos el rol que mandó el Admin
        ]);

        // --- ELIMINAMOS LA CREACIÓN DE TOKEN ---
        // $token = $user->createToken('myapptoken')->plainTextToken;  <--- BORRAR ESTO
        // ---------------------------------------

        // 3. Respuesta limpia (Solo confirmación)
        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (Auth::attempt(...)) {
            \App\Models\AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'LOGIN',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return response()->json(['user' => $user, 'token' => $user->createToken('API Token')->plainTextToken, 'role' => $user->role->name]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Successfully logged out']);
    }
}
