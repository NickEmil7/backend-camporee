<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('scores')) {
            Schema::create('scores', function (Blueprint $table) {
                $table->id();
                $table->foreignId('juez_id')->constrained('users');
                $table->integer('club_id')->constrained();
                $table->integer('event_id')->constrained();
                $table->integer('score'); // Si necesitas decimales cámbialo a decimal
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};