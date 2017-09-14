<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
//$table->string('nombre');
//$table->string('cantidad');
//$table->string('estado');
//$table->float('precio', 8, 2);
    protected $table = 'articulos';
    protected $fillable = [
        'nombre',
        'cantidad',
        'estado',
        'precio'
    ];
}
