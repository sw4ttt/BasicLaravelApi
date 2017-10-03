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
        'recibo',
        'ref_payco',
        'documento',
        'factura',
        'estado',
        'valor',
        'nombre',
        'apellido',
        'email'
    ];

//"ref_payco": 417624,
//"factura": "OR-1234",
//"descripcion": "Test Payment",
//"valor": "116000",
//"iva": "16000",
//"baseiva": 100000,
//"moneda": "COP",
//"banco": "Banco de Pruebas",
//"estado": "Aceptada",
//"respuesta": "Aprobada",
//"autorizacion": "000000",
//"recibo": 417624,
//"fecha": "2017-10-02 22:25:58",
//"cod_respuesta": 1,
//"ip": "169.254.74.36",
//"tipo_doc": "CC",
//"documento": "1035851980",
//"nombres": "John",
//"apellidos": "Doe",
//"email": "example@email.com",
//"enpruebas": 1

    protected $casts = [
        'articulos' => 'array'
    ];
}
