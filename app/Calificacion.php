<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    protected $table = 'calificaciones';
    protected $fillable = [
        'idProfesor',
        'idEstudiante',
        'idMateria',
        'periodo',
        'evaluaciones'
    ];
    protected $casts = [
        'evaluaciones' => 'array'
    ];
}
