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
        //
        Schema::create('players', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('chesscom_username')->unique()->lower();
            $table->string('display_name');
            $table->string('avatar_url');
            $table->unsignedSmallInteger('last_archived_year');
            $table->unsignedSmallInteger('last_archived_month');
            $table->date('last_synched_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
