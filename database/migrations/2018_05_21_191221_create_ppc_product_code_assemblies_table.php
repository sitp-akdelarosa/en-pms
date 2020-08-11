<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePpcProductCodeAssembliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppc_product_code_assemblies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('prod_type');
            $table->string('character_num');
            $table->string('character_code');
            $table->string('description');
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
        Schema::dropIfExists('ppc_product_code_assemblies');
    }
}
