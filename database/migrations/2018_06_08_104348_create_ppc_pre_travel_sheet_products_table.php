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
            $table->integer('pre_travel_sheet_id')->default(0);
            $table->string('jo_no');
            $table->string('prod_code');
            $table->double('issued_qty_per_sheet',20,2)->default(0.00);
            $table->string('sc_no');
            $table->string('jo_sequence')->default('');
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
        Schema::dropIfExists('ppc_pre_travel_sheet_products');
    }
}
