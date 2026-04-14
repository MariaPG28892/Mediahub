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
        Schema::create('series', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tmdb_id')->unique();
            $table->text('titulo')->nullable();
            $table->text('descripcion')->nullable();
            $table->text('poster')->nullable();
            $table->text('backdrop')->nullable();
            $table->date('fecha_estreno')->nullable();
            $table->text('emision')->nullable(); 
            $table->integer('numero_temporadas')->nullable();
            $table->integer('numero_episodios')->nullable();
            $table->float('rating')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('series');
    }
};
