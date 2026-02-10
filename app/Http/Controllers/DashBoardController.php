<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Club;
use App\Models\Event;
use App\Models\User;
use App\Models\Score;

class DashboardController extends Controller
{
    public function stats()
    {
        $eventsWithoutJudges = Event::where('is_active', true)
        ->doesntHave('judges')
        ->select('id', 'name') // Solo necesitamos ID y nombre
        ->get();
        return response()->json([
            // Contamos clubes activos
            'total_clubs' => Club::where('is_active', true)->count(),
            
            // Contamos eventos activos
            'total_events' => Event::where('is_active', true)->count(),
            
            // Contamos Jueces (role_id 2). 
            // Como usas SoftDeletes, al no poner withTrashed(), 
            // Laravel automáticamente cuenta solo los activos.
            'total_judges' => User::where('role_id', 2)->count(),
            
            // Total de evaluaciones
            'total_scores' => Score::count(),


            'pending_events' => $eventsWithoutJudges
        ]);
    }
}