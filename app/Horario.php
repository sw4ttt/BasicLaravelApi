<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $table = 'horarios';
    protected $fillable = [
        'tipo',
        'idMateria',
        'nombre',
        'dia',
        'grado',
        'lugar'
    ];
}
