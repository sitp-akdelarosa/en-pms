<?php

use Illuminate\Database\Seeder;
use App\PpcDropdownName;

class PpcDropdownNameTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PpcDropdownName::create([
        	'dropdown_name' => 'Process',
			'create_user' => 'System Administrator',
			'update_user' => 'System Administrator',
        ]);

        PpcDropdownName::create([
        	'dropdown_name' => 'Process Set',
			'create_user' => 'System Administrator',
			'update_user' => 'System Administrator',
        ]);

        PpcDropdownName::create([
        	'dropdown_name' => 'Alloy',
			'create_user' => 'System Administrator',
			'update_user' => 'System Administrator',
        ]);

        PpcDropdownName::create([
        	'dropdown_name' => 'Schedule',
			'create_user' => 'System Administrator',
			'update_user' => 'System Administrator',
        ]);

        PpcDropdownName::create([
        	'dropdown_name' => 'Size',
			'create_user' => 'System Administrator',
			'update_user' => 'System Administrator',
        ]);

        PpcDropdownName::create([
        	'dropdown_name' => 'Supplier',
			'create_user' => 'System Administrator',
			'update_user' => 'System Administrator',
        ]);

        PpcDropdownName::create([
        	'dropdown_name' => 'Item Type',
			'create_user' => 'System Administrator',
			'update_user' => 'System Administrator',
        ]);

        PpcDropdownName::create([
            'dropdown_name' => 'Production Line',
            'create_user' => 'System Administrator',
            'update_user' => 'System Administrator',
        ]);
    }
}
