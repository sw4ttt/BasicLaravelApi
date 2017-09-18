<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    protected $table = 'mensajes';
    protected $fillable = [
        'idUsuario',
        'nombre',
        'idMateria',
        'materia',
        'grado',
        'asunto',
        'mensaje'
    ];
}
