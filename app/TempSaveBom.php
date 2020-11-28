<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TempSaveBom extends Model
{
    protected $fillable = [
        'upd_inv_id', 
        'inv_id', 
        'rmwd_id', 
        'size', 
        'computed_per_piece', 
        'material_type', 
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
        'rmw_no', 
        'ship_date', 
        'sc_no', 
        'prod_code', 
        'description', 
        'quantity', 
        'create_user', 
        'created_at', 
        'updated_at', 
        'sc_id',
        'ref_id'
    ];
}
