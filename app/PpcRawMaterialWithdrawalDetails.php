<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcRawMaterialWithdrawalDetails extends Model
{
    protected $fillable = [
    	'trans_id',
    	'mat_code',
		'alloy',
		'item',
		'size',
		'schedule',
		'lot_no',
		'material_heat_no',
		'sc_no',
		'remarks',
		'issued_qty',
		'issued_uom',
		'needed_qty',
		'needed_uom',
		'inv_id',
		'returned_qty',
		'scheduled_qty',
		'returned_uom',
		'create_user',
		'update_user'
    ];

    public function infos()
    {
        return $this->belongsTo('App\PpcRawMaterialWithdrawalInfo', 'trans_id');
    }
}
