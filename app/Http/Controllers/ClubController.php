<?php

namespace App\Http\Controllers;
use App\Models\Club;
use Illuminate\Http\Request;

class ClubController extends Controller
{
    function index(){
        $clubs = Club::all();

        // Verifica si la lista está vacía
        if ($clubs->isEmpty()) {
            return response()->json([
                'message' => 'No hay clubes cargados en el sistema.'
            ], 200); // 200 OK, pero sin contenido relevante
        }

        return response()->json(['clubs' => $clubs]);
    }
    function store(Request $request){
        $request->validate([
            'name' => 'required|string',
            'church' => 'required|string',
            'location' => 'required|string',
            'members' => 'required|integer'
        ]);
        $club = Club::create($request->all());
        return response()->json(['club' => $club]);
    }
    function update(Request $request, Club $club){
        $request->validate([
            'name' => 'sometimes|string',
            'church' => 'sometimes|string',
            'location' => 'sometimes|string',
            'members' => 'sometimes|integer'
        ]);
        $club->update($request->all());
        return response()->json(['club' => $club]);
    }
    function destroy(Club $club){
        $club->delete();
        return response()->json(['message' => 'club deleted']);
    }
}
