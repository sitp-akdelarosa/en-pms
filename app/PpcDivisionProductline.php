<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcDivisionProductline extends Model
{
    protected $fillable = [
        'productline',
        'division_id',
    ];
}
