<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('audit_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->constrained(); // ¿Quién lo hizo?
        $table->string('action');      // CREATE, UPDATE, DELETE, LOGIN, SANCTION
        $table->string('entity_type')->nullable(); // Ej: App\Models\User
        $table->unsignedBigInteger('entity_id')->nullable(); // ID del objeto afectado
        
        // Aquí guardamos los cambios en formato JSON
        $table->json('old_values')->nullable(); // Lo que había antes
        $table->json('new_values')->nullable(); // Lo que hay ahora
        
        // Datos técnicos
        $table->string('ip_address')->nullable();
        $table->string('user_agent')->nullable(); // Navegador/Dispositivo
        
        $table->timestamps();
    });
}
};
