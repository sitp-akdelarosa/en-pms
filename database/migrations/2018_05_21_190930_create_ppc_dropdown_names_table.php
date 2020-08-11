<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\PpcDropdownName;

class CreatePpcDropdownNamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppc_dropdown_names', function (Blueprint $table) {
            $table->increments('id');
            $table->string('dropdown_name');
            $table->integer('create_user')->default(0);
            $table->integer('update_user')->default(0);
            $table->timestamps();
        });

        $names = [
            'Process',
            'Alloy',
            'Schedule',
            'Size',
            'Supplier',
            'Item Type',
            'Production Line',
            'Material Type'
        ];

        foreach ($names as $key => $name) {
            PpcDropdownName::create([
                                'dropdown_name' => $name,
                                'create_user' => 1,
                                'update_user' => 1,
                            ]);
        }

        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ppc_dropdown_names');
    }
}
