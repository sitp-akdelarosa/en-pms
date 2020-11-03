<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcPreTravelSheet extends Model
{
    protected $fillable = [
    	'jo_no',
    	'prod_code',
    	'issued_qty',
        'qty_per_sheet',
        'status',
        'iso_code',
        'iso_name',
        'iso_photo',
        'ship_date',
        'release_date',
        'remarks',
    	'create_user',
    	'update_user',
    ];

    public function issued_qty()
    {
    	return $this->hasMany('App\PpcPreTravelSheetProducts','pre_travel_sheet_id');
    }

    public function process()
    {
    	return $this->hasMany('App\PpcPreTravelSheetProcess','pre_travel_sheet_id');
    }
}
