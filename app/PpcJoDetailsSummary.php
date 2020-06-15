<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcJoDetailsSummary extends Model
{
    protected $fillable = [
    				'jo_no',
    				'total_sched_qty',
    				'create_user',
    				'update_user'
    			];

    public function jo_details()
    {
        return $this->hasMany('App\PpcJoDetails');
    }
}
