<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelpersController;
use App\AdminAuditTrail;
use App\Events\AuditTrail;


class AuditTrailController extends Controller
{
    protected $_helper;

    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
        $this->_helper = new HelpersController;
    }

    public function index()
    {
        $user_accesses = $this->_helper->UserAccess();
        return view('admin.audit-trail',['user_accesses' => $user_accesses]);
    }

    public function getAllAuditTrail()
    {
        $audit = AdminAuditTrail::orderBy('id','desc')->get();
        return response()->json($audit);
    }

    public function insert(array $params)
    {
        AdminAuditTrail::create($params);
        event(new AuditTrail($params));
    }
}
