<?php

use Illuminate\Database\Seeder;
use App\PpcDropdownItem;

class PpcDropdownItemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PpcDropdownItem::create([
        	'dropdown_name_id' => 1,
			'dropdown_name' => 'Process',
			'dropdown_item' => 'Cutting',
			'create_user' => 'System Administrator',
			'update_user' => 'System Administrator',
        ]);

        PpcDropdownItem::create([
        	'dropdown_name_id' => 1,
			'dropdown_name' => 'Edge Grinding',
			'dropdown_item' => 'Cutting',
			'create_user' => 'System Administrator',
			'update_user' => 'System Administrator',
        ]);

        PpcDropdownItem::create([
        	'dropdown_name_id' => 1,
			'dropdown_name' => 'Process',
			'dropdown_item' => 'Forging',
			'create_user' => 'System Administrator',
			'update_user' => 'System Administrator',
        ]);

        PpcDropdownItem::create([
        	'dropdown_name_id' => 1,
			'dropdown_name' => 'Process',
			'dropdown_item' => 'Heat Treatment',
			'create_user' => 'System Administrator',
			'update_user' => 'System Administrator',
        ]);

        PpcDropdownItem::create([
        	'dropdown_name_id' => 1,
			'dropdown_name' => 'Process',
			'dropdown_item' => 'Initial Sandblast',
			'create_user' => 'System Administrator',
			'update_user' => 'System Administrator',
        ]);

        PpcDropdownItem::create([
        	'dropdown_name_id' => 1,
			'dropdown_name' => 'Process',
			'dropdown_item' => 'Surface Grind',
			'create_user' => 'System Administrator',
			'update_user' => 'System Administrator',
        ]);

        PpcDropdownItem::create([
        	'dropdown_name_id' => 1,
			'dropdown_name' => 'Process',
			'dropdown_item' => 'Magnetic Test',
			'create_user' => 'System Administrator',
			'update_user' => 'System Administrator',
        ]);

        PpcDropdownItem::create([
        	'dropdown_name_id' => 1,
			'dropdown_name' => 'Process',
			'dropdown_item' => 'Final Blast',
			'create_user' => 'System Administrator',
			'update_user' => 'System Administrator',
        ]);

        PpcDropdownItem::create([
        	'dropdown_name_id' => 1,
			'dropdown_name' => 'Process',
			'dropdown_item' => 'QC',
			'create_user' => 'System Administrator',
			'update_user' => 'System Administrator',
        ]);

        PpcDropdownItem::create([
        	'dropdown_name_id' => 1,
			'dropdown_name' => 'Process',
			'dropdown_item' => 'Washing',
			'create_user' => 'System Administrator',
			'update_user' => 'System Administrator',
        ]);

        PpcDropdownItem::create([
        	'dropdown_name_id' => 1,
			'dropdown_name' => 'Packing',
			'dropdown_item' => 'QC',
			'create_user' => 'System Administrator',
			'update_user' => 'System Administrator',
        ]);

        PpcDropdownItem::create([
        	'dropdown_name_id' => 2,
			'dropdown_name' => 'Process Set',
			'dropdown_item' => 'Default',
			'create_user' => 'System Administrator',
			'update_user' => 'System Administrator',
        ]);

        PpcDropdownItem::create([
        	'dropdown_name_id' => 2,
			'dropdown_name' => 'Process Set',
			'dropdown_item' => 'BOM 2',
			'create_user' => 'System Administrator',
			'update_user' => 'System Administrator',
        ]);

        PpcDropdownItem::create([
        	'dropdown_name_id' => 2,
			'dropdown_name' => 'Process Set',
			'dropdown_item' => 'BOM 3',
			'create_user' => 'System Administrator',
			'update_user' => 'System Administrator',
        ]);

        PpcDropdownItem::create([
        	'dropdown_name_id' => 2,
			'dropdown_name' => 'Process Set',
			'dropdown_item' => 'Special BOM',
			'create_user' => 'System Administrator',
			'update_user' => 'System Administrator',
        ]);

    }
}
