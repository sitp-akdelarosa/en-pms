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
            $table->double('quantity')->default(0.00);
            $table->string('unit')->nullable();
            $table->double('sched_qty')->default(0.00)->nullable();
            $table->string('po');
            $table->integer('uploader')->length(8);
            $table->date('date_upload');
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
        Schema::dropIfExists('ppc_upload_orders');
    }
}
