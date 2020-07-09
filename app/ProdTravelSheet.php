<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProdTravelSheet extends Model
{
    protected $fillable = [
    	'jo_no',
    	'jo_sequence',
    	'prod_order_no',
    	'prod_code',
    	'description',
    	'material_used',
    	'material_heat_no',
    	'lot_no',
    	'type',
    	'order_qty',
    	'issued_qty',
        'total_issued_qty',
        'status',
        'iso_code',
        'iso_name',
        'iso_photo',
        'pre_travel_sheet_id',
    	'create_user',
    	'update_user'
    ];

    public function processes()
    {
        return $this->hasMany('App\ProdTravelSheetProcess','travel_sheet_id');
    }
}
