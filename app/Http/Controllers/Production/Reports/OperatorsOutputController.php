<?php

namespace App\Http\Controllers\Production\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use DB;
use App\ProdProductionOutput;

class OperatorsOutputController extends Controller
{
    protected $_helper;
    protected $_audit;
    protected $_moduleID;

    public function __construct()
    {
        // $this->middleware('auth');
        $this->_helper = new HelpersController;
        $this->_audit = new AuditTrailController;

        $this->_moduleID = $this->_helper->moduleID('R0004');
    }

    public function index()
    {
        $user_accesses = $this->_helper->UserAccess();
        return view('production.reports.operators-output',['user_accesses' => $user_accesses]);
    }

    public function search_operator(Request $req)
    {
        $date_from = "NULL";
        $date_to = "NULL";
        $search_operator = "NULL";

        if (!is_null($req->date_from) && !is_null($req->date_to)) {
            $date_from = "'".$req->date_from."'";
            $date_to = "'".$req->date_to."'";
        }

        if (!is_null($req->search_operator)) {
            $search_operator = "'".$req->search_operator."'";
        }

        if (isset($req->date_from) && !isset($req->date_to)) {
            $data = [
                        'msg' => 'The Date to required if the Date from have value.',
                        'status' => 'failed'
                    ];

            return response()->json($data);
        }
        $prod_production_outputs = DB::select(
                                        DB::raw(
                                            "CALL GET_operators_output(
                                                ".$search_operator.",
                                                ".$date_from.",
                                                ".$date_to."
                                            )"
                                        )
                                    );

        $existing = ProdProductionOutput::where('operator',$req->search_operator)->get();
        if (count($prod_production_outputs) > 0) {
            $data = ['status' => 'success','ppo' => $prod_production_outputs];
        } elseif (count($existing) > 0) {

            if (count($prod_production_outputs) == 0) {
                $data = ['msg' => 'There are no existing data on that input date range','status' => 'failed'];
            } else {
                $data = ['status' => 'success','ppo' => $prod_production_outputs];
            }

        } else {
                $data = ['msg' => 'No Operator ID existing.','status' => 'failed'];
        }

        return response()->json($data);
    }
}
