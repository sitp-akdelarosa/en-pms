<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminUserType extends Model
{
    protected $fillable = [
    	'description',
    	'category',
    	'del_flag'
    ];

    public function users()
    {
        return $this->belongsTo('App\User','id');
    }
}
