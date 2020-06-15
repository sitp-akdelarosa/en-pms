<?php

use Illuminate\Database\Seeder;
use App\AdminModuleAccess;

class AdminModuleAccessTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AdminModuleAccess::create([
        	'code' => 'A0001',
			'title' => 'User Master',
			'category' => 'Administrator',
			'user_category' => 'Administrator',
			'user_id' => '1',
			'access' => '1',
			'create_user' => 'Administrator',
			'update_user' => 'Administrator',
        ]);

        AdminModuleAccess::create([
        	'code' => 'A0002',
			'title' => 'Assign Production Line',
			'category' => 'Administrator',
			'user_category' => 'Administrator',
			'user_id' => '1',
			'access' => '1',
			'create_user' => 'Administrator',
			'update_user' => 'Administrator',
        ]);

        AdminModuleAccess::create([
        	'code' => 'A0003',
			'title' => 'User Type',
			'category' => 'Administrator',
			'user_category' => 'Administrator',
			'user_id' => '1',
			'access' => '1',
			'create_user' => 'Administrator',
			'update_user' => 'Administrator',
        ]);

        AdminModuleAccess::create([
        	'code' => 'A0004',
			'title' => 'Audit Trail',
			'category' => 'Administrator',
			'user_category' => 'Administrator',
			'user_id' => '1',
			'access' => '1',
			'create_user' => 'Administrator',
			'update_user' => 'Administrator',
        ]);

        AdminModuleAccess::create([
        	'code' => 'A0005',
			'title' => 'Settings',
			'category' => 'Administrator',
			'user_category' => 'Administrator',
			'user_id' => '1',
			'access' => '1',
			'create_user' => 'Administrator',
			'update_user' => 'Administrator',
        ]);
    }
}
