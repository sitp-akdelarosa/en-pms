<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcProductWithdrawalDetail extends Model
{
    protected $fillable = [
        'trans_id',
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
        'inv_id',
        'create_user',
        'update_user'
    ];
}
