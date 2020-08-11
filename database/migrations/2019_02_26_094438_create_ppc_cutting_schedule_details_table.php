<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePpcCuttingScheduleDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppc_cutting_schedule_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cutt_id')->default(0);
            $table->string('item_no');
            $table->string('alloy');
            $table->string('size');
            $table->string('item');
            $table->string('class');
            $table->string('cut_weight');
            $table->string('cut_length');
            $table->string('schedule');
            $table->string('qty_needed_inbox');
            $table->string('sc_no');
            $table->double('order_qty',20,2)->default(0.00);
            $table->double('qty_needed',20,2)->default(0.00);
            $table->double('qty_cut',20,2)->default(0.00);
            $table->double('plate_qty',20,2)->default(0.00);
            $table->string('material_desc_item');
            $table->string('material_desc_size');
            $table->string('material_desc_heat_no');
            $table->string('material_desc_lot_no');
            $table->string('material_desc_supplier_heat_no');
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
        Schema::dropIfExists('ppc_cutting_schedule_details');
    }
}
