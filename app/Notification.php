<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
    	'title',
    	'content',
    	'from',
    	'to',
    	'read',
    	'module',
        'from_id',
        'link',
        'content_id',
    	'create_user',
        'update_user'
    ];
}
