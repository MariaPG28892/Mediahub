<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lista extends Model
{
    protected $fillable = ['user_id', 'nombre', 'tipo'];

    //Relación muchos a muchos con tabla pivote, guardo la fechas de creación y modificación
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    //Relación muchos a muchos con tabla pivote, guardo la fechas de creación y modificación
    public function peliculas()
    {
        return $this->belongsToMany(Pelicula::class)
            ->withTimestamps();;
    }

    //Relación muchos a muchos con tabla pivote, guardo la fechas de creación y modificación
    public function series()
    {
        return $this->belongsToMany(Serie::class)
            ->withTimestamps();;
    }

    //Relación muchos a muchos con tabla pivote, guardo la fechas de creación y modificación
    public function videojuegos()
    {
        return $this->belongsToMany(Videojuego::class)
            ->withTimestamps();;
    }
}
