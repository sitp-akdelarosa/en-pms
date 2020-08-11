<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\AdminUserType;

class CreateAdminUserTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_user_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->string('category');
            $table->integer('del_flag')->length(1)->default(0);
            $table->integer('create_user')->length(10)->default(0);
            $table->integer('update_user')->length(10)->default(0);
            $table->timestamps();
        });

        AdminUserType::create([
            'description' => 'SYSTEM ADMIN',
            'category' => 'ALL'
        ]);

        AdminUserType::create([
            'description' => 'PPC',
            'category' => 'OFFICE'
        ]);

        AdminUserType::create([
            'description' => 'LINE LEADER',
            'category' => 'PRODUCTION'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_user_types');
    }
}
