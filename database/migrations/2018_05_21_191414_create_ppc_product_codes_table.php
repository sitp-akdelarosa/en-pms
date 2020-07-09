<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePpcProductCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppc_product_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('product_type');
            $table->string('product_code');
            $table->string('code_description');
            $table->double('cut_weight',20,2);
            $table->string('cut_weight_uom');
            $table->double('cut_length',20,2);
            $table->string('cut_length_uom');
            $table->string('item');
            $table->string('class');
            $table->string('alloy');
            $table->string('size');
            $table->string('standard_material_used');
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
        Schema::dropIfExists('ppc_product_codes');
    }
}
