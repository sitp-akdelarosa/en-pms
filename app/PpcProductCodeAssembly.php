<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcProductCodeAssembly extends Model
{
    protected $fillable = [
			    	'prod_type',
			    	'character_num',
			    	'character_code',
			    	'description',
			    	'create_user',
			    	'update_user'
			    ];
}
