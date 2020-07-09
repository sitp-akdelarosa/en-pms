<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePpcPreTravelSheetProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppc_pre_travel_sheet_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pre_travel_sheet_id');
            $table->string('jo_no');
            $table->string('prod_code');
            $table->integer('issued_qty_per_sheet');
            $table->string('sc_no');
            $table->string('jo_sequence');
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
        Schema::dropIfExists('ppc_pre_travel_sheet_products');
    }
}
