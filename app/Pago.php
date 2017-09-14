<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';
    protected $fillable = [
        'entidad',
        'tipo',
        'articulos',
        'monto',
        'estado'
    ];
    protected $casts = [
        'articulos' => 'array'
    ];
}