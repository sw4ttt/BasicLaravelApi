<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTarjetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::create('tarjetas', function (Blueprint $table) {
//            $table->increments('id');
//
//            $table->integer('idUsuario');
//            $table->string('tipo');
//            $table->string('numero');
//            $table->string('nombre');
//            $table->string('cod');
//            $table->string('vencimiento');
//            $table->string('street1');
//            $table->string('street2');
//            $table->string('city');
//            $table->string('state');
//            $table->string('country');
//            $table->string('postalCode');
//            $table->string('phone');
//
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
//        Schema::dropIfExists('tarjetas');
    }
}
