<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProdProductionOutput extends Model
{
    protected $fillable = [
    	'travel_sheet_id',
    	'travel_sheet_process_id',
		'jo_no',
		'unprocessed',
		'good',
		'rework',
		'scrap',
		'convert',
		'alloy_mix',
		'nc',
		'previous_process',
		'current_process',
		'output',
		'operator',
		'machine_no',
		'create_user',
		'update_user'
	];
}
