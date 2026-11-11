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
        Schema::create('games', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('chesscom_uuid')->unique();
            $table->string('url');
            $table->string('time_class');
            $table->string('time_control');
            $table->string('rated');
            $table->string('end_time');
            $table->string('white_player_username');
            $table->string('black_player_username');
            $table->string('white_rating');
            $table->string('black_rating');
            $table->string('white_player_id')->nullable();
            $table->string('black_player_id')->nullable();
            $table->string('white_result');
            $table->string('black_result');
            $table->string('eco_code');
            $table->string('opening_name'); 
            $table->string('opening_url');
            $table->string('pgn');
            $table->string('analysis_status');
            $table->string('analysis_error')->nullable();  
            $table->timestamps();


            //indexing
            $table->index('chesscom_uuid');
            $table->index(['white_player_id', 'time_class', 'end_time']);
            $table->index(['black_player_id', 'time_class', 'end_time']);
            $table->index('eco_code');
            $table->index('time_class');
            $table->index('analysis_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
