<?php

namespace App\Http\Controllers;
use App\Models\Club;
use App\Models\Score;
use Illuminate\Http\Request;

class ClubController extends Controller
{
    function index(){
        // Devolvemos siempre la lista (aunque esté vacía) para que el frontend no falle
        return response()->json(Club::all());
    }

    function store(Request $request){
        $request->validate([
            'name' => 'required|string',
            'code' => 'required|string|unique:clubs,code',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);
        
        $club = Club::create($request->all());
        return response()->json(['club' => $club], 201);
    }

    function update(Request $request, Club $club){
        $request->validate([
            'name' => 'sometimes|string',
            'code' => 'sometimes|string|unique:clubs,code,' . $club->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ],[
        'code.unique' => 'Este código ya pertenece a otro club.',
        ]);
    
        
        $club->update($request->all());
        return response()->json(['club' => $club]);
    }

    function destroy(Club $club){
        $club->delete();
        return response()->json(['message' => 'Club eliminado']);
    }
    public function show(Club $club)
    {
        return response()->json($club);
    }

    // ... (imports necesarios arriba: use App\Models\Score;)

    // Obtener estadísticas y desglose de puntajes de un club
    public function stats($id)
        {
            // Esto está perfecto
            $club = Club::withTrashed()->find($id);

            if (!$club) {
                return response()->json(['message' => 'Club no encontrado'], 404);
            }

            // MEJORA AQUÍ: Agregamos ->with('event') para precargar la relación
            // Esto evita errores si intentas acceder al evento después
            $scores = \App\Models\Score::where('club_id', $club->id)
                        ->with('event') // <--- AGREGA ESTO
                        ->get();

            $averageScore = $scores->count() > 0 ? $scores->avg('score') : 0;

            $eventBreakdown = $scores->map(function ($score) {
                return [
                    'id' => $score->id,
                    // Usamos el operador nullsafe (?) para evitar errores si event es null
                    'eventName' => $score->event?->name ?? 'Evento eliminado', 
                    'evaluationType' => $score->event?->event_type ?? 'Standard',
                    'score' => $score->score,
                    'details' => $score->details ?? null, 
                    'notes' => $score->feedback ?? null
                ];
            });

            return response()->json([
                'club' => $club,
                'averageScore' => round($averageScore, 2),
                'totalDeductions' => 0,
                'eventBreakdown' => $eventBreakdown
            ]);
        }


}