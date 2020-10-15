<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcMaterialCode extends Model
{
    protected $fillable = [
    	'material_type',
    	'material_code',
    	'code_description',
    	'item',
    	'alloy',
    	'schedule',
		'size',
		'std_weight',
    	'create_user',
    	'update_user'
    ];
}
