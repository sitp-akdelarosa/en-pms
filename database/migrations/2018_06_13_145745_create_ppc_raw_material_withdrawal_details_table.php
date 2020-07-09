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
            $table->integer('trans_id');
            $table->string('mat_code');
            $table->string('alloy');
            $table->string('item');
            $table->string('size');
            $table->string('schedule')->nullable();
            $table->string('lot_no')->nullable();
            $table->string('material_heat_no')->nullable();
            $table->text('sc_no')->nullable();
            $table->string('remarks')->nullable();
            $table->integer('issued_qty')->default(0);
            $table->integer('needed_qty')->default(0);
            $table->integer('returned_qty')->default(0);
            $table->string('issued_uom')->default('N/A');
            $table->string('needed_uom')->default('N/A');
            $table->string('returned_uom')->default('N/A');
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
        Schema::dropIfExists('ppc_raw_material_withdrawal_details');
    }
}
