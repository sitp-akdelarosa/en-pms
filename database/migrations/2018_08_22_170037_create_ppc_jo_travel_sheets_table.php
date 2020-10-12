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
            $table->double('order_qty',20,2);
            $table->double('sched_qty',20,2);
            $table->double('issued_qty',20,2);
            $table->string('material_used');
            $table->string('material_heat_no');
            $table->string('uom');
            $table->string('lot_no');
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('ppc_jo_travel_sheets');
    }
}
