<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $row) {
            // Agregamos date y location por si no estaban
            if (!Schema::hasColumn('events', 'date')) {
                $row->date('date')->nullable()->after('description');
            }

        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $row) {
            $row->dropColumn(['date']);
        });
    }
};