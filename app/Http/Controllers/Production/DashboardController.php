<?php

namespace App\Http\Controllers\Production;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use Illuminate\Support\Facades\Auth;
use App\ProdDashboardDetails;
use App\ProdDashboardSummary;
use App\PpcDivision;
use App\User;
use DataTables;
use DB;
use App\ProdTravelSheet;

class DashboardController extends Controller
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
        $details = ProdTravelSheet::all();

        // $division_id = Auth::user()->div_code;
        // $ppc_divisions = DB::table('ppc_divisions')->where('id',  $division_id)->first();
        
        // if(isset($ppc_divisions->div_code)){
            // $div_code = $ppc_divisions->div_code;
            // $processes = DB::table('prod_travel_sheet_processes')->where('div_code', $ppc_divisions->div_code)->get();

            return view('production.dashboard',[
                'user_accesses' => $user_accesses,
                // 'details' => $details,
                // 'processes' => $processes
            ]);
        // }
    }
    public function get_process()
    {
        $division_id = Auth::user()->div_code;
        $ppc_divisions = DB::table('ppc_divisions')->where('id',  $division_id)->first();
         if(isset($ppc_divisions->div_code)){
            $div_code = $ppc_divisions->div_code;

        $prod_travel_sheet_processes = DB::table('prod_travel_sheet_processes')->where('div_code',  $div_code)->get();
        return response()->json($prod_travel_sheet_processes);
        }
    }

    private function getDivCode()
    {
        $div_codes = [];
        $divs = PpcDivision::where('user_id',Auth::user()->id)
                            ->select('div_code')
                            ->get();
        if (count((array)$divs)) {
            foreach ($divs as $key => $div) {
                array_push($div_codes, $div->div_code);
            }
        }

        return $div_codes;
    }
    
    public function getDashBoardURL()
    {
        $div_codes = $this->getDivCode();

        $travel_sheet = DB::table('prod_travel_sheets as ts')
                            ->join('prod_travel_sheet_processes as p','ts.id','=','p.travel_sheet_id')
                            ->leftjoin('ppc_divisions as d','d.div_code','=','p.div_code')
                            ->leftjoin('ppc_update_inventories as i','i.heat_no','=','ts.material_heat_no')
                            ->whereIn('p.div_code',$div_codes)
                            ->whereIn('ts.status',array(0,1,2))
                            ->select(
                                DB::raw("ts.jo_sequence as jo_sequence"),
                                DB::raw("ts.prod_code as prod_code"),
                                DB::raw("ts.description as description"),
                                DB::raw("d.plant as plant"),
                                DB::raw("p.process as process"),
                                DB::raw("p.div_code as div_code"),
                                DB::raw("ts.material_used as material_used"),
                                DB::raw("ts.material_heat_no as material_heat_no"),
                                DB::raw("ts.lot_no as lot_no"),
                                DB::raw("ts.order_qty as order_qty"),
                                DB::raw("ts.total_issued_qty as total_issued_qty"),
                                DB::raw("ts.issued_qty as issued_qty"),
                                DB::raw("p.unprocessed as unprocessed"),
                                DB::raw("p.good as good"),
                                DB::raw("p.rework as rework"),
                                DB::raw("p.scrap as scrap"),
                                DB::raw("p.status as status")
                            )
                            ->orderBy('ts.id', 'DESC')
                            ->orderBy('p.sequence' , 'ASC')
                            ->get();

        return $travel_sheet;
    }

    // public function getDashBoardURL()
    // {
        // $details = DB::table('prod_travel_sheet_processes as ptsp')
        //             ->leftjoin('prod_travel_sheets as pts', 'pts.id', '=', 'ptsp.travel_sheet_id')
        //             ->select([
        //                  DB::raw("GROUP_CONCAT(CONCAT(ptsp.good) SEPARATOR ',') as g"),
        //                  DB::raw("GROUP_CONCAT(CONCAT(ptsp.unprocessed) SEPARATOR ',') as u"),
        //                  DB::raw("GROUP_CONCAT(CONCAT(ptsp.rework) SEPARATOR ',') as r"),
        //                  DB::raw("GROUP_CONCAT(CONCAT(ptsp.scrap) SEPARATOR ',') as s"),
        //                 'pts.jo_no as jo_no',
        //                 'pts.prod_order_no as prod_order_no',
        //                 'pts.prod_code as prod_code',
        //                 'pts.description as description',
        //                 'pts.status as status',
        //                 'ptsp.good as good'
        //             ])
        //             ->get();
        //  return DataTables::of($details)  
        // ->addColumn('good', function($data) {
        //     $g = explode(',',$data->g);
        //     return $g;
        // })
        // ->addColumn('unprocessed', function($data) {
        //     $u = explode(',',$data->u);
        //     return $u;
        // })
        // ->addColumn('rework', function($data) {
        //     $r = explode(',',$data->r);
        //     return $r;
        // })
        // ->addColumn('scrap', function($data) {
        //     $s = explode(',',$data->s);
        //     return $s;
        // })
        //  ->make(true);        
   // }
}
