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
            $table->string('sched_qty');
            $table->string('unprocessed');
            $table->string('good');
            $table->string('rework');
            $table->string('scrap');
            $table->string('total_output');
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
