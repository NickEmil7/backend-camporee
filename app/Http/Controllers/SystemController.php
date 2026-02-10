<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Score;
use App\Models\Sanction;

class SystemController extends Controller
{
   public function resetScores()
    {
        // OPCIÓN AUDITABLE:
        // Traemos todos los puntajes y los borramos uno por uno.
        // Esto dispara el evento 'deleted' en el Observer para CADA puntaje.
        $scores = Score::all();
        
        $scores->each(function ($score) {
            $score->delete(); 
        });

        // Opcional: Si son muchísimos (ej. 5000), esto puede tardar unos segundos.
        // Pero es la única forma de tener el registro "viejo" en la auditoría.

        return response()->json(['message' => 'Todos los puntajes han sido eliminados y auditados.']);
    }

    public function resetSanctions()
    {
        // OPCIÓN AUDITABLE:
        $sanctions = Sanction::all();

        $sanctions->each(function ($sanction) {
            $sanction->delete();
        });

        return response()->json(['message' => 'Todas las sanciones han sido eliminadas y auditadas.']);
    }
}