<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSanctionsTable extends Migration
{
    public function up()
    {
        Schema::create('sanctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('juez_id')->constrained('users'); // Relacionado con los jueces
            $table->foreignId('club_id')->constrained();        // Relacionado con los clubes
            $table->integer('points_deducted');                // Puntos restados
            $table->text('description');                       // Descripción de la sanción
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sanctions');
    }
}
