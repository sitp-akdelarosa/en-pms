<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminAuditTrail extends Model
{
    protected $fillable = [
    				'user_type',
    				'module',
    				'action',
    				'user'
    			];
}
