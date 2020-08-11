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
            $table->integer('leader_id')->length(10)->nullable();
            $table->integer('is_disable')->length(1)->default(0);
            $table->integer('user_id')->default(0);
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
        Schema::dropIfExists('ppc_divisions');
    }
}
