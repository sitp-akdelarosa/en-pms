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
            $table->string('email')->unique();
            $table->string('password');
            $table->string('actual_password');
            $table->string('div_code');
            $table->string('user_type')->default('PPC');
            $table->string('user_category')->default('OFFICE');
            $table->string('photo')->default('images/default-profile.png');
            $table->integer('is_admin')->default(0);
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
            'user_type' => 'Administrator',
            'user_category' => 'OFFICE',
            'photo' => '/images/default-profile.png',
            'is_admin' => 1,
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
