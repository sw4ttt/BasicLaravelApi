<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tarjeta extends Model
{
    protected $table = 'tarjetas';
    protected $fillable = [
        'idUsuario',
        'tipo',
        'numero',
        'token',
        'customerId'
        ];
}


