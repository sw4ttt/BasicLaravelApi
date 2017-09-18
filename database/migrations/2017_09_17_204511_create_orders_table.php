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
//'articulos',
//'reference',
//'payu_order_id',
//'transaction_id',
//'state',
//'value',
//'user_id'
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');

            $table->string('reference');
            $table->string('payu_order_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('state');
            $table->string('value');
            $table->string('user_id');
            $table->text('articulos');

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
