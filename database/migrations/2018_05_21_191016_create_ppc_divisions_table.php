<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePpcDivisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppc_divisions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('div_code');
            $table->string('div_name');
            $table->string('plant');
            $table->string('leader');
            $table->string('is_disable');
            $table->integer('user_id');
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
        Schema::dropIfExists('ppc_divisions');
    }
}
