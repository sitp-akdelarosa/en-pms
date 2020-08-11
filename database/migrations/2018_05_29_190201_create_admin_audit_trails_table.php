<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminAuditTrailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::create('admin_audit_trails', function (Blueprint $table) {
                $table->increments('id');
                $table->string('user_type');
                $table->integer('module_id')->default(0);
                $table->string('module');
                $table->text('action');
                $table->integer('user')->length(8)->default(0);
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
        Schema::dropIfExists('admin_audit_trails');
    }
}
