<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcDivision extends Model
{
    protected $fillable = [
    				'div_code',
    				'div_name',
    				'plant',
					'leader',
					'is_disable',
    				'user_id',
    				'create_user',
    				'update_user'
    			];
}
