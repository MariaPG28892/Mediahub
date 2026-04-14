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
        Schema::create('serie_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id', 'fk_user_serie')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('serie_id')->nullable();
            $table->foreign('serie_id', 'fk_serie_user')->references('id')->on('series')->onUpdate('cascade')->onDelete('cascade');
            $table->enum('estado', ['pendiente', 'vista', 'favorito']);
            $table->integer('puntuacion')->nullable();
            $table->unique(['user_id', 'serie_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('serie_user');
    }
};
