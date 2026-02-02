<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// --- IMPORTACIÓN OBLIGATORIA ---
use App\Models\Event; 

class EventController extends Controller
{
    public function index()
    {
        // Esto fallaba si faltaba el "use App\Models\Event;"
        return response()->json(Event::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'event_type' => 'required|string',
            'evaluation_type' => 'required|string',
            'max_score' => 'required|numeric',
            'weight' => 'required|numeric',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
            'date' => 'nullable|date',
            'location' => 'nullable|string',
        ]);

        $event = Event::create($request->all());

        return response()->json([
            'message' => 'Evento creado exitosamente',
            'event' => $event
        ], 201);
    }

    public function show(Event $event)
    {
        // Esto fallaba por falta de importación en el encabezado
        return response()->json($event);
    }

    public function update(Request $request, Event $event)
    {
        $request->validate([
            'name' => 'sometimes|string',
            'event_type' => 'sometimes|string',
            'evaluation_type' => 'sometimes|string',
            'max_score' => 'sometimes|numeric',
            'weight' => 'sometimes|numeric',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $event->update($request->all());

        return response()->json([
            'message' => 'Evento actualizado exitosamente',
            'event' => $event
        ]);
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return response()->json([
            'message' => 'Evento eliminado exitosamente'
        ]);
    }

    // Asignar jueces a un evento (Recibe un array de IDs de jueces)
    public function assignJudges(Request $request, Event $event)
    {
        $request->validate([
            'judges' => 'required|array',
            'judges.*' => 'exists:users,id'
        ]);

        // 'sync' reemplaza los jueces anteriores por los nuevos. 
        // Si quieres agregar sin borrar, usa 'attach'.
        $event->judges()->sync($request->judges);

        return response()->json([
            'message' => 'Jueces asignados correctamente',
            'judges' => $event->judges
        ]);
    }
    
    // Obtener los jueces de un evento específico
    public function getJudges(Event $event)
    {
        return response()->json($event->judges);
    }
}