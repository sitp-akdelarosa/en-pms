<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProdDashboardSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prod_dashboard_summaries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('product_code');
            $table->string('description');
            $table->double('sched_qty',20,2)->default(0.00);
            $table->integer('unprocessed')->length(0)->default(0);
            $table->integer('good')->length(0)->default(0);
            $table->integer('rework')->length(0)->default(0);
            $table->integer('scrap')->length(0)->default(0);
            $table->integer('total_output')->length(0)->default(0);
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
        Schema::dropIfExists('prod_dashboard_summaries');
    }
}
