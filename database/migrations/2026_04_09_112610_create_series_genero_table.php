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
        Schema::create('series_genero', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('serie_id')->nullable();
            $table->foreign('serie_id', 'fk_serie_genero')->references('id')->on('series')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('genero_id')->nullable();
            $table->foreign('genero_id', 'fk_genero_serie')->references('id')->on('generos')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('series_genero');
    }
};
