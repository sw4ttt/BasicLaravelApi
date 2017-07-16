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
            'idDocument'=> 1111,
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123456'),
            'type' => 'ADMIN',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('users')->insert([
            'idDocument'=> 1112,
            'name' => 'Profesor1',
            'email' => 'profesor1@gmail.com',
            'password' => bcrypt('123456'),
            'type' => 'PROFESOR',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('users')->insert([
            'idDocument'=> 1113,
            'name' => 'Alumno1',
            'email' => 'alumno1@gmail.com',
            'password' => bcrypt('123456'),
            'type' => 'ALUMNO',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
