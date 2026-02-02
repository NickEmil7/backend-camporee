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
        Schema::table('scores', function (Blueprint $table) {
            // Agregamos el campo JSON para los detalles (sliders)
            $table->json('details')->nullable()->after('score');
            // Agregamos el campo de texto para comentarios
            $table->text('feedback')->nullable()->after('details');
        });
    }

    public function down(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->dropColumn(['details', 'feedback']);
        });
    }
};
