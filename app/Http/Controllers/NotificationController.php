<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use Illuminate\Support\Facades\Auth;
use App\PpcProductCode;
use App\Notification;
use DB;


class NotificationController extends Controller
{
	protected $_helper = '';
    protected $_audit = '';

    public function __construct()
    {
        $this->middleware('auth');
        $this->_helper = new HelpersController;
        $this->_audit = new AuditTrailController;
    }

    public function index()
    {
    	$user_accesses = $this->_helper->UserAccess();

		return view('notification', [
			'user_accesses' => $user_accesses,
			'permission_access' => 0
		]);
    }

    public function getUnreadNotification()
    {
    	$noti_count = Notification::where('to',Auth::user()->id)
								->where('read',0)->count();
		$noti_list = DB::table('notifications as n')
						->select(
							'id',
							'title',
							'content',
							'from',
							'to',
							'read',
							'module',
							'create_user',
							'created_at',
							'link',
							DB::raw("(SELECT photo FROM users as u WHERE u.id = n.from_id) as photo")
						)
						->where('to',Auth::user()->id)->take(3)
						->orderBy('id','desc')->get();

		return [
			'noti_count' => $noti_count,
			'noti_list' => $noti_list
		];
    }

    public function readNotification(Request $req)
    {
    	$noti = Notification::find($req->id);
    	$noti->read = 1;
    	$noti->update_user = Auth::user()->id;
        $noti->updated_at = date('Y-m-d h:i:s');
        $noti->update();
        return $this->getUnreadNotification();
    }

    public function all(Request $req)
    {
    	Notification::where('to',Auth::user()->id)
					->update(['read' => 1]);

    	$skip = $req->take - 10;
    	$noti = DB::table('notifications as n')
					->select(
						'id',
						'title',
						'content',
						'from',
						'to',
						'read',
						'module',
						'create_user',
						'created_at',
						'link',
						'content_id',
						DB::raw("(SELECT photo FROM users as u WHERE u.id = n.from_id) as photo")
					)
					->where('to',$req->user_id)
					->skip($skip)->take($req->take)
					->orderBy('id','desc')->get();

		return response()->json($noti);
    }
}
