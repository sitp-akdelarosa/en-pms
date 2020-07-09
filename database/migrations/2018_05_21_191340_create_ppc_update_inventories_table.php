<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePpcUpdateInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppc_update_inventories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('materials_type');
            $table->string('materials_code');
            $table->string('description')->nullable();
            $table->string('item')->nullable();
            $table->string('alloy')->nullable();
            $table->string('schedule')->nullable();
            $table->string('size')->nullable();
            $table->string('weight')->nullable();
            $table->string('length')->nullable();
            $table->integer('quantity');
            $table->string('uom')->nullable();
            $table->string('heat_no')->nullable();
            $table->string('invoice_no')->nullable();
            $table->date('received_date');
            $table->string('supplier');
            $table->string('supplier_heat_no');
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
        Schema::dropIfExists('ppc_update_inventories');
    }
}
