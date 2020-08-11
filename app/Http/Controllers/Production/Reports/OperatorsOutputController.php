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
        $this->middleware('auth');
        $this->_helper = new HelpersController;
        $this->_audit = new AuditTrailController;

        $this->_moduleID = $this->_helper->moduleID('R0004');
    }

    public function index()
    {
        $user_accesses = $this->_helper->UserAccess();
        return view('production.reports.operators-output',['user_accesses' => $user_accesses]);
    }

    public function search_operator(Request $request)
    {
        if (isset($request->date_from) && !isset($request->date_to)) {
            $data = ['msg' => 'The Date to required if the Date from have value.','status' => 'failed'];
            return response()->json($data);
        }
        if (isset($request->date_from) && isset($request->date_to)) {
            $prod_production_outputs = DB::table('prod_production_outputs as ppo')
                            ->join('prod_travel_sheets as ts','ts.id','=','ppo.travel_sheet_id')
                            ->where('ppo.operator',$request->search_operator)
                            ->whereBetween(DB::raw('left(ppo.created_at,10)'), [$request->date_from, $request->date_to])
                            ->select(
                                DB::raw("ts.jo_no as jo_no"),
                                DB::raw("ts.prod_code as prod_code"),
                                DB::raw("ts.prod_order_no as prod_order_no"),
                                DB::raw("ts.total_issued_qty as total_issued_qty"),
                                DB::raw("ppo.previous_process as previous_process"),
                                DB::raw("ppo.current_process as current_process"),
                                DB::raw("ppo.unprocessed as unprocessed"),
                                DB::raw("ppo.good as good"),
                                DB::raw("ppo.rework as rework"),
                                DB::raw("ppo.scrap as scrap"),
                                DB::raw("ppo.created_at as created_at")
                            )->get();
        }else{
            $prod_production_outputs = DB::table('prod_production_outputs as ppo')
                            ->join('prod_travel_sheets as ts','ts.id','=','ppo.travel_sheet_id')
                            ->where('ppo.operator',$request->search_operator)
                            ->select(
                                DB::raw("ts.jo_no as jo_no"),
                                DB::raw("ts.prod_code as prod_code"),
                                DB::raw("ts.prod_order_no as prod_order_no"),
                                DB::raw("ts.total_issued_qty as total_issued_qty"),
                                DB::raw("ppo.previous_process as previous_process"),
                                DB::raw("ppo.current_process as current_process"),
                                DB::raw("ppo.unprocessed as unprocessed"),
                                DB::raw("ppo.good as good"),
                                DB::raw("ppo.rework as rework"),
                                DB::raw("ppo.scrap as scrap"),
                                DB::raw("ppo.created_at as created_at")
                            )->get();
        }
        $existing = ProdProductionOutput::where('operator',$request->search_operator)->get();
        if (count($existing) > 0) {
            if(count($prod_production_outputs) == 0){
                $data = ['msg' => 'There are no existing data on that input date range','status' => 'failed'];
            }else{
                $data = ['status' => 'success','ppo' => $prod_production_outputs];
            }
        }else{
                $data = ['msg' => 'No Operator ID existing.','status' => 'failed'];
        }

        return response()->json($data);
    }
}
