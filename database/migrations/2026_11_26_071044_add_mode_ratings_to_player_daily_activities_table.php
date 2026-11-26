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
        Schema::table('player_daily_activities', function (Blueprint $table): void {
            $table->unsignedSmallInteger('rapid_rating')->nullable()->after('classic_count');
            $table->unsignedSmallInteger('blitz_rating')->nullable()->after('rapid_rating');
            $table->unsignedSmallInteger('bullet_rating')->nullable()->after('blitz_rating');
            $table->unsignedSmallInteger('classic_rating')->nullable()->after('bullet_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('player_daily_activities', function (Blueprint $table): void {
            $table->dropColumn([
                'rapid_rating',
                'blitz_rating',
                'bullet_rating',
                'classic_rating',
            ]);
        });
    }
};
