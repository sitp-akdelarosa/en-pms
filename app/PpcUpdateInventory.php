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
					'qty_weight',
					'weight_uom',
					'qty_pcs',
					'pcs_uom',
					'heat_no',
					'invoice_no',
					'received_date',
					'supplier',	
					'supplier_heat_no',
					'create_user',
					'update_user',
					'created_at',
					'updated_at',
					'deleted',
					'delete_user',
					'deleted_at',
					'mode'
			    ];
}
