<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminAssignProductionLine extends Model
{
    protected $fillable = [
        'user_id', 'product_line', 'create_user', 'update_user'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
