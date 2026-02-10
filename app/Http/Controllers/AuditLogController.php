<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
   public function index(Request $request)
    {
        // 1. Preparamos la consulta ordenando por el más reciente
        $query = AuditLog::with('user:id,name,last_name')->latest();

        // 2. Filtro de Acción (si el front lo pide)
        if ($request->has('action') && $request->action !== 'ALL') {
            $query->where('action', $request->action);
        }

        // 3. CAMBIO: Limitamos estrictamente a 100 registros
        return response()->json($query->limit(100)->get());
    }
}