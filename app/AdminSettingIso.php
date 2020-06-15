<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminSettingIso extends Model
{
    protected $fillable = [
    	'iso_name',
    	'iso_code',
    	'photo',
    	'create_user',
    	'update_user'
    ];
}
