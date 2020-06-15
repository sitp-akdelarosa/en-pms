<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RptFgSummary extends Model
{
    protected $fillable = [
    	'sc_no','prod_code','description','order_qty','qty','status','create_user','update_user'
    ];
}
