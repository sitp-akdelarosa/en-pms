<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcPreTravelSheetProcess extends Model
{
    protected $fillable = [
    	'pre_travel_sheet_id',
        'jo_no',
		'set',
		'process_name',
        'sequence',
        'remarks',
		'div_code',
		'create_user',
		'update_user',
    ];

    public function pre_travel_sheet()
    {
        return $this->belongsTo('App\PpcPreTravelSheet', 'pre_travel_sheet_id');
    }
}
