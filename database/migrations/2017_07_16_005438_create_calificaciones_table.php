<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalificacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idProfesor');//idUser tipo PROFESOR.
            $table->integer('idAlumno');//idUser tipo ALUMNO.
            $table->string('periodo'); //2017-2018.
            $table->text('evaluaciones');//Arreglo de Objetos con Nombre(evaluacion) y Nota(puntaje).
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
        Schema::dropIfExists('calificaciones');
    }
}
