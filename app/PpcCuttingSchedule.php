<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PpcCuttingSchedule extends Model
{
    protected $fillable = [
			    	'iso_control_no',
			    	'withdrawal_slip_no',
			    	'date_issued',
			    	'machine_no',
			    	'prepared_by',
			    	'leader',
			    	'leader_id',
			    	'create_user',
			    	'update_user'
    			];
}
