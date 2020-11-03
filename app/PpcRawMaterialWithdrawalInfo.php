<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcRawMaterialWithdrawalInfo extends Model
{
    protected $fillable = [
      'trans_no',
      'status',
      'create_user',
      'update_user'
    ];

    public function details()
    {
    	return $this->hasMany('App\PpcRawMaterialWithdrawalDetails','trans_id');
    }
}
