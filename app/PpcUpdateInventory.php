<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcUpdateInventory extends Model
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
					'quantity',
					'qty_weight',
					'qty_pcs',
					'uom',
					'heat_no',
					'invoice_no',
					'received_date',
					'supplier',	
					'supplier_heat_no',
					'created_at',
					'updated_at'
			    ];
}
