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
        Schema::create('peliculas_genero', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pelicula_id')->nullable();
            $table->foreign('pelicula_id', 'fk_pelicula_genero')->references('id')->on('peliculas')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('genero_id')->nullable();
            $table->foreign('genero_id', 'fk_genero_pelicula')->references('id')->on('generos')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peliculas_genero');
    }
};
