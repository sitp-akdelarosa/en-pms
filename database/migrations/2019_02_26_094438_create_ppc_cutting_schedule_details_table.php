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
            $table->string('item_no')->nullable()->default('');
            $table->string('alloy')->nullable()->default('');
            $table->string('size')->nullable()->default('');
            $table->string('item')->nullable()->default('');
            $table->string('class')->nullable()->default('');
            $table->string('cut_weight')->nullable()->default('');
            $table->string('cut_length')->nullable()->default('');
            $table->string('schedule')->nullable()->default('');
            $table->string('qty_needed_inbox')->nullable()->default('');
            $table->string('sc_no')->nullable()->default('');
            $table->double('order_qty',20,2)->default(0.00);
            $table->double('qty_needed',20,2)->default(0.00);
            $table->double('qty_cut',20,2)->default(0.00);
            $table->double('plate_qty',20,2)->default(0.00);
            $table->string('material_desc_item')->nullable()->default('');
            $table->string('material_desc_size')->nullable()->default('');
            $table->string('material_desc_heat_no')->nullable()->default('');
            $table->string('material_desc_lot_no')->nullable()->default('');
            $table->string('material_desc_supplier_heat_no')->nullable()->default('');
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
        Schema::dropIfExists('ppc_cutting_schedule_details');
    }
}
