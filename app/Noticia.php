<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Noticia extends Model
{
    //
    protected $table = 'noticias';
    protected $fillable = [
        'idUser', 'title', 'content','image'
    ];

    public function user()
    {
        return $this->belongsTo('App\User','id');
    }
}
