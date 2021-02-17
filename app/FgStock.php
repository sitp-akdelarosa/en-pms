<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FgStock extends Model
{
    protected $fillable = [
    				'jo_no',
    				'prod_code',
    				'description',
    				'order_qty',
    				'qty',
    				'status',
    				'create_user',
    				'update_user'
	];
}
