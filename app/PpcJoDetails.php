<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcJoDetails extends Model
{
    protected $fillable = [
    				'jo_summary_id',
    				'sc_no',
					'product_code',
					'description',
					'back_order_qty',
					'sched_qty',
					'material_used',
					'material_heat_no',
					'uom',
					'lot_no',
					'inv_id',
					'create_user',
					'update_user',
					'rmw_id',
					'rmw_issued_qty',
					'material_type',
					'for_over_issuance',
					'heat_no_id',
					'ship_date'
    			];

    public function jo_summary()
    {
        return $this->belongsTo('App\PpcJoDetailsSummary');
    }
}
