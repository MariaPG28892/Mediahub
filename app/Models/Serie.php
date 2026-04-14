<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    protected $table = 'series';

    protected $fillable = [
        'tmdb_id',
        'titulo',
        'descripcion',
        'poster',
        'backdrop',
        'fecha_estreno',
        'emision',
        'numero_temporadas',
        'numero_episodios',
        'rating',
    ];

    //Relación muchos a muchos con tabla pivote, guardo la fechas de creación y modificación
    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'serie_user')
            ->withPivot('estado', 'puntuacion')
            ->withTimestamps();
    }
    //Relación muchos a muchos con tabla pivote, guardo la fechas de creación y modificación
    public function generos()
    {
        return $this->belongsToMany(
            Genero::class,
            'series_genero',
            'serie_id',
            'genero_id'
        );
    }

    //Relación muchos a muchos con tabla pivote, guardo la fechas de creación y modificación
    public function listas()
    {
        return $this->belongsToMany(Lista::class)
            ->withTimestamps();;
    }
}
