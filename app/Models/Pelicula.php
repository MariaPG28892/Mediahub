<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelicula extends Model
{
    protected $table = 'peliculas';

    protected $fillable = [
        'tmdb_id',
        'titulo',
        'descripcion',
        'poster',
        'backdrop',
        'fecha_estreno',
        'duracion',
        'rating'
    ];

    //Muchas películas pueden tener muchos géneros//Relación muchos a muchos con tabla pivote, guardo la fechas de creación y modificación
    public function generos()
    {
        return $this->belongsToMany(
            Genero::class,
            'peliculas_genero',
            'pelicula_id',
            'genero_id'
        );
    }

    //Relación muchos a muchos con tabla pivote, guardo la fechas de creación y modificación
    public function usuarios()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('estado', 'puntuacion')
            ->withTimestamps();
    }

    //Relación muchos a muchos con tabla pivote, guardo la fechas de creación y modificación
    public function listas()
    {
        return $this->belongsToMany(Lista::class)
            ->withTimestamps();;
    }

    //Getter para traer el poster
    public function getPosterUrlAttribute()
    {
        if (!$this->poster) {
            return asset('images/no-poster.png');
        }

        return str_starts_with($this->poster, 'http')
            ? $this->poster
            : 'https://image.tmdb.org/t/p/w500' . $this->poster;
    }
}