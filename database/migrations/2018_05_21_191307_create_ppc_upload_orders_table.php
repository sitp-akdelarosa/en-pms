<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePpcUploadOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppc_upload_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sc_no');
            $table->string('prod_code');
            $table->string('description');
            $table->integer('quantity');
            $table->string('unit')->nullable();
            $table->integer('sched_qty')->nullable();
            $table->string('po');
            $table->date('date_upload');
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
        Schema::dropIfExists('ppc_upload_orders');
    }
}
