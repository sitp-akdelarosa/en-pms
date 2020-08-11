<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePpcPreTravelSheetProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppc_pre_travel_sheet_processes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pre_travel_sheet_id')->default(0);
            $table->string('jo_no');
            $table->string('set');
            $table->string('process_name');
            $table->integer('sequence')->default(0);
            $table->string('div_code');
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
        Schema::dropIfExists('ppc_pre_travel_sheet_processes');
    }
}
