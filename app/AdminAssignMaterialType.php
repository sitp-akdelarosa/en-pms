<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminAssignMaterialType extends Model
{
    protected $fillable = [
        'user_id', 'material_type', 'create_user', 'update_user'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
