<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProdTransferItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prod_transfer_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('jo_no')->nullable();
            $table->string('prod_order_no')->nullable();
            $table->string('prod_code')->nullable();
            $table->string('description')->nullable();
            $table->string('current_process')->nullable();
            $table->string('div_code')->nullable();
            $table->string('process')->nullable();
            $table->integer('qty')->default(0);
            $table->string('status')->nullable();
            $table->string('remarks')->nullable();
            $table->integer('item_status')->default(0);
            $table->integer('receive_qty')->default(0);
            $table->text('receive_remarks')->nullable();
            $table->string('create_user');
            $table->string('update_user');
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
        Schema::dropIfExists('prod_transfer_items');
    }
}
