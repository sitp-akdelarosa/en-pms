<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProdTravelSheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prod_travel_sheets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('jo_no');
            $table->string('jo_sequence');
            $table->string('prod_order_no');
            $table->string('prod_code');
            $table->string('description');
            $table->string('material_used');
            $table->string('material_heat_no');
            $table->string('lot_no');
            $table->string('type');
            $table->integer('order_qty')->default(0);
            $table->integer('issued_qty')->default(0);
            $table->integer('total_issued_qty')->default(0);
            $table->integer('status')->default(0);
            $table->string('iso_code');
            $table->string('iso_name');
            $table->string('iso_photo');
            $table->integer('pre_travel_sheet_id');
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
        Schema::dropIfExists('prod_travel_sheets');
    }
}
