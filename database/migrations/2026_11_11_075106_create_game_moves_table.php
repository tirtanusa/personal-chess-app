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
        Schema::create('game_moves', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('game_id');
            $table->integer('ply');
            $table->string('move_san');
            $table->string('move_uci');
            $table->decimal('eval_cp');
            $table->integer('mate_in')->nullable();
            $table->decimal('cp_loss');
            $table->tinyInteger('classification');
            $table->string('phase');
            $table->unique(['game_id', 'ply']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_moves');
    }
};
