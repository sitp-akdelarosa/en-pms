<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePpcCuttingSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppc_cutting_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('iso_control_no');
            $table->string('withdrawal_slip_no')->nullable();
            $table->date('date_issued');
            $table->string('machine_no');
            $table->string('prepared_by');
            $table->string('leader');
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
        Schema::dropIfExists('ppc_cutting_schedules');
    }
}
