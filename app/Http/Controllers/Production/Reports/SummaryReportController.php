<?php

namespace App\Http\Controllers\Production\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;

class SummaryReportController extends Controller
{
    protected $_helper;
    protected $_audit;

    public function __construct()
    {
        $this->middleware('auth');
        $this->_helper = new HelpersController;
        $this->_audit = new AuditTrailController;
    }

    public function index()
    {
        $user_accesses = $this->_helper->UserAccess();
        return view('production.reports.summary-report',['user_accesses' => $user_accesses]);
    }
}
