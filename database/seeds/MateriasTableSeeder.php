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
            'nombre'=>'Matematica 1'
        ];
        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $materia = Materia::create($input);
        $materia->profesores()->attach(2,[
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]
        );

        $input = [
            'grado'=>1,
            'nombre'=>'Lenguaje 1'
        ];
        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $materia = Materia::create($input);
        $materia->profesores()->attach(2,[
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]
        );


        $input = [
            'grado'=>1,
            'nombre'=>'Historia 1'
        ];
        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $materia = Materia::create($input);
        $materia->profesores()->attach(2,[
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]
        );

        $input = [
            'grado'=>2,
            'nombre'=>'Matematica 2'
        ];
        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $materia = Materia::create($input);
        $materia->profesores()->attach(2,[
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]
        );

        $input = [
            'grado'=>2,
            'nombre'=>'Lenguaje 2'
        ];
        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $materia = Materia::create($input);
        $materia->profesores()->attach(2,[
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]
        );


        $input = [
            'grado'=>2,
            'nombre'=>'Historia 2'
        ];
        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $materia = Materia::create($input);
        $materia->profesores()->attach(2,[
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]
        );
    }
}
