<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        $user = $request->user()->load('role');

        if (!$user || $user->role->name !== $role) {
            return response()->json([
                'message' => 'No tienes permisos de'  . $role ,
                'user_role' => $user ? $user->role->name : null
        ], 403);
        }

        return $next($request);
    }
}

