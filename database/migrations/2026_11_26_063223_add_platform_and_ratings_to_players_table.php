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
        Schema::table('players', function (Blueprint $table): void {
            $table->string('platform')->default('chesscom')->after('id');
            $table->string('country_code', 8)->nullable()->after('avatar_url');
            $table->unsignedSmallInteger('rapid_rating')->nullable()->after('country_code');
            $table->unsignedSmallInteger('blitz_rating')->nullable()->after('rapid_rating');
            $table->unsignedSmallInteger('bullet_rating')->nullable()->after('blitz_rating');
            $table->unsignedSmallInteger('classic_rating')->nullable()->after('bullet_rating');
        });

        Schema::table('games', function (Blueprint $table): void {
            $table->string('opening_url')->nullable()->change();
            $table->text('pgn')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('players', function (Blueprint $table): void {
            $table->dropColumn([
                'platform',
                'country_code',
                'rapid_rating',
                'blitz_rating',
                'bullet_rating',
                'classic_rating',
            ]);
        });

        Schema::table('games', function (Blueprint $table): void {
            $table->string('opening_url')->nullable(false)->change();
            $table->string('pgn')->change();
        });
    }
};
