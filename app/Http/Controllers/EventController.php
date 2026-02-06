<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    /**
     * Listar todos los eventos
     */
    public function index()
    {
        return response()->json(Event::all());
    }

    /**
     * Crear un nuevo evento
     */
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

    /**
     * Mostrar un evento específico
     * CORREGIDO: Recibe $id en lugar de (Event $event) para evitar errores de binding
     */
public function show($id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        // CORRECCIÓN: Devolvemos el evento DIRECTAMENTE, sin envoltorios de debug
        return response()->json($event);
    }

/**
     * Actualizar un evento
     */
    public function update(Request $request, $id)
    {
        

        // $event = Event::find($id);

        // if (!$event) {
        //     \Illuminate\Support\Facades\Log::error(">>> [DEBUG LARAVEL] Evento NO encontrado en DB");
        //     return response()->json(['message' => 'Evento no encontrado'], 404);
        // }
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        // 1. VALIDACIÓN: Usamos los nombres que vienen del Frontend (camelCase)
        //    o simplemente validamos los tipos.
        $request->validate([
            'name'            => 'sometimes|string',
            'eventType'       => 'sometimes|string', // Nombre del input en React
            'evaluationType'  => 'sometimes|string',
            'maxScore'        => 'sometimes|numeric',
            'weight'          => 'sometimes|numeric',
            'is_active'       => 'boolean', // Recuerda el input hidden que agregamos
            'description'     => 'nullable|string',
        ]);

        // 2. ACTUALIZACIÓN MANUAL: Mapeamos Input React -> Columna DB
        $event->update([
            'name'            => $request->input('name', $event->name),
            
            // Aquí ocurre la magia: leemos 'eventType' y guardamos en 'event_type'
            'event_type'      => $request->input('eventType', $event->event_type),
            'evaluation_type' => $request->input('evaluationType', $event->evaluation_type),
            'max_score'       => $request->input('maxScore', $event->max_score),
            
            'weight'          => $request->input('weight', $event->weight),
            'is_active'       => $request->input('is_active', $event->is_active),
            'description'     => $request->input('description', $event->description),
        ]);

        return response()->json([
            'message' => 'Evento actualizado exitosamente',
            'event' => $event
        ]);
    }
    /**
     * Eliminar un evento
     * CORREGIDO: Recibe $id
     */
    public function destroy($id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        $event->delete();

        return response()->json(['message' => 'Evento eliminado exitosamente']);
    }

    /**
     * Asignar jueces a un evento
     * CORREGIDO: Recibe $id
     */
    public function assignJudges(Request $request, $id)
    {
        // 1. Búsqueda manual para evitar errores automáticos
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        // 2. Validación "present" permite enviar un array vacío [] (para borrar jueces)
        //    "required" fallaría si intentas dejar el evento sin jueces.
        $request->validate([
            'judges' => 'present|array', 
            'judges.*' => 'exists:users,id'
        ]);

        // 3. LIMPIEZA DE DATOS: Convertimos ["1", "5"] a [1, 5]
        // Esto evita errores si tu base de datos es estricta con los tipos
        $judgeIds = array_map('intval', $request->input('judges', []));

        // 4. Guardamos
        $event->judges()->sync($judgeIds);

        return response()->json([
            'message' => 'Jueces asignados correctamente',
            'count' => count($judgeIds)
        ]);
    }
    /**
     * Obtener los jueces asignados a un evento
     * CORREGIDO: Recibe $id
     */
    public function getJudges($id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        return response()->json($event->judges);
    }

    /**
     * Obtener SOLO los eventos asignados al juez autenticado
     */
    public function myEvents(Request $request)
    {
        $user = $request->user();

        // Buscamos eventos que tengan asignado este usuario en la tabla pivote
        $events = Event::whereHas('judges', function($q) use ($user) {
            $q->where('users.id', $user->id);
        })
        ->where('is_active', true) // Opcional: Solo traer los activos
        ->get();

        return response()->json($events);
    }
}