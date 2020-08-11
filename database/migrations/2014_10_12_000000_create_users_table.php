<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\User;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email')->unique()->nullable();
            $table->string('password');
            $table->string('actual_password');
            $table->string('div_code')->nullable();
            $table->integer('user_type');
            $table->string('user_category')->default(0);
            $table->string('photo')->default('images/default-profile.png');
            $table->integer('is_admin')->default(0);
            $table->integer('is_superadmin')->default(0);
            $table->integer('del_flag')->default(0);
            $table->integer('create_user')->default(0);
            $table->integer('update_user')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });

        User::create([
            'user_id' => 'akdelarosa',
            'firstname' => 'Kurt',
            'lastname' => 'Dela Rosa',
            'email' => 'ak.delarosa@seiko-it.com.ph',
            'password' => '$2y$10$IOJMSaeoJVM0m1mEx.38Lu9Ds4zhCeCUPrAiXah/nJqfqBT9bce9i',
            'actual_password' => 'admin01',
            'div_code' => 'CS1',
            'user_type' => 1,
            'user_category' => 'Administrator',
            'photo' => '/images/default-profile.png',
            'is_admin' => 1,
            'is_superadmin' => 1,
            'create_user' => 1,
            'update_user' => 1
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
