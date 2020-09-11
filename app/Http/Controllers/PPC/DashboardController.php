<?php

namespace App\Http\Controllers\PPC;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HelpersController;
use Illuminate\Support\Facades\Auth;
use App\PpcDashboard;
use DataTables;
use DB;
use Carbon;

class DashboardController extends Controller
{
    protected $_helper;

    public function __construct()
    {
        $this->_helper = new HelpersController;
    }

    public function index()
    {
        $dateFrom = date('Y-m-d');
        $dateTo = date('Y-m-d',strtotime(str_replace('-', '/', date('Y-m-d')) . "+1 days"));

        $user_accesses = $this->_helper->UserAccess();

        return view('ppc.dashboard',['from' => $dateFrom,'to' => $dateTo, 'user_accesses' => $user_accesses]);
    }

    public function pie_graph(Request $req)
    {
        if($req->jo_no == ''){
            $data = DB::select("SELECT  ifnull(
                                            (SUM(p.unprocessed)/(
                                                SUM(p.unprocessed)+SUM(p.good)+SUM(p.rework)+SUM(p.scrap)
                                            ))*100,
                                        0) AS unprocessed, 
                                        ifnull(
                                            (SUM(p.good)/(
                                                SUM(p.unprocessed)+SUM(p.good)+SUM(p.rework)+SUM(p.scrap)
                                            ))*100,
                                        0) AS good,
                                        ifnull(
                                            (SUM(p.rework)/(
                                                SUM(p.unprocessed)+SUM(p.good)+SUM(p.rework)+SUM(p.scrap)
                                            ))*100,
                                        0) AS rework,
                                        ifnull(
                                            (SUM(p.scrap)/(
                                                SUM(p.unprocessed)+SUM(p.good)+SUM(p.rework)+SUM(p.scrap)
                                            ))*100,
                                        0) AS scrap ,
                                        p.process AS process
                                FROM prod_travel_sheets AS ts
                                JOIN prod_travel_sheet_processes AS p ON ts.id = p.travel_sheet_id
                                LEFT JOIN ppc_product_codes AS pc ON ts.prod_code = pc.product_code
                                LEFT JOIN admin_assign_production_lines AS pl ON pl.product_line = pc.product_type
                                WHERE ts.status = 2 and 
                                      pl.user_id = '".Auth::user()->id."' and 
                                      (p.unprocessed != 0 or p.good != 0 or p.rework != 0 or p.scrap != 0)
                                GROUP BY p.process
                                ORDER BY p.sequence ASC");

        }else{
            $data = DB::select("SELECT  ifnull(
                                            (SUM(p.unprocessed)/(
                                                SUM(p.unprocessed)+SUM(p.good)+SUM(p.rework)+SUM(p.scrap)
                                            ))*100,
                                        0) AS unprocessed, 
                                        ifnull(
                                            (SUM(p.good)/(
                                                SUM(p.unprocessed)+SUM(p.good)+SUM(p.rework)+SUM(p.scrap)
                                            ))*100,
                                        0) AS good,
                                        ifnull(
                                            (SUM(p.rework)/(
                                                SUM(p.unprocessed)+SUM(p.good)+SUM(p.rework)+SUM(p.scrap)
                                            ))*100,
                                        0) AS rework,
                                        ifnull(
                                            (SUM(p.scrap)/(
                                                SUM(p.unprocessed)+SUM(p.good)+SUM(p.rework)+SUM(p.scrap)
                                            ))*100,
                                        0) AS scrap ,
                                        p.process AS process
                                FROM prod_travel_sheets AS ts
                                JOIN prod_travel_sheet_processes AS p ON ts.id = p.travel_sheet_id
                                WHERE ts.jo_sequence = '".$req->jo_no."' and 
                                (p.unprocessed != 0 or p.good != 0 or p.rework != 0 or p.scrap != 0)
                                GROUP BY p.process
                                ORDER BY p.sequence ASC ");
        }

        $pie = [];
        foreach ($data as $key => $dt) {
            array_push($pie, [
                'process' => $dt->process,
                'records' => [
                    [ 'y' => $dt->unprocessed, 'label' => 'Unprocessed', 'color' => '#ffc107'],
                    [ 'y' => $dt->good, 'label' => 'Good', 'color' => '#28a745' ],
                    [ 'y' => $dt->rework, 'label' => 'Rework', 'color' => '#004485' ],
                    [ 'y' => $dt->scrap, 'label' => 'Scrap', 'color' => '#d84951' ]
                ]
            ]);
        }


        return response()->json($pie);
    }

