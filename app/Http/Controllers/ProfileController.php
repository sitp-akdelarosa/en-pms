<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use App\User;
use App\AdminAuditTrail;
use DB;

class ProfileController extends Controller
{
    protected $_helper = '';
    protected $_audit = '';

    public function __construct()
    {
        $this->middleware('auth');
        $this->_helper = new HelpersController;
        $this->_audit = new AuditTrailController;
    }

    public function index($user_id)
    {
    	$user = User::where('user_id',$user_id)->first();
    	$user_accesses = $this->_helper->UserAccess();

		return view('profile', [
			'user_accesses' => $user_accesses,
			'permission_access' => 0,
			'user' => $user
		]);
    }

    public function getActivity(Request $req)
    {
    	$timeline = [];
    	$activity = [];
    	$data = [];
    	$skip = $req->take - 5;
    	$dates = AdminAuditTrail::where('user',$req->id)
    							->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as timeline'))
    							->skip($skip)->take($req->take)
    							->orderBy('id','desc')
    							->get();

    	foreach ($dates as $key => $dt) {
    		array_push($timeline, $dt->timeline);
    	}

    	array_unique($timeline);

    	foreach ($timeline as $key => $date) {
    		$act = AdminAuditTrail::where('user',$req->id)
	    						->where('created_at','like',$date.'%')
	    						->select(
	    							'user_type',
	    							'module',
	    							'action',
	    							'user',
	    							DB::raw('DATE_FORMAT(created_at, "%r") as time')
	    						)
	    						->orderBy('id','desc')
								->get();
			$key_date = $this->_helper->convertDate($date,'M d Y');
			$activity[$key_date] = $act;
    	}

    	return response()->json($activity);
    }
}
