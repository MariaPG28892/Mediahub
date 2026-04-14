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
        Schema::table('listas', function (Blueprint $table) {
            $table->enum('tipo', ['pelicula', 'serie', 'videojuego'])->default('pelicula')->after('nombre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listas', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }
};
