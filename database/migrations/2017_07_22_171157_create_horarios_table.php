<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHorariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->increments('id');
            $table->string('entidad');
            $table->string('idEntidad')->nullable();
            $table->string('nombreEntidad')->nullable();
            $table->string('descripcion')->nullable();
            $table->string('dia');
            $table->string('inicio');
            $table->string('fin');
            $table->integer('grado');
            $table->string('lugar');
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
        Schema::dropIfExists('horarios');
    }
}
