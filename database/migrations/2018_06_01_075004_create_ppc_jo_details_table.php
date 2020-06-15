<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreatePpcJoDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppc_jo_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('jo_summary_id');
            $table->string('sc_no');
            $table->string('product_code');
            $table->string('description');
            $table->integer('back_order_qty');
            $table->integer('sched_qty');
            $table->string('material_used');
            $table->string('material_heat_no');
            $table->string('lot_no');
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
        Schema::dropIfExists('ppc_jo_details');
    }
}
