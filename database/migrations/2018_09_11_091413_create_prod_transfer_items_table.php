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
            $table->double('qty',20,2)->default(0.00);
            $table->string('status')->nullable();
            $table->string('remarks')->nullable();
            $table->integer('item_status')->default(0);
            $table->integer('deleted')->default(0)->length(1);
            $table->double('receive_qty',20,2)->default(0.00);
            $table->text('receive_remarks')->nullable();
            $table->dateTime('date_received');
            $table->dateTime('date_transfered');
            $table->integer('create_user')->default(0);
            $table->integer('update_user')->default(0);
            $table->integer('delete_user')->default(0);
            $table->dateTime('deleted_at')->nullable();
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
