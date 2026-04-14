<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContenidoControl extends Model
{
    protected $table = 'contenido_control';

    protected $fillable = [
        'tipo',
        'api_id',
        'visible'
    ];
}
