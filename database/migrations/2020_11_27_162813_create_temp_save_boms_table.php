<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempSaveBomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_save_boms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('upd_inv_id')->default(0)->nullable();
            $table->integer('inv_id')->default(0)->nullable();
            $table->integer('rmwd_id')->default(0)->nullable();
            $table->double('size',20,2)->default(0.00)->nullable();
            $table->double('computed_per_piece')->default(0.00)->nullable();
            $table->string('material_type')->nullable();
            $table->double('sched_qty',20,2)->default(0.00)->nullable();
            $table->string('material_heat_no')->nullable();
            $table->double('rmw_issued_qty',20,2)->default(0.00)->nullable();
            $table->string('material_used')->nullable();
            $table->string('lot_no')->nullable();
            $table->double('blade_consumption',20,2)->default(0.00)->nullable();
            $table->double('cut_weight',20,2)->default(0.00)->nullable();
            $table->double('cut_length',20,2)->default(0.00)->nullable();
            $table->double('cut_width',20,2)->default(0.00)->nullable();
            $table->double('mat_length',20,2)->default(0.00)->nullable();
            $table->double('mat_weight',20,2)->default(0.00)->nullable();
            $table->double('assign_qty',20,2)->default(0.00)->nullable();
            $table->double('remaining_qty',20,2)->default(0.00)->nullable();
            $table->string('rmw_no')->nullable();
            $table->date('ship_date')->nullable();
            $table->string('sc_no')->nullable();
            $table->string('prod_code')->nullable();
            $table->string('description')->nullable();
            $table->double('quantity',20,2)->default(0.00)->nullable();
            $table->integer('create_user')->default(0)->nullable();
            $table->integer('sc_id')->default(0)->nullable();
            $table->integer('ref_id')->default(0)->nullable();
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
        Schema::dropIfExists('temp_save_boms');
    }
}
