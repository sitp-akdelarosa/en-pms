<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcProcessSet extends Model
{
    protected $fillable = ['set','product_line','create_user','update_user'];
}
