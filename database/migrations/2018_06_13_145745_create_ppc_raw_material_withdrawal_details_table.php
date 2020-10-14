<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePpcRawMaterialWithdrawalDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppc_raw_material_withdrawal_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('trans_id')->default(0);
            $table->string('mat_code');
            $table->string('alloy');
            $table->string('item');
            $table->string('size');
            $table->string('schedule')->nullable();
            $table->string('lot_no')->nullable();
            $table->string('material_heat_no')->nullable();
            $table->text('sc_no')->nullable();
            $table->string('remarks')->nullable();
            $table->double('issued_qty',20,2)->default(0.00);
            $table->double('needed_qty',20,2)->default(0.00);
            $table->double('returned_qty',20,2)->default(0.00);
            $table->double('scheduled_qty',20,2)->default(0.00);
            $table->string('issued_uom')->default('N/A');
            $table->string('needed_uom')->default('N/A');
            $table->string('returned_uom')->default('N/A');
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
        Schema::dropIfExists('ppc_raw_material_withdrawal_details');
    }
}
