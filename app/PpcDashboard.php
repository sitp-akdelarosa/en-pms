<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcDashboard extends Model
{
    protected $fillable = [
    	'item_code',
		'description',
		'plant',
		'alloy',
		'size',
		'process',
		'class',
		'mats',
		'heat_no',
		'lot_no',
		'order_qty',
		'sched_qty',
		'create_user',
		'update_user'
    ];
}