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
        Schema::create('videojuegos', function (Blueprint $table) {
            $table->id();
            $table->integer('rawg_id')->unique();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('poster')->nullable();
            $table->float('rating')->nullable();
            $table->date('fecha_lanzamiento')->nullable();
            $table->json('generos')->nullable();
            $table->json('plataformas')->nullable();
            $table->json('desarrolladores')->nullable();
            $table->json('editores')->nullable();
            $table->json('tags')->nullable();
            $table->enum('estado', ['lanzado', 'proximamente', 'en_desarrollo'])->default('lanzado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videojuegos');
    }
};
