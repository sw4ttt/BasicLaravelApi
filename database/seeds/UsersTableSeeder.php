<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //'idDocument','name', 'email', 'password','type'
        DB::table('users')->insert([
            'idPersonal'=> 1111,
            'nombre' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123456'),
            'type' => 'ADMIN',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('users')->insert([
            'idPersonal'=> 1112,
            'nombre' => 'Profesor1',
            'email' => 'profesor1@gmail.com',
            'password' => bcrypt('123456'),
            'type' => 'PROFESOR',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('users')->insert([
            'idPersonal'=> 1113,
            'nombre' => 'Representante1',
            'email' => 'alumno1@gmail.com',
            'password' => bcrypt('123456'),
            'type' => 'REPRESENTANTE',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
