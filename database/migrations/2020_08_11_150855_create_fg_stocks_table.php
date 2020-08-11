<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFgStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fg_stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('jo_no');
            $table->string('prod_code');
            $table->string('description');
            $table->double('order_qty',20,2)->default(0.00);
            $table->double('qty',20,2)->default(0.00);
            $table->integer('status')->default(0);
            $table->integer('create_user')->default(0);
            $table->integer('update_user')->default(0);
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
        Schema::dropIfExists('fg_stocks');
    }
}
