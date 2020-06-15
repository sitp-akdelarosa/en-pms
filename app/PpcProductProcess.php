<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcProductProcess extends Model
{
    protected $fillable = [
    				'prod_id',
			    	'prod_code',
			    	'process',
			    	'div_code',
			    	'set',
			    	'create_user',
			    	'update_user'
			    ];
}
