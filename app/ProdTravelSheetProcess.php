<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProdTravelSheetProcess extends Model
{
    protected $fillable = [
        'travel_sheet_id',
    	'unprocessed',
    	'good',
    	'rework',
    	'scrap',
    	'convert',
    	'alloy_mix',
    	'nc',
    	'process',
        'sequence',
    	'previous_process',
    	'div_code',
        'machine_no',
    	'operator',
    	'leader',
        'leader_id',
    	'status',
    	'create_user',
    	'update_user'
    ];

    public function travel_sheet()
    {
        return $this->belongsTo('App\ProdTravelSheet','travel_sheet_id');
    }
}
