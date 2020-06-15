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
            $table->string('sched_qty');
            $table->string('unprocessed');
            $table->string('good');
            $table->string('rework');
            $table->string('scrap');
            $table->string('total_output');
            $table->string('requested_date');
            $table->string('end_date');
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
