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
        Schema::create('videojuego_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id', 'fk_user_videojuego')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('videojuego_id')->nullable();
            $table->foreign('videojuego_id', 'fk_videojuego_user')->references('id')->on('videojuegos')->onUpdate('cascade')->onDelete('cascade');
            $table->enum('estado', ['pendiente', 'jugado', 'favorito']);
            $table->integer('puntuacion')->nullable();
            $table->unique(['user_id', 'videojuego_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videojuego_user');
    }
};
