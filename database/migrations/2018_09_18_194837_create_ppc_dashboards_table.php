<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePpcDashboardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppc_dashboards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('item_code');
            $table->string('description');
            $table->string('plant');
            $table->string('alloy');
            $table->string('size');
            $table->string('process');
            $table->string('class');
            $table->string('mats');
            $table->string('heat_no');
            $table->string('lot_no');
            $table->integer('order_qty');
            $table->integer('sched_qty');
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
        Schema::dropIfExists('ppc_dashboards');
    }
}
