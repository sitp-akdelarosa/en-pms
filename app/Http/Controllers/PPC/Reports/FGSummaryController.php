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
    protected $_audit;
    protected $_moduleID;

    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
        $this->_helper = new HelpersController;
        $this->_audit = new AuditTrailController;

        $this->_moduleID = $this->_helper->moduleID('R0006');
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
        $data = [ 
                'status' => 'failed',
                'msg' => 'Saving process was unsuccessful.' 
            ];

        $this->validate($req, [
            'sc_no' => 'required',
            'qty' => 'required',
        ]);

        RptFgSummary::where('id',$req->id)->decrement('qty',(int)$req->qty);      
        $RptFgSummary = RptFgSummary::where('sc_no',$req->sc_no)
                                    ->where('prod_code',$req->prod_code)
                                    ->increment('qty',(int)$req->qty, [
                                        'update_user' => Auth::user()->id
                                    ]);

        if($RptFgSummary == 0){

            $fgsummary = new RptFgSummary();
            $fgsummary->sc_no = $req->sc_no;
            $fgsummary->prod_code = $req->prod_code;
            $fgsummary->description = $req->description;
            $fgsummary->order_qty = $req->total_order_qty;
            $fgsummary->qty = $req->qty;
            $fgsummary->status = 0;
            $fgsummary->create_user = Auth::user()->id;
            $fgsummary->update_user = Auth::user()->id;

            if ($fgsummary->save()) {
                $this->_audit->insert([
                    'user_type' => Auth::user()->user_type,
                    'module_id' => $this->_moduleID,
                    'module' => 'FG Summary',
                    'action' => 'Saved data ID: ' . $fgsummary->id . '.',
                    'user' => Auth::user()->id,
                ]);

                $data = [ 
                    'status' => 'success',
                    'msg' => 'Successfully saved.' 
                ];
            }
        }
        
        $data = [ 
                'status' => 'success',
                'msg' => 'Successfully saved.' 
            ];
        return response()->json($data);
    }

    public function get_sc_no(Request $req)
    {
        $data = DB::select("select sc_no, order_qty FROM rpt_fg_summaries  where order_qty > qty and prod_code ='".$req->prod_code."'");
        return response()->json($data);
    }

}
