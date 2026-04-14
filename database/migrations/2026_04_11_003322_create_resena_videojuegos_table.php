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
        Schema::create('resena_videojuegos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id', 'fk_user_resena_videojuego')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('videojuego_id')->nullable();
            $table->foreign('videojuego_id', 'fk_videojuego_resena')->references('id')->on('videojuegos')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('resena_videojuegos');
    }
};
