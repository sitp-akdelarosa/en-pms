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
					'cut_width',
    				'cut_width_uom',
                    'item',
                    'class',
                    'alloy',
					'size',
					'standard_material_used',
					'finish_weight',
					'formula_classification',
    				'create_user',
					'update_user'
    			];
}
