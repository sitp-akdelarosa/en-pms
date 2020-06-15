<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminTransactionNo extends Model
{
    protected $fillable = [
	    			'code',
	    			'description',
	    			'prefix',
	    			'prefixformat',
	    			'nextno',
	    			'nextnolength',
	    			'month',
	    			'create_user',
	    			'update_user'
	    		];
}
