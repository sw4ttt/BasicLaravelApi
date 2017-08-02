<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notification;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    //$table->string('tipoIdPersonal');
    //$table->bigInteger('idPersonal')->unique();
    //$table->string('nombre');
    //$table->string('tlfDomicilio');
    //$table->string('tlfCelular');
    //$table->string('direccion');
    //$table->string('email');
    //$table->string('password');
    //$table->string('type');
    protected $fillable = [
        'tipoIdPersonal',
        'idPersonal',
        'nombre',
        'tlfDomicilio',
        'tlfCelular',
        'direccion',
        'email',
        'password',
        'image',
        'type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function noticias()
    {
        return $this->hasMany('App\Noticia','idUser');
    }
    public function estudiantes()
    {
        return $this->hasMany('App\Estudiante','idUser');
    }
    public function materias()
    {
        return $this->belongsToMany('App\Materia','profesores_materias','idProfesor','idMateria');
    }
}
