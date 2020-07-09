<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePpcJoDetailsSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppc_jo_details_summaries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('jo_no');
            $table->integer('total_sched_qty');
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
        Schema::dropIfExists('ppc_jo_details_summaries');
    }
}
