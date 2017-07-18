<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    //
    protected $table = 'materias';
    protected $fillable = [
        "nombre",
        'grado'
    ];

    public function estudiantes()
    {
        return $this->belongsToMany('App\Estudiante','estudiantes_materias','idMateria','idEstudiante');
    }
}
