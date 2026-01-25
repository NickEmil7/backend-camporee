<?php

namespace App\Http\Controllers;

use App\Models\Score;
use Illuminate\Http\Request;

class ScoreController extends Controller
{
    // Crear un nuevo puntaje
    public function store(Request $request)
    {
        // Validar la entrada (sin juez_id, ya que lo obtenemos del usuario autenticado)
        $validated = $request->validate([
            'club_id' => 'required|exists:clubs,id',
            'evento_id' => 'required|exists:eventos,id',
            'score' => 'required|integer|min:0|max:100',
        ]);

        // Obtener el juez autenticado
        $juez = $request->user();

        // Crear el puntaje con el ID del juez autenticado
        $score = Score::create([
            'juez_id' => $juez->id,
            'club_id' => $validated['club_id'],
            'evento_id' => $validated['evento_id'],
            'score' => $validated['score']
        ]);

        // Devolver el puntaje creado y el nombre del juez
        return response()->json([
            'score' => $score,
            'juez_name' => $juez->name // Nombre del juez autenticado
        ], 201);
    }

    // Actualizar un puntaje existente
    public function update(Request $request, Score $score)
    {
        $validated = $request->validate([
            'score' => 'required|integer|min:0|max:100',
        ]);

        $score->update($validated);

        return response()->json($score, 200);
    }

    // Eliminar un puntaje
    public function destroy(Score $score)
    {
        $score->delete();

        return response()->json(['message' => 'Puntaje eliminado correctamente'], 200);
    }

    // Listar todos los puntajes
    public function index()
    {
        $scores = Score::all();
        return response()->json($scores, 200);
    }
}
