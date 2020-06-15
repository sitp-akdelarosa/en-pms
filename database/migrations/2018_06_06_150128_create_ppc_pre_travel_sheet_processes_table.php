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
            $table->integer('pre_travel_sheet_id');
            $table->string('jo_no');
            $table->string('set');
            $table->string('process_name');
            $table->integer('sequence');
            $table->string('div_code');
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
        Schema::dropIfExists('ppc_pre_travel_sheet_processes');
    }
}
