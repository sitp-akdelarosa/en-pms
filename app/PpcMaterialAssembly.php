<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcMaterialAssembly extends Model
{
    protected $fillable = [
    	'mat_type',
    	'character_num',
    	'character_code',
    	'description',
    	'create_user',
    	'update_user'
    ];
}
