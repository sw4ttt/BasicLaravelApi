<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\User;
use App\Noticia;
use App\Calificacion;
use App\Estudiante;
use App\Materia;
use App\Material;

class MateriasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        DB::table('materias')->insert([
//            'nombre'=> "Materia 1",
//            'grado' => 1,
//            'idProfesor' => 2,
//            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
//            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
//        ]);
//        DB::table('materias')->insert([
//            'nombre'=> "Materia 2",
//            'grado' => 2,
//            'idProfesor' => 2,
//            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
//            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
//        ]);

        $input = [
            'grado'=>1,
            'nombre'=>'Materia1'
        ];
        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $materia = Materia::create($input);
        $materia->profesores()->attach(2,[
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]
        );

        $input2 = [
            'grado'=>1,
            'nombre'=>'Materia2'
        ];
        $input2['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input2['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $materia2 = Materia::create($input2);
        $materia2->profesores()->attach(2,[
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]
        );
    }
}
