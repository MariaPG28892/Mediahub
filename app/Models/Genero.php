<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genero extends Model
{
    protected $table = 'generos';

    protected $fillable = ['nombre'];

    //Relacion de muchos a muchos
    public function peliculas()
    {
        return $this->belongsToMany(
            Pelicula::class,
            'peliculas_genero',
            'genero_id',
            'pelicula_id'
        );
    }
    
    //Relacion de muchos a muchos
    public function series()
    {
        return $this->belongsToMany(
            Serie::class,
            'series_genero',
            'genero_id',
            'serie_id'
        );
    }
}