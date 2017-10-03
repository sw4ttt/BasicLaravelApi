<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
//'idUsuario',
//'articulos',
//'recibo',
//'ref_payco',
//'documento',
//'factura',
//'estado',
//'valor',
//'nombre',
//'apellido',
//'email'
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('idUsuario');
            $table->text('articulos');

            $table->string('recibo')->nullable();
            $table->string('ref_payco')->nullable();
            $table->string('documento')->nullable();
            $table->string('factura')->nullable();

            $table->string('estado');
            $table->string('valor');
            $table->string('nombre');
            $table->string('apellido');
            $table->string('email');

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
        Schema::dropIfExists('orders');
    }
}
