<?php

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use App\Calificacion;
use App\Materia;
use App\User;
use App\Estudiante;

class CalificacionesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('calificaciones')->delete();

        $profesores = User::where('type','PROFESOR')->get();

//        echo "profesores->count()=".$profesores->count();
        if($profesores->count()>0)
        {

            foreach ($profesores as $profesor){
//                echo "\n profesor=".$profesor->nombre;
                foreach ($profesor->materias as $materia) {
                    $estudiantes = Estudiante::where('grado',$materia->grado)->get();
//                    echo "\n  estudiantes->count()=".$estudiantes->count();
                    if($estudiantes->count()>0){
                        foreach ($estudiantes as $estudiante){

                            $calificacion = new Calificacion;
                            $calificacion->idProfesor = $profesor->id;
                            $calificacion->idEstudiante = $estudiante->id;
                            $calificacion->idMateria = $materia->id;
                            $calificacion->periodo = "2017-2018";
                            $calificacion->evaluaciones = [];
                            $calificacion->acumulado = 0;
                            $calificacion->save();

                        }
                    }
                }

            }
        }

    }
}
