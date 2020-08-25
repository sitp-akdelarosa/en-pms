<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotRegisteredMaterial extends Model
{
    protected $fillable = [
                    'materials_type',
    				'materials_code',
    				'quantity',
    				'uom',
    				'heat_no',
    				'invoice_no',
    				'received_date',
    				'supplier',
    				'width',
    				'length',
    				'supplier_heat_no',
    				'create_user',
    				'update_user'
    			];
}
