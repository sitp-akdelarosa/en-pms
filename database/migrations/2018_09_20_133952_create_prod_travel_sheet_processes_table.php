<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProdTravelSheetProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prod_travel_sheet_processes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('travel_sheet_id')->default(0);
            $table->integer('unprocessed')->default(0);
            $table->integer('good')->default(0);
            $table->integer('rework')->default(0);
            $table->integer('scrap')->default(0);
            $table->integer('convert')->default(0);
            $table->integer('alloy_mix')->default(0);
            $table->integer('nc')->default(0);
            $table->string('process');
            $table->integer('sequence')->default(0);
            $table->string('previous_process')->nullable();
            $table->string('div_code');
            $table->string('operator')->nullable();
            $table->string('leader')->nullable();
            $table->integer('leader_id')->default(0)->nullable();
            $table->string('machine_no')->nullable();
            $table->integer('status')->default(0);
            $table->integer('create_user')->default(0);
            $table->integer('update_user')->default(0);
            $table->dateTime('end_date')->nullable();
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
        Schema::dropIfExists('prod_travel_sheet_processes');
    }
}
