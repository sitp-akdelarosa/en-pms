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
            $table->double('order_qty',20,2)->default(0.00);
            $table->double('sched_qty',20,2)->default(0.00);
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
        Schema::dropIfExists('ppc_dashboards');
    }
}
