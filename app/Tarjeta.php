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
        'nombre',
        'cod',
        'vencimiento',
        'street1',
        'street2',
        'city',
        'state',
        'country',
        'postalCode',
        'phone'
    ];
}


