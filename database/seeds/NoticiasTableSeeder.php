<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class NoticiasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        for($i=0;$i<=10;$i++)
        {
            DB::table('noticias')->insert([
                'idUser'=> 1,
                'title' => 'noticia seeder',
                'content' => 'contenido de noticia.',
                'image' => 'images/default.png',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);

        }
    }
}
