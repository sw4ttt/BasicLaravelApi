<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::create('users', function (Blueprint $table) {
//            $table->increments('id');
//            $table->string('tipoIdPersonal');
//            $table->bigInteger('idPersonal')->unique();
//            $table->string('nombre');
//            $table->string('image');
//            $table->string('tlfDomicilio');
//            $table->string('tlfCelular');
//            $table->string('direccion');
//            $table->string('email');
//            $table->string('password');
//            $table->string('type');
//            $table->rememberToken();
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
//        Schema::drop('users');
    }
}
