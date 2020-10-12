<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\AdminUserCategory;

class CreateAdminUserCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_user_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->integer('create_user')->default(0);
            $table->integer('update_user')->default(0);
            $table->timestamps();
        });

        AdminUserCategory::create([
                            'description' => 'OFFICE',
                            'create_user' => 1,
                            'update_user' => 1,
                        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_user_categories');
    }
}
