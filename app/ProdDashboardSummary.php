<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProdDashboardSummary extends Model
{
    protected $fillable = [
    				'product_code',
    				'description',
    				'sched_qty',
    				'unprocessed',
    				'good',
    				'rework',
    				'scrap',
    				'total_output',
    			];
}
