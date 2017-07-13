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
            'idDocument'=> 123456,
            'name' => 'oscar',
            'email' => 'oscar.marquez.to@gmail.com',
            'password' => bcrypt('123456'),
            'type' => 'ADMIN',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
