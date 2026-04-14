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
        Schema::create('pelicula_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id', 'fk_user_pelicula')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('pelicula_id')->nullable();
            $table->foreign('pelicula_id', 'fk_pelicula_user')->references('id')->on('peliculas')->onUpdate('cascade')->onDelete('cascade');
            $table->enum('estado', ['pendiente', 'vista', 'favorito']);
            $table->unique(['user_id', 'pelicula_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelicula_user');
    }
};
