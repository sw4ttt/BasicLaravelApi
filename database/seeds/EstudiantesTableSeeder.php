<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EstudiantesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('estudiantes')->insert([
            'idUser'=> 3,
            'idPersonal' => 123,
            'nombre' => 'Estudiante 1',
            'grado' => 1,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('estudiantes')->insert([
            'idUser'=> 3,
            'idPersonal' => 124,
            'grado' => 2,
            'nombre' => 'Estudiante 2',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
