<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminModuleAccess extends Model
{
    protected $fillable = [
    				'code',
    				'title',
    				'category',
    				'user_category',
    				'user_id',
    				'access',
    				'create_user',
    				'update_user',
    			];
}
