<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcOperator extends Model
{
    protected $fillable = [
        'operator_id',
        'firstname',
        'lastname',
        'nickname',
        'position',
        'create_user',
        'update_user'
    ];
}
