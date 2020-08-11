<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotRegisteredMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('not_registered_materials', function (Blueprint $table) {
            $table->increments('id');
            $table->string('materials_code');
            $table->double('quantity',20,2);
            $table->string('uom');
            $table->string('heat_no');
            $table->string('invoice_no');
            $table->date('received_date');
            $table->string('supplier');
            $table->string('width')->nullable()->default('N/A');
            $table->string('length')->nullable()->default('N/A');
            $table->string('supplier_heat_no')->nullable()->default('N/A');
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
        Schema::dropIfExists('not_registered_materials');
    }
}
