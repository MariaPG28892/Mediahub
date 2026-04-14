<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Pelicula;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nombre_usuario',
        'fecha_nacimiento',
        'telefono',
        'foto',
        'biografia',
        'ultimo_login',
        'bloqueado'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //Un usuario puede tener muchas películas y una película puede pertenecer a muchos usuarios
    public function peliculas()
    {
        return $this->belongsToMany(Pelicula::class)
            ->withPivot('estado', 'puntuacion')
            ->withTimestamps();
    }

    //Relación muchos a muchos con tabla pivote, guardo la fechas de creación y modificación
    public function series()
    {
        return $this->belongsToMany(Serie::class, 'serie_user')
            ->withPivot('estado', 'puntuacion')
            ->withTimestamps();
    }

    //Relación muchos a muchos con tabla pivote, guardo la fechas de creación y modificación
    public function videojuegos()
    {
        return $this->belongsToMany(Videojuego::class, 'videojuego_user')
            ->withPivot('estado', 'puntuacion')
            ->withTimestamps();
    }

    //Relación uno a uno
    public function resenas()
    {
        return $this->hasMany(ResenaPelicula::class, 'user_id');
    }

    public function resenasSeries()
    {
        return $this->hasMany(ResenaSerie::class, 'user_id');
    }

    public function resenasVideojuegos()
    {
        return $this->hasMany(ResenaVideojuego::class, 'user_id');
    }

    public function listas()
    {
        return $this->hasMany(Lista::class);
    }
}