<?php

use Illuminate\Database\Seeder;
use App\AdminModule;

class AdminModuleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AdminModule::create([
        	'code' => 'M0001',
			'title' => 'Division Master',
			'category' => 'System Maintenance',
			'user_category' => 'OFFICE',
			'description' => '',
        ]);

        AdminModule::create([
        	'code' => 'M0002',
			'title' => 'Dropdown Master',
			'category' => 'System Maintenance',
			'user_category' => 'OFFICE',
			'description' => '',
        ]);

        AdminModule::create([
        	'code' => 'M0003',
			'title' => 'Product Master',
			'category' => 'System Maintenance',
			'user_category' => 'OFFICE',
			'description' => '',
        ]);

        AdminModule::create([
        	'code' => 'M0004',
			'title' => 'Material Master',
			'category' => 'System Maintenance',
			'user_category' => 'OFFICE',
			'description' => '',
        ]);

        AdminModule::create([
        	'code' => 'M0005',
			'title' => 'Process Master',
			'category' => 'System Maintenance',
			'user_category' => 'OFFICE',
			'description' => '',
        ]);

        AdminModule::create([
        	'code' => 'M0006',
			'title' => 'Operator Master',
			'category' => 'System Maintenance',
			'user_category' => 'OFFICE',
			'description' => '',
        ]);

        AdminModule::create([
        	'code' => 'T0001',
			'title' => 'Update Inventory',
			'category' => 'Transaction',
			'user_category' => 'OFFICE',
			'description' => '',
        ]);

        AdminModule::create([
        	'code' => 'T0002',
			'title' => 'Upload Orders',
			'category' => 'Transaction',
			'user_category' => 'OFFICE',
			'description' => '',
        ]);

        AdminModule::create([
        	'code' => 'T0003',
			'title' => 'Raw Material Withdrawal',
			'category' => 'Transaction',
			'user_category' => 'OFFICE',
			'description' => '',
        ]);

        AdminModule::create([
        	'code' => 'T0004',
			'title' => 'Production Schedule',
			'category' => 'Transaction',
			'user_category' => 'OFFICE',
			'description' => '',
        ]);

        AdminModule::create([
        	'code' => 'T0005',
			'title' => 'Cutting Schedule',
			'category' => 'Transaction',
			'user_category' => 'OFFICE',
			'description' => '',
        ]);

        AdminModule::create([
        	'code' => 'T0006',
			'title' => 'Travel Sheet',
			'category' => 'Transaction',
			'user_category' => 'OFFICE',
			'description' => '',
        ]);

        AdminModule::create([
        	'code' => 'T0007',
			'title' => 'Production Output',
			'category' => 'Transaction',
			'user_category' => 'PRODUCTION',
			'description' => '',
        ]);

        AdminModule::create([
        	'code' => 'T0008',
			'title' => 'Transfer Item',
			'category' => 'Transaction',
			'user_category' => 'PRODUCTION',
			'description' => '',
        ]);

        AdminModule::create([
        	'code' => 'R0001',
			'title' => 'Travel Sheet Status',
			'category' => 'Reports',
			'user_category' => 'OFFICE',
			'description' => '',
        ]);

        AdminModule::create([
        	'code' => 'R0002',
			'title' => 'Transfer Item',
			'category' => 'Reports',
			'user_category' => 'OFFICE',
			'description' => '',
        ]);

        AdminModule::create([
        	'code' => 'R0003',
			'title' => 'Summary Report',
			'category' => 'Reports',
			'user_category' => 'OFFICE',
			'description' => '',
        ]);

        AdminModule::create([
        	'code' => 'R0004',
			'title' => 'Operators Output',
			'category' => 'Reports',
			'user_category' => 'PRODUCTION',
			'description' => '',
        ]);

        AdminModule::create([
        	'code' => 'R0005',
			'title' => 'Production Summary Report',
			'category' => 'Reports',
			'user_category' => 'PRODUCTION',
			'description' => '',
        ]);

        AdminModule::create([
        	'code' => 'R0006',
			'title' => 'FG Summary',
			'category' => 'Reports',
			'user_category' => 'OFFICE',
			'description' => '',
        ]);

        AdminModule::create([
        	'code' => 'A0001',
			'title' => 'User Master',
			'category' => 'Administrator',
			'user_category' => 'ALL',
			'description' => '',
        ]);

        AdminModule::create([
        	'code' => 'A0002',
			'title' => 'Assign Production Line',
			'category' => 'Administrator',
			'user_category' => 'ALL',
			'description' => '',
        ]);

        AdminModule::create([
        	'code' => 'A0003',
			'title' => 'User Type',
			'category' => 'Administrator',
			'user_category' => 'ALL',
			'description' => '',
        ]);

        AdminModule::create([
        	'code' => 'A0004',
			'title' => 'Audit Trail',
			'category' => 'Administrator',
			'user_category' => 'ALL',
			'description' => '',
        ]);

        AdminModule::create([
        	'code' => 'A0005',
			'title' => 'Settings',
			'category' => 'Administrator',
			'user_category' => 'ALL',
			'description' => '',
        ]);

        
    }
}
