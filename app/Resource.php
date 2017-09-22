<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $table = 'resources';
    protected $fillable = [
        'tipo',
        'entidad',
        'link'
    ];
}
