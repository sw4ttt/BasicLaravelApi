<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $table = 'horarios';
    protected $fillable = [
        'entidad',
        'idEntidad',
        'nombreEntidad',
        'descripcion',
        'dia',
        'inicio',
        'fin',
        'grado',
        'lugar'
    ];
}
