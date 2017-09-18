<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticulosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::create('articulos', function (Blueprint $table) {
//            $table->increments('id');
//            $table->string('nombre');
//            $table->string('cantidad');
//            $table->string('categoria');
//            $table->string('descripcion');
//            $table->string('estado');
//            $table->float('precio', 8, 2);
//            $table->string('image');
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
//        Schema::dropIfExists('articulos');
    }
}
