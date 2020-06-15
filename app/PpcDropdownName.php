<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcDropdownName extends Model
{
    protected $fillable = [
    				'dropdown_name',
    				'create_user',
    				'update_user'
    			];

    public function items()
    {
        return $this->hasMany('App\PpcDropdownItem','dropdown_name_id');
    }
}
