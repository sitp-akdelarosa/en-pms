<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcProcess extends Model
{
    protected $fillable = [
    	'process',
    	'sequence',
		'set',
		'set_id',
		'create_user',
		'update_user'
    ];
}
