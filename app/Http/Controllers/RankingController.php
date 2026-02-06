<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Club;
use App\Models\Event;
use App\Models\Score;
use App\Models\Sanction; // Asegúrate de tener este modelo creado

class RankingController extends Controller
{
    public function index()
    {
        // Devolvemos las 4 listas completas para que el Frontend haga la matemática
        return response()->json([
            'clubs' => Club::where('is_active', true)->get(),
            'events' => Event::where('is_active', true)->get(),
            'scores' => Score::all(), 
            'sanctions' => Sanction::all(),
        ]);
    }
}