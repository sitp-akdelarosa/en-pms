<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePpcJoTravelSheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppc_jo_travel_sheets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('jo_no');
            $table->string('sc_no');
            $table->string('prod_code');
            $table->string('description');
            $table->integer('order_qty');
            $table->integer('sched_qty');
            $table->integer('issued_qty');
            $table->string('material_used');
            $table->string('material_heat_no');
            $table->string('lot_no');
            $table->integer('status');
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
        Schema::dropIfExists('ppc_jo_travel_sheets');
    }
}
