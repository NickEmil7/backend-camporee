<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSanctionsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('sanctions')) {
            Schema::create('sanctions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('juez_id')->constrained('users');
                $table->foreignId('club_id')->constrained();
                $table->integer('points_deducted');
                $table->text('description');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('sanctions');
    }
}