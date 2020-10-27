<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminAssignWarehouse extends Model
{
     protected $fillable = [
        'user_id', 'warehouse', 'create_user', 'update_user'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
