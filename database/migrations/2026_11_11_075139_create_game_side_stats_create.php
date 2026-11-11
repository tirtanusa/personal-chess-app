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
        Schema::create('game_side_stats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('game_id')->constrained('games')->cascadeOnDelete();
            $table->foreignUuid('player_id')->constrained('players')->cascadeOnDelete();
            $table->string('color');
            $table->decimal('accuracy');
            $table->decimal('avg_cp_loss');
            $table->integer('best_moves');
            $table->integer('mistakes');
            $table->integer('good_moves');
            $table->integer('excellent_moves');
            $table->integer('blunders');
            $table->integer('inaccuracy');
            $table->string('engine_names');
            $table->string('engine_version');
            $table->string('depth');
            $table->timestamps();
            $table->unique(['game_id', 'color']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_side_stats');
    }
};
