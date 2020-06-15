<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminModule extends Model
{
    protected $fillable = [
    				'code',
    				'title',
    				'category',
    				'user_category',
    				'description',
    			];
}
