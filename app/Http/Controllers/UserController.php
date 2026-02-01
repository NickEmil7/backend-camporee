<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function get_user(Request $request){
        return response()->json([
            'name' => $request->user() -> name,
            'role' => $request->user() -> role -> name
        ]);
    }
    public function get_jueces(Request $request){
        
        $users = User::where('role_id', 2)->paginate($request->limit);
        return response()->json(['users' => $users]);

    }

    public function update(Request $request, $user){
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]); 
        
        // $user = User::findOrFail($id); -> Borrar despues de probar $user

        // Verificar que el usuario a modificar sea un Juez
        if ($user->role->name !== 'Juez') {
            return response()->json(['message' => 'Solo puedes modificar jueces.'], 403);
        }

        // Actualizar los datos del juez
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('last_name')) {
            $user->last_name = $request->last_name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('password')) {
            $user->password = Hash::make($request->password); 
        }

        $user->save();

        return response()->json(['message' => 'Juez actualizado exitosamente', 'user' => $user]);
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
}
