<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Revisamos si la tabla existe para evitar errores, aunque con fresh no debería pasar
        if (!Schema::hasTable('events')) {
            Schema::create('events', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                
                // === CAMPOS NUEVOS (Frontend) ===
                $table->string('event_type')->nullable(); 
                $table->string('evaluation_type')->default('standard');
                $table->text('description')->nullable();
                $table->decimal('max_score', 8, 2)->default(0);
                $table->integer('weight')->default(0);
                $table->boolean('is_active')->default(true);

                // === CAMPOS VIEJOS (Compatibilidad) ===
                // Los dejamos nullable para que no te den error si no los usas
                $table->date('date')->nullable();
                $table->string('location')->nullable();
                $table->string('type')->nullable(); 

                $table->timestamps();
                $table->softDeletes(); // Importante para tu modelo
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
