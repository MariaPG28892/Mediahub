<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResenaPelicula extends Model
{
    protected $table = 'resenas_peliculas';

    protected $fillable = [
        'user_id',
        'pelicula_id',
        'contenido',
        'aprobada'
    ];

    //Relación con usuario. Una a muchos. No hay tabla pivote.
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    //Relación con película. Una a muchos. No hay tabla pivote.
    public function pelicula()
    {
        return $this->belongsTo(Pelicula::class, 'pelicula_id');
    }
}
