<?php

namespace App\Http\Controllers;

use App\Models\Sanction;
use Illuminate\Http\Request;

class SanctionController extends Controller
{
    // Crear una nueva sanción
    public function store(Request $request)
    {
        // Validar la entrada
        $validated = $request->validate([
            'club_id' => 'required|exists:clubs,id',
            'points_deducted' => 'required|integer|min:1',
            'description' => 'required|string|max:255',
        ]);

        // Obtener el juez autenticado
        $juez = $request->user();

        // Crear la sanción con el ID del juez autenticado
        $sanction = Sanction::create([
            'juez_id' => $juez->id,
            'club_id' => $validated['club_id'],
            'points_deducted' => $validated['points_deducted'],
            'description' => $validated['description']
        ]);

        return response()->json([
            'sanction' => $sanction,
            'juez_name' => $juez->name
        ], 201);
    }

    // Modificar una sanción existente
    public function update(Request $request, Sanction $sanction)
    {
        $validated = $request->validate([
            'points_deducted' => 'required|integer|min:1',
            'description' => 'required|string|max:255',
        ]);

        $sanction->update($validated);

        return response()->json($sanction, 200);
    }

    // Eliminar una sanción
    public function destroy(Sanction $sanction)
    {
        $sanction->delete();

        return response()->json(['message' => 'Sanción eliminada correctamente'], 200);
    }

    // Listar todas las sanciones
    public function index()
    {
        $sanctions = Sanction::all();
        return response()->json($sanctions, 200);
    }
}
