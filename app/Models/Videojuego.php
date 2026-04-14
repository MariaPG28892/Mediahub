<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Videojuego extends Model
{
    protected $table = 'videojuegos';

    protected $fillable = [
        'rawg_id',
        'nombre',
        'descripcion',
        'poster',
        'rating',
        'fecha_lanzamiento',
        'generos',
        'plataformas',
        'desarrolladores',
        'editores',
        'tags',
        'estado',
    ];

    protected $casts = [
        'generos' => 'array',
        'plataformas' => 'array',
        'desarrolladores' => 'array',
        'editores' => 'array',
        'tags' => 'array',
    ];

    //Relación muchos a muchos con tabla pivote, guardo la fechas de creación y modificación
    public function users()
    {
        return $this->belongsToMany(User::class, 'videojuego_user')
            ->withPivot('estado', 'puntuacion')
            ->withTimestamps();
    }

    //Relación muchos a muchos con tabla pivote, guardo la fechas de creación y modificación
    public function listas()
    {
        return $this->belongsToMany(Lista::class);
    }
}
