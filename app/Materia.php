<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    //
    protected $table = 'materias';
    protected $fillable = [
        'idProfesor', 'idAlumno','periodo','data'
    ];
}
