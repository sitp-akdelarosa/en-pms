<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePpcPreTravelSheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppc_pre_travel_sheets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('jo_no');
            $table->string('prod_code');
            $table->double('issued_qty',20,2)->default(0.00);
            $table->double('qty_per_sheet',20,2)->default(0.00);
            $table->integer('status')->default(0);
            $table->string('iso_code');
            $table->string('iso_name');
            $table->string('iso_photo');
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
        Schema::dropIfExists('ppc_pre_travel_sheets');
    }
}
