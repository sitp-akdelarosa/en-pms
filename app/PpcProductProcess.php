<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcProductProcess extends Model
{
    protected $fillable = [
    				'prod_id',
			    	'prod_code',
			    	'process',
			    	'set',
					'sequence',
					'remarks',
			    	'create_user',
			    	'update_user'
			    ];
}
