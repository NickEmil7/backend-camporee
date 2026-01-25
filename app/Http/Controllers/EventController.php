<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    // Obtener todos los eventos
    public function index()
    {
        $events = Event::all();

        // Verifica si la lista está vacía
        if ($events->isEmpty()) {
            return response()->json([
                'message' => 'No hay eventos cargados en el sistema.'
            ], 200); // 200 OK, pero sin contenido relevante
        }

        return response()->json($events);
    }

    // Crear un nuevo evento
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'date' => 'required|date',
            'location' => 'required|string',
            'type' => 'required|string'
        ]);

        $event = Event::create($request->all());

        return response()->json([
            'message' => 'Evento creado exitosamente',
            'event' => $event
        ], 201);
    }

    // Actualizar un evento
    public function update(Request $request, Event $event)
    {
        $request->validate([
            'name' => 'sometimes|string',
            'date' => 'sometimes|date',
            'location' => 'sometimes|string',
            'type' => 'sometimes|string'
        ]);

        $event->update($request->all());

        return response()->json([
            'message' => 'Evento actualizado exitosamente',
            'event' => $event
        ]);
    }

    // Eliminar un evento
    public function destroy(Event $event)
    {
        $event->delete();

        return response()->json([
            'message' => 'Evento eliminado exitosamente'
        ]);
    }
}
