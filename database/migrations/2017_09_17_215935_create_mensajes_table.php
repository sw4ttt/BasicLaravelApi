<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMensajesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
//'idUsuario',
//'nombre',
//'idMateria',
//'materia',
//'grado',
//'asunto',
//'mensaje'
    public function up()
    {
        Schema::create('mensajes', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('idEmisor');
            $table->string('nombre');
            $table->integer('idMateria');
            $table->string('materia');
            $table->integer('idReceptor');
            $table->integer('grado');
            $table->string('asunto');
            $table->string('mensaje');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mensajes');
    }
}
