<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProdDashboardDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prod_dashboard_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('job_order_no');
            $table->string('product_code');
            $table->string('description');
            $table->double('sched_qty',20,2)->default(0.00);
            $table->integer('unprocessed')->default(0)->length(10);
            $table->integer('good')->default(0)->length(10);
            $table->integer('rework')->default(0)->length(10);
            $table->integer('scrap')->default(0)->length(10);
            $table->integer('total_output')->default(0)->length(10);
            $table->date('requested_date');
            $table->date('end_date');
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
        Schema::dropIfExists('prod_dashboard_details');
    }
}
