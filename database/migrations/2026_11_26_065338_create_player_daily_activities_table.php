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
        Schema::create('player_daily_activities', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('player_id')->constrained('players')->cascadeOnDelete();
            $table->date('activity_date');
            $table->unsignedInteger('games_count')->default(0);
            $table->unsignedInteger('wins_count')->default(0);
            $table->unsignedInteger('draws_count')->default(0);
            $table->unsignedInteger('losses_count')->default(0);
            $table->unsignedSmallInteger('rapid_count')->default(0);
            $table->unsignedSmallInteger('blitz_count')->default(0);
            $table->unsignedSmallInteger('bullet_count')->default(0);
            $table->unsignedSmallInteger('classic_count')->default(0);
            $table->timestamps();

            $table->unique(['player_id', 'activity_date']);
            $table->index('activity_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_daily_activities');
    }
};
