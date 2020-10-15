<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePpcMaterialCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppc_material_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('material_type');
            $table->string('material_code');
            $table->string('code_description');
            $table->string('item');
            $table->string('alloy');
            $table->string('schedule');
            $table->string('size');
            $table->double('std_weight',20,2)->default(0.00);
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
        Schema::dropIfExists('ppc_material_codes');
    }
}
