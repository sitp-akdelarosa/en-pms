<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePpcProductWithdrawalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppc_product_withdrawals', function (Blueprint $table) {
            $table->increments('id');
            $table->string('trans_no');
            $table->string('item_class');
            $table->string('item_code');
            $table->string('jo_no')->nullable();
            $table->string('lot_no')->nullable();
            $table->string('heat_no')->nullable();
            $table->string('alloy')->nullable();
            $table->string('item')->nullable();
            $table->string('size')->nullable();
            $table->string('schedule')->nullable();
            $table->string('sc_no')->nullable();
            $table->string('remarks')->nullable();
            $table->double('issued_qty',20,2)->default(0.00);
            $table->string('issued_uom')->default('N/A');
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
        Schema::dropIfExists('ppc_product_withdrawals');
    }
}
