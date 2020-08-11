<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePpcTempUploadOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppc_temp_upload_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sc_no');
            $table->string('prod_code');
            $table->string('description');
            $table->double('quantity',20,2)->default(0.00);;
            $table->string('po');
            $table->integer('uploader')->length(8)->default(0);
            $table->dateTime('date_upload');
            $table->string('create_user')->default(0);
            $table->string('update_user')->default(0);
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
        Schema::dropIfExists('ppc_temp_upload_orders');
    }
}
