<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePpcProductWithdrawalInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppc_product_withdrawal_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('trans_no');
            $table->string('status')->default('O')->length(1); // O = OPEN; X = CLOSE; C = CANCEL
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
        Schema::dropIfExists('ppc_product_withdrawal_infos');
    }
}
