<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotRegisteredProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('not_registered_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sc_no');
            $table->string('prod_code');
            $table->double('quantity',20,2)->default(0.00);
            $table->string('po');
            $table->integer('uploader')->length(8)->default(0);
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
        Schema::dropIfExists('not_registered_products');
    }
}
