<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcCuttingScheduleDetail extends Model
{
    protected $fillable = [
    				'cutt_id',
    				'item_no',
    				'alloy',
    				'size',
    				'item',
					'class',
					'cut_weight',
					'cut_length',
					'schedule',
					'qty_needed_inbox',
    				'sc_no',
    				'order_qty',
    				'qty_needed',
    				'qty_cut',
					'material_desc'
    			];
}
