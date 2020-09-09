<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProdProductionOutputsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prod_production_outputs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('travel_sheet_id')->default(0);
            $table->integer('travel_sheet_process_id')->default(0);
            $table->string('jo_no')->nullable();
            $table->integer('unprocessed')->default(0);
            $table->integer('good')->default(0);
            $table->integer('rework')->default(0);
            $table->integer('scrap')->default(0);
            $table->integer('convert')->default(0);
            $table->integer('alloy_mix')->default(0);
            $table->integer('nc')->default(0);
            $table->integer('deleted')->default(0)->length(1);
            $table->string('previous_process')->nullable();
            $table->string('current_process')->nullable();
            $table->integer('output')->default(0);
            $table->string('operator')->nullable();
            $table->string('machine_no')->nullable();
            $table->integer('create_user')->default(0);
            $table->integer('update_user')->default(0);
            $table->integer('delete_user')->default(0);
            $table->dateTime('deleted_at')->nullable();
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
        Schema::dropIfExists('prod_production_outputs');
    }
}
