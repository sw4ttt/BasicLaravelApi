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
        'articulos',
        'reference',
        'payu_order_id',
        'transaction_id',
        'state',
        'value',
        'user_id',
        'user_name'
    ];

    protected $casts = [
        'articulos' => 'array'
    ];
}
