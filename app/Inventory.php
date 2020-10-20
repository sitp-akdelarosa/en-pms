<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'item_class',
        'materials_type',
        'product_line',
        'item_code',
        'description',
        'item',
        'alloy',
        'schedule',
        'class',
        'size',
        'width',
        'length',
        'orig_quantity',
        'qty_weight',
        'weight_uom',
        'qty_pcs',
        'pcs_uom',
        'heat_no',
        'lot_no',
        'invoice_no',
        'received_date',
        'supplier',
        'supplier_heat_no',
        'received_id',
        'create_user',
        'update_user',
        'deleted',
        'delete_user',
        'deleted_at',
        'mode',
        'thickness'
    ];
}
