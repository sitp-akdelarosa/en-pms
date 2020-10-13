<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'materials_type',
        'materials_code',
        'description',
        'item',
        'alloy',
        'schedule',
        'size',
        'width',
        'length',
        'orig_quantity',
        'qty_weight',
        'qty_pcs',
        'quantity',
        'uom',
        'heat_no',
        'invoice_no',
        'received_date',
        'supplier',
        'supplier_heat_no',
        'received_id',
        'create_user',
        'update_user'
    ];
}
