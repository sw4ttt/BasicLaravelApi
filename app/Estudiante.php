<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    protected $table = 'estudiantes';
    protected $fillable = [
        'idUser',
        'idPersonal',
        'nombre'
    ];
    public function user()
    {
        return $this->belongsTo('App\User','id');
    }
    public function calificaciones()
    {
        return $this->hasMany('App\Calificacion','idEstudiante');
    }
    public function materias()
    {
        return $this->belongsToMany('App\Materia','estudiantes_materias','idEstudiante','idMateria');
    }
}
