<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcProductWithdrawal extends Model
{
    protected $fillable = [
        'trans_no',
        'item_class',
        'item_code',
        'jo_no',
        'lot_no',
        'heat_no',
        'alloy',
        'item',
        'size',
        'schedule',
        'sc_no',
        'remarks',
        'issued_qty',
        'issued_uom',
        'create_user',
        'update_user'
    ];
}
