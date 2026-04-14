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
        Schema::create('resenas_peliculas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id', 'fk_user_resena')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('pelicula_id')->nullable();
            $table->foreign('pelicula_id', 'fk_pelicula_resena')->references('id')->on('peliculas')->onUpdate('cascade')->onDelete('cascade');
            $table->text('contenido')->nullable();
            $table->boolean('aprobada')->nullable()->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resenas_peliculas');
    }
};
