<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 
        'firstname', 
        'lastname',
        'email',
        'password',
        'actual_password',
        'user_type',
        'photo',
        'div_code',
        'is_admin',
        'is_superadmin',
        'user_category',
        'del_flag'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    public function product_lines()
    {
        return $this->hasMany('App\PpcAssignProductionLine');
    }

    public function user_types()
    {
        return $this->hasOne('App\AdminUserType','id','user_type');
    }
}
