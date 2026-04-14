<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResenaVideojuego extends Model
{
    protected $table = 'resena_videojuegos'; 

    protected $fillable = [
        'user_id',
        'videojuego_id',
        'contenido',
        'aprobada',
    ];

    //Relación muchos a muchos con tabla pivote, guardo la fechas de creación y modificación
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    //Relación muchos a muchos con tabla pivote, guardo la fechas de creación y modificación
    public function videojuego()
    {
        return $this->belongsTo(Videojuego::class, 'videojuego_id');
    }
}
