<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcPreTravelSheetProducts extends Model
{
    protected $fillable = [
    	'pre_travel_sheet_id',
        'jo_no',
    	'prod_code',
    	'issued_qty_per_sheet',
        'sc_no',
        'jo_sequence',
    	'create_user',
    	'update_user'
    ];

    public function pre_travel_sheet()
    {
        return $this->belongsTo('App\PpcPreTravelSheet', 'pre_travel_sheet_id');
    }
}
