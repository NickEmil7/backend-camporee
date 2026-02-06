<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        // Traemos logs con el usuario asociado, ordenados por fecha
        $query = AuditLog::with('user:id,name,last_name')->latest();

        // Filtro opcional por acción (CREATE, UPDATE, etc.)
        if ($request->has('action') && !empty($request->action)) {
            $query->where('action', $request->action);
        }

        // Limitamos a 100 para no saturar la vista (o usa paginate)
        return response()->json($query->limit(100)->get());
    }
}