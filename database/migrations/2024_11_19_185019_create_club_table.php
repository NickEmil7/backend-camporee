<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // CAMBIO: Nombre de tabla 'clubs' (plural)
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable(); // Campo nuevo del Frontend
            $table->text('description')->nullable(); // Campo nuevo
            $table->boolean('is_active')->default(true); // Campo nuevo
            
            // Eliminamos church, location, members porque el frontend nuevo no los usa
            
            $table->timestamps();
            $table->softDeletes(); // Importante para tu modelo
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clubs');
    }
};