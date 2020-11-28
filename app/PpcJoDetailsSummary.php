<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcJoDetailsSummary extends Model
{
    protected $fillable = [
                    'jo_no',
                    'status',
    				'total_sched_qty',
    				'create_user',
                    'update_user',
                    'rmw_no',
                    'cancelled',
                    'deleted',
                    'delete_user',
                    'deleted_at'
    			];

    public function jo_details()
    {
        return $this->hasMany('App\PpcJoDetails');
    }
}
