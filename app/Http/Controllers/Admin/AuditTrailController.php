<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelpersController;
use App\AdminAuditTrail;
use App\Events\AuditTrail;
use DB;


class AuditTrailController extends Controller
{
    protected $_helper;

    public function __construct()
    {
        // $this->middleware('ajax-session-expired');
        // $this->middleware('auth');
        $this->_helper = new HelpersController;

        $this->_moduleID = $this->_helper->moduleID('A0004');
    }

    public function index()
    {
        $user_accesses = $this->_helper->UserAccess();
        $permission_access = $this->_helper->check_permission('A0004');

        return view('admin.audit-trail', [
            'user_accesses' => $user_accesses,
            'permission_access' => $permission_access
        ]);
    }

    public function getAllAuditTrail()
    {
        $audit = DB::select(
                            "SELECT a.id as id,
                                    ut.description as user_type,
                                    a.module as module,
                                    a.`action` as `action`,
                                    CONCAT(u.firstname,' ',u.lastname) as fullname,
                                    a.created_at as created_at,
                                    a.updated_at as updated_at
                            FROM enpms.admin_audit_trails as a
                            inner join users as u
                            on u.id = a.`user`
                            inner join admin_user_types as ut
                            on ut.id = a.user_type
                            order by a.id desc"
                        );
                        // ->join('')
                        // ->orderBy('a.id','desc')
                        // ->get();
        return response()->json($audit);
    }

    public function insert(array $params)
    {
        AdminAuditTrail::create($params);
        $ut = DB::table('admin_user_types')->select('description')->where('id',Auth::user()->user_type)->first();

        $params['user_type'] = $ut->description;

        //event(new AuditTrail($params));
    }
}
