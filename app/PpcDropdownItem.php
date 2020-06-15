<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcDropdownItem extends Model
{
    protected $fillable = [
    				'dropdown_name_id',
    				'dropdown_name',
    				'dropdown_item',
    				'create_user',
    				'update_user'
    			];

    public function name()
    {
        return $this->belongsTo('App\PpcDropdownName', 'dropdown_name_id');
    }
}
