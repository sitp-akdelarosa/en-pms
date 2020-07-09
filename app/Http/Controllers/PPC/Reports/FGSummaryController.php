<?php

namespace App\Http\Controllers\PPC\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use Illuminate\Support\Facades\Auth;
use App\PpcProductionSummary;
use App\ProdTravelSheetProcess;
use App\ProdTravelSheet;
use App\RptFgSummary;
use DB;

class FGSummaryController extends Controller
{
    protected $_helper;

    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
        $this->_helper = new HelpersController;
        $this->_audit = new AuditTrailController;
    }

    public function index()
    {
        $user_accesses = $this->_helper->UserAccess();
        return view('ppc.reports.fg-summary',['user_accesses' => $user_accesses]);
    }

    public function getFG(Request $req)
    {
        if($req->status == 0){
            $data = DB::select("select rfs.id ,rfs.sc_no ,rfs.prod_code, rfs.description, rfs.order_qty, rfs.qty, rfs.status FROM rpt_fg_summaries as rfs
                    LEFT JOIN ppc_product_codes as pc ON pc.product_code = rfs.prod_code
                    LEFT JOIN admin_assign_production_lines as pl ON pl.product_line = pc.product_type
                    where order_qty > qty 
                    group by rfs.id, rfs.sc_no , rfs.prod_code, rfs.description, rfs.order_qty, rfs.qty, rfs.status");
        }else{
            $data = DB::select("select rfs.id ,rfs.sc_no ,rfs.prod_code, rfs.description, rfs.order_qty, rfs.qty, rfs.status FROM rpt_fg_summaries as rfs
                    LEFT JOIN ppc_product_codes as pc ON pc.product_code = rfs.prod_code
                    LEFT JOIN admin_assign_production_lines as pl ON pl.product_line = pc.product_type
                    where order_qty <= qty  and qty != 0 
                    group by rfs.id, rfs.sc_no , rfs.prod_code, rfs.description, rfs.order_qty, rfs.qty, rfs.status");
        }

        return response()->json($data);
    }

    public function save_sc_no(Request $req)
    {
        $this->validate($req, [
            'sc_no' => 'required',
            'qty' => 'required',
        ]);
        RptFgSummary::where('id',$req->id)->decrement('qty',(int)$req->qty);      
        $RptFgSummary = RptFgSummary::where('sc_no',$req->sc_no)->where('prod_code',$req->prod_code)
                        ->increment('qty',(int)$req->qty);
        if($RptFgSummary == 0){
            RptFgSummary::create([
                'sc_no' => $req->sc_no,
                'prod_code' => $req->prod_code,
                'description' => $req->description,
                'order_qty' => $req->total_order_qty,
                'qty' => $req->qty,
                'status' => 0,
                'create_user' => Auth::user()->user_id,
                'update_user' => Auth::user()->user_id,
            ]);
        }
        $data = [ 'status' => 'success','msg' => 'Successfully saved.' ];
        return response()->json($data);
    }

    public function get_sc_no(Request $req)
    {
        $data = DB::select("select sc_no, order_qty FROM rpt_fg_summaries  where order_qty > qty and prod_code ='".$req->prod_code."'");
        return response()->json($data);
    }

}
