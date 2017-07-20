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

    public function profesores()
    {
        return $this->belongsToMany('App\User','profesores_materias','idMateria','idProfesor');
    }
}
