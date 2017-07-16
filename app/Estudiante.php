<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    protected $table = 'estudiantes';
    protected $fillable = [
        'idUser', 'nombre','idPersonal'
    ];
    public function user()
    {
        return $this->belongsTo('App\User','id');
    }
}
