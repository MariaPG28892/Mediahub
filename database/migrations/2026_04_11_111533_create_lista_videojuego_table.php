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
        Schema::create('lista_videojuego', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lista_id')->nullable();
            $table->foreign('lista_id', 'fk_lista_videojuego')->references('id')->on('listas')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('videojuego_id')->nullable();
            $table->foreign('videojuego_id', 'fk_videojuego_lista')->references('id')->on('videojuegos')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lista_videojuego');
    }
};
