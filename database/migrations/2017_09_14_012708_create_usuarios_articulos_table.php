<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsuariosArticulosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::create('usuarios_articulos', function (Blueprint $table) {
//            $table->primary(['idUsuario', 'idArticulo']);
//            $table->integer('idUsuario');
//            $table->integer('idArticulo');
//            $table->integer('cantidad');
//            $table->timestamps();
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::dropIfExists('usuarios_articulos');
    }
}
