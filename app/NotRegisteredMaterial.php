<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotRegisteredMaterial extends Model
{
    protected $fillable = [
                    'materials_type',
    				'materials_code',
    				'qty_weight',
    				'qty_pcs',
    				'heat_no',
    				'invoice_no',
    				'received_date',
    				'supplier',
    				'width',
    				'length',
    				'supplier_heat_no',
    				'create_user',
					'update_user',
					'thickness'
    			];
}
