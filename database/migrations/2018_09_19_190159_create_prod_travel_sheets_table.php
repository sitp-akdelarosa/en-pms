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
            $table->double('order_qty',20,2)->default(0.00);
            $table->double('issued_qty',20,2)->default(0.00);
            $table->double('total_issued_qty',20,2)->default(0.00);
            $table->double('status',20,2)->default(0.00);
            $table->string('iso_code');
            $table->string('iso_name');
            $table->string('iso_photo');
            $table->integer('pre_travel_sheet_id')->default(0);
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
        Schema::dropIfExists('prod_travel_sheets');
    }
}
