<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminUserTypeModulesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('admin_user_type_modules', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_type_id')->length(5)->default(0);
			$table->integer('module_id')->length(5)->default(0);
			$table->string('code')->length(5);
			$table->string('user_category')->length(100);
			$table->integer('create_user')->length(10)->default(0);
			$table->integer('update_user')->length(10)->default(0);
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
		Schema::dropIfExists('admin_user_type_modules');
	}
}