    public function get_dashboard(Request $req)
    {
        if (isset($req->date_from) && !isset($req->date_to)) {
            $data = ['msg' => 'The Date to required if the Date from have value.','status' => 'failed'];
            return response()->json($data);
        }
        if (isset($req->date_from) && isset($req->date_to)) {
            $travel_sheet = DB::table('prod_travel_sheets as ts')
                                ->join('prod_travel_sheet_processes as p','ts.id','=','p.travel_sheet_id')
                                ->leftjoin('ppc_divisions as d','d.div_code','=','p.div_code')                                
                                ->leftjoin('ppc_product_codes as pc', 'ts.prod_code', '=', 'pc.product_code')
                                ->leftjoin('admin_assign_production_lines as pl', 'pl.product_line', '=', 'pc.product_type')
                                ->where('pl.user_id' ,Auth::user()->id)
                                ->whereIn('ts.status',[0,1,2,5])
                                ->whereBetween(DB::raw('left(p.updated_at,10)'), [$req->date_from, $req->date_to])
                                ->select(
                                    DB::raw("ts.jo_sequence as jo_sequence"),
                                    DB::raw("ts.prod_code as prod_code"),
                                    DB::raw("ifnull(pc.code_description,ts.description) as description"),
                                    DB::raw("d.plant as plant"),
                                    DB::raw("p.process as process"),
                                    DB::raw("ts.material_used as material_used"),
                                    DB::raw("ts.material_heat_no as material_heat_no"),
                                    DB::raw("ts.lot_no as lot_no"),
                                    DB::raw("ts.order_qty as order_qty"),
                                    DB::raw("ts.total_issued_qty as total_issued_qty"),
                                    DB::raw("ts.issued_qty as issued_qty"),
                                    DB::raw("p.div_code as div_code"),
                                    DB::raw("p.status as status")
                                )
                                ->orderBy('ts.id', 'DESC')
                                ->orderBy('p.sequence' , 'ASC');
                                    
        }else{
            $travel_sheet = DB::table('prod_travel_sheets as ts')
                                ->join('prod_travel_sheet_processes as p','ts.id','=','p.travel_sheet_id')
                                ->leftjoin('ppc_divisions as d','d.div_code','=','p.div_code')
                                ->leftjoin('ppc_product_codes as pc', 'ts.prod_code', '=', 'pc.product_code')
                                ->leftjoin('admin_assign_production_lines as pl', 'pl.product_line', '=', 'pc.product_type')
                                ->where('pl.user_id' ,Auth::user()->id)
                                ->where('ts.status',2)
                                ->select(
                                    DB::raw("ts.jo_sequence as jo_sequence"),
                                    DB::raw("ts.prod_code as prod_code"),
                                    DB::raw("ifnull(pc.code_description,ts.description) as description"),
                                    DB::raw("d.plant as plant"),
                                    DB::raw("p.process as process"),
                                    DB::raw("ts.material_used as material_used"),
                                    DB::raw("ts.material_heat_no as material_heat_no"),
                                    DB::raw("ts.lot_no as lot_no"),
                                    DB::raw("ts.order_qty as order_qty"),
                                    DB::raw("ts.total_issued_qty as total_issued_qty"),
                                    DB::raw("ts.issued_qty as issued_qty"),
                                    DB::raw("p.div_code as div_code"),
                                    DB::raw("p.status as status")
                                )
                                ->orderBy('ts.id', 'DESC')
                                ->orderBy('p.sequence' , 'ASC');
                                
       
        }
        // return dd(DB::getQueryLog());
       return DataTables::of($travel_sheet)
                ->editColumn('status', function($data) {
                    $status = 'ON PROCESS';
                    if ($data->status == 1) {
                        $status = 'READY FOR FG';
                    }else if($data->status == 2){
                        $status = 'FINISHED';
                    }else if($data->status == 3){
                        $status = 'CANCELLED';
                    }else if($data->status == 4){
                        $status = 'TRANSFER ITEM';
                    }
                    return $status;
                })->make(true);
    }

    public function get_jono()
    {
        $jo_no = DB::table('prod_travel_sheets as ts')
                    ->leftjoin('ppc_product_codes as pc', 'ts.prod_code', '=', 'pc.product_code')
                    ->leftjoin('admin_assign_production_lines as pl', 'pl.product_line', '=', 'pc.product_type')
                    ->select(['ts.jo_sequence'])
                    ->where('pl.user_id' ,Auth::user()->id)
                    ->where('ts.status','!=',3)
                    ->get();
        return response()->json($jo_no);
    }
}
