<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    //$table->string('tipoIdPersonal');
    //$table->bigInteger('idPersonal')->unique();
    //$table->string('nombre');
    //$table->string('tlfDomicilio');
    //$table->string('tlfCelular');
    //$table->string('direccion');
    //$table->string('email');
    //$table->string('password');
    //$table->string('type');
    public function run()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tipoIdPersonal');
            $table->bigInteger('idPersonal')->unique();
            $table->string('nombre');
            $table->string('image');
            $table->string('tlfDomicilio');
            $table->string('tlfCelular');
            $table->string('direccion');
            $table->string('email');
            $table->string('password');
            $table->string('type');
            $table->rememberToken();
            $table->timestamps();
        });
        DB::table('users')->insert([
            'tipoIdPersonal' => 'cedula',
            'idPersonal'=> 1111,
            'nombre' => 'admin',
            'image' => 'https://lacasacreativaapp.com/images/default-profile.jpg',
            'tlfDomicilio' => '+58123456',
            'tlfCelular' => '+58123456',
            'direccion' => 'direccion ADMIN',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123456'),
            'type' => 'ADMIN',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('users')->insert([
            'tipoIdPersonal' => 'cedula',
            'idPersonal'=> 1112,
            'nombre' => 'Profesor1',
            'image' => 'https://lacasacreativaapp.com/images/default-profile.jpg',
            'tlfDomicilio' => '+58123456',
            'tlfCelular' => '+58123456',
            'direccion' => 'direccion PROFESOR',
            'email' => 'profesor1@gmail.com',
            'password' => bcrypt('123456'),
            'type' => 'PROFESOR',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('users')->insert([
            'tipoIdPersonal' => 'cedula',
            'idPersonal'=> 1113,
            'nombre' => 'Representante1',
            'image' => 'https://lacasacreativaapp.com/images/default-profile.jpg',
            'tlfDomicilio' => '+58123456',
            'tlfCelular' => '+58123456',
            'direccion' => 'direccion REPRESENTANTE',
            'email' => 'alumno1@gmail.com',
            'password' => bcrypt('123456'),
            'type' => 'REPRESENTANTE',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
