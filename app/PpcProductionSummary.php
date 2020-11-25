<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcProductionSummary extends Model
{
    protected $fillable = [
    				'sc_no',
    				'prod_code',
    				'description',
    				'quantity',
    				'unit',
    				'sched_qty',
    				'po',
                    'status',
    				'date_upload',
    				'create_user',
					'update_user',
					'jo_summary_id'
    			];
}
