<?php

namespace App\Http\Controllers;

use App\Models\Score;
use Illuminate\Http\Request;

class ScoreController extends Controller
{
    /**
     * Crear un nuevo puntaje (Evaluación)
     */
    // public function store(Request $request)
    // {
    //     // 1. Validaciones
    //     // Quitamos la regla 'array' estricta para 'details' porque FormData envía texto.
    //     $validated = $request->validate([
    //         'club_id'  => 'required|exists:clubs,id',
    //         'event_id' => 'required|exists:events,id',
    //         'score'    => 'required|numeric|min:0', 
    //         'details'  => 'required', // Puede ser string (JSON) o array
    //         'feedback' => 'nullable|string'
    //     ]);

    //     // 2. Procesar 'details'
    //     // Si viene como string (JSON) desde el FormData, lo convertimos a Array PHP.
    //     $details = $request->input('details');
    //     if (is_string($details)) {
    //         $details = json_decode($details, true);
    //     }

    //     // 3. Crear el registro
    //     $score = Score::create([
    //         'user_id'  => $request->user()->id, 
    //         'club_id'  => $validated['club_id'],
    //         'event_id' => $validated['event_id'],
    //         'score'    => $validated['score'],
    //         'details'  => $details, // Pasamos el array limpio
    //         'feedback' => $validated['feedback'] ?? null
    //     ]);

    //     return response()->json([
    //         'message' => 'Puntaje guardado exitosamente',
    //         'score' => $score,
    //         'juez_name' => $request->user()->name
    //     ], 201);
    // }

    // /**
    //  * Listar todos los puntajes
    //  */
    // public function index()
    // {
    //     $scores = Score::with(['user:id,name', 'club:id,name', 'event:id,name'])
    //                     ->latest()
    //                     ->get();
                        
    //     return response()->json($scores, 200);
    // }
public function store(Request $request)
    {
        // ... (Validaciones anteriores igual) ...
        $validated = $request->validate([
            'club_id'  => 'required',
            'event_id' => 'required',
            'score'    => 'required', 
            'details'  => 'required', 
            'feedback' => 'nullable'
        ]);

        // Decodificar JSON
        $details = $request->input('details');
        if (is_string($details)) {
            $details = json_decode($details, true);
        }

        // CREAR EL REGISTRO
        $score = Score::create([
            'juez_id'  => $request->user()->id, // <--- CAMBIO CRÍTICO: Asignamos al campo 'juez_id'
            'club_id'  => $validated['club_id'],
            'event_id' => $validated['event_id'],
            'score'    => $validated['score'],
            'details'  => $details,
            'feedback' => $validated['feedback'] ?? null
        ]);

        return response()->json([
            'message' => 'Puntaje guardado exitosamente',
            'score' => $score
        ], 201);
    }

    // Actualiza también el INDEX para traer la relación correcta
    public function index()
    {
        // Cambiamos 'user:id,name' por 'juez:id,name'
        $scores = Score::with(['juez:id,name', 'club:id,name', 'event:id,name'])
                        ->latest()
                        ->get();
                        
        return response()->json($scores, 200);
    }
    /**
     * Mostrar un puntaje específico
     */
    public function show($id)
    {
        $score = Score::with(['user', 'club', 'event'])->find($id);

        if (!$score) {
            return response()->json(['message' => 'Puntaje no encontrado'], 404);
        }

        return response()->json($score, 200);
    }

    /**
     * Actualizar un puntaje existente
     */
    public function update(Request $request, $id)
    {
        $score = Score::find($id);

        if (!$score) {
            return response()->json(['message' => 'Puntaje no encontrado'], 404);
        }

        $validated = $request->validate([
            'score'    => 'sometimes|numeric|min:0',
            'details'  => 'sometimes',
            'feedback' => 'nullable|string'
        ]);

        // Manejo de detalles en Update también
        if ($request->has('details')) {
            $details = $request->input('details');
            if (is_string($details)) {
                $details = json_decode($details, true);
            }
            $score->details = $details;
        }

        if ($request->has('score')) $score->score = $validated['score'];
        if ($request->has('feedback')) $score->feedback = $validated['feedback'];

        $score->save();

        return response()->json([
            'message' => 'Puntaje actualizado correctamente',
            'score' => $score
        ], 200);
    }

    /**
     * Eliminar un puntaje
     */
    public function destroy($id)
    {
        $score = Score::find($id);

        if (!$score) {
            return response()->json(['message' => 'Puntaje no encontrado'], 404);
        }

        $score->delete();

        return response()->json(['message' => 'Puntaje eliminado correctamente'], 200);
    }
}