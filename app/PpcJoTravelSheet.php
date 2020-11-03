<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcJoTravelSheet extends Model
{
    protected $fillable = [
		'jo_summary_id',
    	'jo_no',
    	'sc_no',
    	'prod_code',
    	'description',
    	'order_qty',
    	'sched_qty',
    	'issued_qty',
    	'material_used',
		'material_heat_no',
		'uom',
		'lot_no',
		'ship_date',
    	'status',
        'create_user',
        'update_user'
    ];
}
