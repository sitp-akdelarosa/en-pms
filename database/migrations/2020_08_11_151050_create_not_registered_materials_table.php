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
            $table->string('materials_type');
            $table->string('materials_code');
            $table->double('quantity',20,2)->default(0.00);
            $table->string('uom');
            $table->double('qty_weight',20,2)->default(0.00);
            $table->double('qty_pcs',20,2)->default(0.00);
            $table->string('heat_no');
            $table->string('invoice_no');
            $table->date('received_date');
            $table->string('supplier');
            $table->string('width');
            $table->string('length');
            $table->string('supplier_heat_no');
            $table->string('thickness');
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
        Schema::dropIfExists('not_registered_materials');
    }
}
