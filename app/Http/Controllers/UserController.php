<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function get_user(Request $request){
        $user = $request->user();
        
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'role' => $user->role->name,
            'role_id' => $user->role_id,
            
            // CAMBIO: Calculamos si está activo verificando si NO está en la papelera
            'is_active' => !$user->trashed(), 

            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]);
    }

    public function get_jueces(Request $request){
        // Si quieres incluir jueces desactivados, agrega ->withTrashed() antes del where
        $users = User::where('role_id', 2)->paginate($request->limit);
        return response()->json(['users' => $users]);
    }

    // 1. SHOW: Buscar usuario (incluso si está desactivado)
    public function show($id)
    {
        // CAMBIO: withTrashed() permite encontrar usuarios desactivados
        $user = User::withTrashed()->find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        // Agregamos la propiedad virtual para el frontend
        $user->is_active = !$user->trashed();

        return response()->json($user);
    }

    // 2. UPDATE: Maneja Datos + Restaurar/Eliminar (SoftDelete)
    public function update(Request $request, $id) 
    {
        Log::info('👀 Petición de actualización recibida', $request->all());
        
        // CAMBIO: Buscamos incluso entre los borrados para poder reactivarlos
        $userModel = User::withTrashed()->find($id);

        $shouldBeActive = $request->isActive === 'on' || $request->isActive === true;
    
        if (!$shouldBeActive && $userModel->id === $request->user()->id) {
            return response()->json(['message' => '¡No puedes desactivarte a ti mismo!'], 403);
        }


        
        if (!$userModel) {
             return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            // El unique debe ignorar el ID actual, incluso si está soft-deleted
            'email' => 'required|string|email|max:255|unique:users,email,'.$userModel->id,
            'password' => 'nullable|string|min:6',
            'role_id' => 'required|integer|exists:roles,id'
        ]); 
        
        // 1. Actualizar datos básicos
        $userModel->name = $request->name;
        $userModel->email = $request->email;
        $userModel->role_id = $request->role_id;

        if ($request->has('last_name')) {
            $userModel->last_name = $request->last_name;
        }

        if ($request->filled('password')) {
            $userModel->password = Hash::make($request->password); 
        }

        // 2. LÓGICA DE ACTIVAR / DESACTIVAR (SoftDeletes)
        // Convertimos el input del Switch a booleano
        $shouldBeActive = $request->isActive === 'on' || $request->isActive === true || $request->isActive === "true";

        if ($shouldBeActive) {
            // Si queremos activarlo y actualmente está borrado -> RESTAURAR
            if ($userModel->trashed()) {
                $userModel->restore();
            }
        } else {
            // Si queremos desactivarlo y actualmente está vivo -> BORRAR (Soft)
            if (!$userModel->trashed()) {
                $userModel->delete();
            }
        }

        $userModel->save();

        // Calculamos el estado final para devolverlo al front
        $userModel->is_active = !$userModel->trashed();

        return response()->json(['message' => 'Usuario actualizado exitosamente', 'user' => $userModel]);
    }

    public function delete($id){
        // Si usas el botón de "Eliminar" en la lista, esto hará un SoftDelete (Desactivar)
        // Si quieres borrado permanente usa forceDelete()
        $user = User::findOrFail($id);
        $user->delete(); 

        return response()->json(['message' => 'Usuario desactivado exitosamente']);
    }

    public function index(Request $request) {
        // CAMBIO: Traemos TODOS (activos y eliminados)
        $users = User::with('role')
            ->withTrashed() // <--- Importante para ver el historial
            ->orderBy('id', 'desc') // Opcional: ver los nuevos primero
            ->paginate($request->limit ?? 15);
        
        // Transformamos la colección para inyectar el campo 'is_active' que espera el Frontend
        $users->getCollection()->transform(function ($user) {
            $user->is_active = !$user->trashed();
            return $user;
        });

        return response()->json(['users' => $users]);
    }
}