<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TempItemMaterial extends Model
{
    protected $fillable = [
        'sched_qty',
        'material_heat_no',
        'rmw_issued_qty',
        'material_used',
        'lot_no',
        'blade_consumption',
        'cut_weight',
        'cut_length',
        'cut_width',
        'mat_length',
        'mat_weight',
        'assign_qty',
        'remaining_qty',
        'inv_id',
        'rmwd_id',
        'rmw_no',
        'ship_date',
        'sc_no',
        'prod_code',
        'description',
        'quantity',
        'create_user',
    ];
}
