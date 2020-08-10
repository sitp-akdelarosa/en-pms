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

	public function user_type_modules()
	{
		return $this->hasMany('App\AdminUserTypeModule','user_type_id');
	}

}
