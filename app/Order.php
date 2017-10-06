<?php

namespace App;

use Alexo\LaravelPayU\Payable;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use Payable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'orders';
    protected $fillable = [
        'idUsuario',
        'articulos',
        'descripcion',
        'tipo',
        'recibo',
        'ref_payco',
        'documento',
        'factura',
        'transactionID',
        'ticketId',
        'pin',
        'codigoproyecto',
        'estado',
        'valor',
        'nombre',
        'apellido',
        'email'
    ];

    protected $casts = [
        'articulos' => 'array'
    ];
}
