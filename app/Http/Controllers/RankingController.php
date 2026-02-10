<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Club;
// No necesitas importar Score o Sanction si usas las relaciones de Eloquent

class RankingController extends Controller
{
    public function index()
    {
        // 1. Traemos los clubes activos con sus relaciones (Puntajes+Evento y Sanciones)
        // Esto es mucho más eficiente que traer todo suelto.
        $clubs = Club::where('is_active', true)
            ->with(['scores.event', 'sanctions']) 
            ->get();

        // 2. Calculamos el puntaje final de cada club
        $ranking = $clubs->map(function ($club) {
            
            // --- A. SUMA DE PUNTOS POR EVENTOS (CON PESO) ---
            $eventPoints = $club->scores->reduce(function ($carry, $score) {
                $event = $score->event;
                
                // Si el evento no existe o fue borrado, ignoramos
                if (!$event || !$event->is_active) return $carry;

                // Validación para evitar división por cero
                $maxScore = floatval($event->max_score);
                if ($maxScore <= 0) $maxScore = 1; 

                // --- LA FÓRMULA MAESTRA ---
                // (Nota Obtenida / Nota Máxima) * PESO REAL
                $percentage = floatval($score->score) / $maxScore;
                $pointsEarned = $percentage * floatval($event->weight);

                return $carry + $pointsEarned;
            }, 0);

            // --- B. RESTA DE SANCIONES ---
            // Asumimos que la tabla sanctions tiene una columna 'points_deducted'
            // Si tu columna se llama diferente (ej: 'amount'), cámbialo aquí.
            $sanctionPoints = $club->sanctions->sum('points_deducted'); 

            // --- C. TOTAL FINAL ---
            $totalScore = $eventPoints - $sanctionPoints;

            // Devolvemos solo los datos necesarios para la tabla
            return [
                'id' => $club->id,
                'name' => $club->name,
                'code' => $club->code,
                // 'logo' => $club->logo_url, // Descomenta si tienes logos
                'events_score' => round($eventPoints, 2),    // Puntos ganados
                'sanctions_penalty' => round($sanctionPoints, 2), // Puntos perdidos
                'total_score' => round($totalScore, 2)       // El número mágico
            ];
        });

        // 3. Ordenamos: Mayor puntaje arriba
        // values() es para resetear los índices del array (0, 1, 2...)
        $sortedRanking = $ranking->sortByDesc('total_score')->values();

        return response()->json($sortedRanking);
    }
}
    // public function index()
    // {
    //     // Devolvemos las 4 listas completas para que el Frontend haga la matemática
    //     return response()->json([
    //         'clubs' => Club::where('is_active', true)->get(),
    //         'events' => Event::where('is_active', true)->get(),
    //         'scores' => Score::all(), 
    //         'sanctions' => Sanction::all(),
    //     ]);
    // }

    
