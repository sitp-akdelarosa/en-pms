<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\AdminTransactionNo;

class CreateAdminTransactionNosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_transaction_nos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('description');
            $table->string('prefix')->nullable();
            $table->string('prefixformat')->nullable();
            $table->integer('nextno')->default(1);
            $table->integer('nextnolength')->default(5);
            $table->string('month')->default(01);
            $table->integer('assigned_user_id')->default(0);
            $table->integer('create_user')->default(0);
            $table->integer('update_user')->default(0);
            $table->timestamps();
        });

        AdminTransactionNo::create([
                            'code' => 'RMW',
                            'description' => 'Raw Material Withdrawal Transaction No.',
                            'prefix' => 'RMW-YYMM-',
                            'prefixformat' => 'RMW-%y%m-',
                            'nextno' => 1,
                            'nextnolength' => 4,
                            'month' => '01',
                            'assigned_user_id' => 1,
                            'create_user' => 1,
                            'update_user' => 1,
                        ]);

        AdminTransactionNo::create([
                            'code' => 'JO',
                            'description' => 'Job Order No.',
                            'prefix' => 'JO-YYMM-',
                            'prefixformat' => 'JO-%y%m-',
                            'nextno' => 1,
                            'nextnolength' => 3,
                            'month' => '01',
                            'assigned_user_id' => 1,
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
        Schema::dropIfExists('admin_transaction_nos');
    }
}
