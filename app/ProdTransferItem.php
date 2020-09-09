<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProdTransferItem extends Model
{
    protected $fillable = [
    	'jo_id',
		'jo_no',
		'prod_order_no',
		'prod_code',
		'description',
		'div_code',
		'process',
		'qty',
		'status',
		'remarks',
		'receive_qty',
		'receive_remarks',
		'item_status',
		'date_received',
		'date_transfered',
		'create_user',
		'update_user',
		'deleted',
		'delete_user',
		'deleted_at'
	];
}
