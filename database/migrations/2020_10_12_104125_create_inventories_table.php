<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('materials_type');
            $table->string('materials_code');
            $table->string('description')->nullable();
            $table->string('item')->nullable();
            $table->string('alloy')->nullable();
            $table->string('schedule')->nullable();
            $table->string('size')->nullable();
            $table->string('width')->nullable();
            $table->string('length')->nullable();
            $table->double('orig_quantity',20,2)->default(0.00);
            $table->double('quantity',20,2)->default(0.00);
            $table->string('uom')->nullable();
            $table->string('heat_no')->nullable();
            $table->string('invoice_no')->nullable();
            $table->dateTime('received_date');
            $table->string('supplier');
            $table->string('supplier_heat_no');
            $table->integer('received_id')->default(0);
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
        Schema::dropIfExists('inventories');
    }
}
