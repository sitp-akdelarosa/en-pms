<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProdDashboardDetails extends Model
{
    protected $fillable = [
    				'job_order_no',
    				'product_code',
    				'description',
    				'sched_qty',
    				'unprocessed',
    				'good',
    				'rework',
    				'scrap',
    				'total_output',
    				'requested_date',
    				'end_date',
    			];
}
