<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // --- CAMBIO IMPORTANTE AQUÍ ---
    public function get_user(Request $request){
        $user = $request->user();
        
        // Devolvemos todos los datos que el frontend necesita
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'last_name' => $user->last_name, // Necesario para 'fullName'
            'email' => $user->email,
            'role' => $user->role->name, // "Admin" o "Juez"
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]);
    }

    public function get_jueces(Request $request){
        // Filtramos por rol de Juez (asumiendo que ID 2 es Juez)
        // O mejor, usamos whereHas si las relaciones están bien, pero esto funciona:
        $users = User::where('role_id', 2)->paginate($request->limit);
        return response()->json(['users' => $users]);
    }

    public function update(Request $request, $user){
        // Nota: Laravel inyecta el modelo User automáticamente si la ruta es /users/{user}
        // Pero como recibes $user (probablemente el ID) desde la ruta, lo buscamos:
        
        $userModel = User::find($user);
        
        if (!$userModel) {
             return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $request->validate([
            'name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,'.$userModel->id,
            'password' => 'nullable|string|min:8',
        ]); 
        
        // Verificar que el usuario a modificar sea un Juez
        if ($userModel->role->name !== 'Juez') {
            return response()->json(['message' => 'Solo puedes modificar jueces.'], 403);
        }

        // Actualizar los datos del juez
        if ($request->has('name')) {
            $userModel->name = $request->name;
        }
        if ($request->has('last_name')) {
            $userModel->last_name = $request->last_name;
        }
        if ($request->has('email')) {
            $userModel->email = $request->email;
        }
        if ($request->has('password') && !empty($request->password)) {
            $userModel->password = Hash::make($request->password); 
        }

        $userModel->save();

        return response()->json(['message' => 'Juez actualizado exitosamente', 'user' => $userModel]);
    }

    public function delete($id){
        $user = User::findOrFail($id);

        // Verificar que el usuario a modificar sea un Juez
        if ($user->role->name !== 'Juez') {
            return response()->json(['message' => 'Solo puedes modificar jueces.'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'Juez eliminado exitosamente']);
    }

    public function index(Request $request) {
    // Retorna todos los usuarios paginados
    $users = User::with('role')->paginate($request->limit ?? 15);
    return response()->json(['users' => $users]);
    }
}