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
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->uuid('id')->primary;
            $table->foreignUuid('player_id')->constrained('players')->cascadeOnDelete();
            $table->string('started_at');
            $table->string('finished_at');
            $table->string('status');
            $table->integer('games_added');
            $table->string('error');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
    }
};
