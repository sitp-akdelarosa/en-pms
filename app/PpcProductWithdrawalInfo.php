<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcProductWithdrawalInfo extends Model
{
    protected $fillable = [
        'trans_no',
        'create_user',
        'update_user'
    ];
}
