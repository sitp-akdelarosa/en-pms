<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcUploadOrder extends Model
{
    protected $fillable = [
                    'sc_no',
                    'prod_code',
                    'description',
                    'quantity',
                    'unit',
                    'sched_qty',
                    'po',
                    'uploader',
                    'date_upload',
                    'create_user',
                    'update_user',
                ];
}
