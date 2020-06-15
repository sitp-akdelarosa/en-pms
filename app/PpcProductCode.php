<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcProductCode extends Model
{
    protected $fillable = [
    				'product_type',
    				'product_code',
    				'code_description',
    				'cut_weight',
    				'cut_weight_uom',
    				'cut_length',
    				'cut_length_uom',
                    'item',
                    'class',
                    'alloy',
					'size',
					'standard_material_used',
    				'create_user',
					'update_user'
    			];
}
