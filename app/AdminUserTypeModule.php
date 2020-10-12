<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminUserTypeModule extends Model
{
	protected $fillable = [
		'user_type_id',
		'module_id',
		'code',
		'user_category',
		'create_user',
		'update_user'
	];

	public function user_type()
	{
		return $this->belongsTo('App\AdminUserType','id');
	}
	
}
