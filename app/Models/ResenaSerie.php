<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResenaSerie extends Model
{
    protected $table = 'resenas_series';

    protected $fillable = [
        'user_id',
        'serie_id',
        'contenido',
        'aprobada'
    ];

    //Relación con usuario. Una a muchos. No hay tabla pivote.
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    //Relación con película. Una a muchos. No hay tabla pivote.
    public function serie()
    {
        return $this->belongsTo(Serie::class, 'serie_id');
    }
}
