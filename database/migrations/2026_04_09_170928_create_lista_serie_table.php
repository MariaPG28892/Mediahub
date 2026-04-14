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
        Schema::create('lista_serie', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lista_id')->nullable();
            $table->foreign('lista_id', 'fk_lista_serie')->references('id')->on('listas')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('serie_id')->nullable();
            $table->foreign('serie_id', 'fk_serie_lista')->references('id')->on('series')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lista_serie');
    }
};
