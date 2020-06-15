<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcTempUploadOrder extends Model
{
    protected $fillable = [
    				'sc_no',
    				'prod_code',
    				'description',
    				'quantity',
    				'po',
    				'date_upload',
    				'create_user',
    				'update_user'
    			];
}
