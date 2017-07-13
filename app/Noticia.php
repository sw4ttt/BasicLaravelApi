<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Noticia extends Model
{
    //
    protected $table = 'noticias';
    protected $fillable = [
        'idUser', 'title', 'content'
    ];
}
