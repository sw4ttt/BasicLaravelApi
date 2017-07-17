<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Noticia extends Model
{
    //
    protected $table = 'noticias';
    protected $fillable = [
        'idUser',
        'titulo',
        'contenido',
        'imagen'
    ];

    public function user()
    {
        return $this->belongsTo('App\User','id');
    }
}
