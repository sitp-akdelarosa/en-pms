<?php

namespace App\Http\Controllers\PPC\Transaction;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use App\PpcProcess;
use App\PpcProductProcess;
use App\PpcDivision;
use App\PpcDivisionProcess;
use App\PpcPreTravelSheet;
use App\PpcPreTravelSheetProducts;
use App\PpcPreTravelSheetProcess;
use App\ProdTravelSheet;
use App\PpcJoTravelSheet;
use App\PpcJoDetails;
use App\ProdTravelSheetProcess;
use App\AdminSettingIso;
use App\User;
use App\PpcUpdateInventory;
use DataTables;
use DB;

class TravelSheetController extends Controller
{
    protected $_helper = '';
    protected $_audit;
    protected $_moduleID;

    public function __construct()
    {
        // $this->middleware('auth');
        $this->_helper = new HelpersController;
        $this->_audit = new AuditTrailController;

        $this->_moduleID = $this->_helper->moduleID('T0006');
    }

    public function index()
    {
        $user_accesses = $this->_helper->UserAccess();
        return view('ppc.transaction.travel-sheet',['user_accesses' => $user_accesses]);
    }

    public function getJoDetails(Request $req)
    {
        $status = '';
        $TO = '';
        $FROM = '';
        $jo_details = [];
        switch ($req->status) {
            case 1:
                $status = 'AND ts.status IS NULL';
                break;
            case 2:
                $status = 'AND ts.status = 1'; // 
                break;
            case 3:
                $status = 'AND ts.status = 2';
                break;  
            case 5:
                $status = 'AND ts.status = 5';
                break;   
            default:
                $status = '';
                break;
        }
        if (isset($req->fromvalue)) {
                $from = PpcJoTravelSheet::select('id')
                            ->where('jo_no', 'like', '%'.$req->fromvalue.'%')
                            ->first();
                $to =  PpcJoTravelSheet::select('id')
                            ->where('jo_no', 'like', '%'.$req->tovalue.'%')
                            ->first();
                if (isset($from) && isset($to))
                {
                    $FROM = $from->id;
                    $TO = $to->id;

                    if($req->tovalue == ''){
                        $TO = $FROM;
                    }
                }

                $data = DB::table('ppc_jo_travel_sheets as jt')
                        ->leftJoin('ppc_pre_travel_sheets as ts', 'ts.jo_no','=','jt.jo_no')
                        ->leftjoin('ppc_product_codes as pc', 'jt.prod_code', '=', 'pc.product_code')
                        ->leftjoin('admin_assign_production_lines as pl', 'pl.product_line', '=', 'pc.product_type')
                        ->where('jt.create_user' ,Auth::user()->id)
                        ->where('pl.user_id' ,Auth::user()->id)
                        ->whereRaw("1=1 ".$status)
                        ->where('jt.status' , '!=',3)
                        ->whereBetween('jt.id', [$FROM, $TO])
                        ->orderBy('jt.jo_no','desc')
                        ->groupBy(
                            'jt.jo_summary_id',
                            // 'ts.id',
                            'ts.qty_per_sheet',
                            'ts.iso_code',
                            'jt.jo_no',
                            'jt.sc_no',
                            'jt.prod_code',
                            'jt.description',
                            'ts.issued_qty',
                            'jt.material_used',
                            'jt.material_heat_no',
                            'jt.created_at',
                            'ts.status')
                        ->select(
                            DB::raw("jt.jo_summary_id as idJO"),
                            DB::raw("ts.qty_per_sheet as qty_per_sheet"),
                            DB::raw("ts.iso_code as iso_code"),
                            DB::raw("IFNULL(ts.id,'') as id"),
                            DB::raw("jt.jo_no as jo_no"),
                            DB::raw("jt.sc_no as sc_no"),
                            DB::raw("jt.prod_code as product_code"),
                            DB::raw("ifnull(pc.code_description,jt.description) as description"),
                            DB::raw("SUM(jt.order_qty) as back_order_qty"),
                            DB::raw("SUM(jt.sched_qty) as sched_qty"),
                            DB::raw("IFNULL(ts.issued_qty,0) as issued_qty"),
                            DB::raw("jt.material_used as material_used"),
                            DB::raw("jt.material_heat_no as material_heat_no"),
                            DB::raw("DATE_FORMAT(jt.created_at,'%Y-%m-%d %H:%i:%s') AS created_at"),
                            DB::raw("IFNULL(ts.status,0) as status")
                        )->get();

                foreach ($data as $key => $dt) {
                    array_push($jo_details, [
                        'qty_per_sheet' => $dt->qty_per_sheet,
                        'iso_code' => $dt->iso_code,
                        'id' => $dt->id,
                        'idJO' => $dt->idJO,
                        'jo_no' => $dt->jo_no,
                        'sc_no' => $dt->sc_no,
                        'product_code' => $dt->product_code,
                        'description' => $dt->description,
                        'back_order_qty' => $dt->back_order_qty,
                        'sched_qty' => $dt->sched_qty,
                        'issued_qty' => $dt->issued_qty,
                        'material_used' => $dt->material_used,
                        'material_heat_no' => $dt->material_heat_no,
                        'created_at' => $dt->created_at,
                        'status' => $dt->status
                    ]);
                }

                
              
                $data = DB::table('ppc_jo_travel_sheets as jt')
                        ->leftJoin('ppc_pre_travel_sheets as ts', 'ts.jo_no','=','jt.jo_no')
                        ->leftjoin('ppc_product_codes as pc', 'jt.prod_code', '=', 'pc.product_code')
                        ->leftjoin('admin_assign_production_lines as pl', 'pl.product_line', '=', 'pc.product_type')
                        ->where('pl.user_id' ,Auth::user()->id)
                        ->where('jt.create_user', '!=', Auth::user()->id)
                        ->whereBetween('jt.id', [$FROM, $TO])
                        ->whereRaw("1=1 ".$status)
                        ->where('jt.status' , '!=',3)
                        ->orderBy('jt.jo_no','desc')
                        ->groupBy(
                            'jt.jo_summary_id',
                            // 'ts.id',
                            'ts.qty_per_sheet',
                            'ts.iso_code',
                            'jt.jo_no',
                            'jt.sc_no',
                            'jt.prod_code',
                            'jt.description',
                            'ts.issued_qty',
                            'jt.material_used',
                            'jt.material_heat_no',
                            'jt.created_at',
                            'ts.status')
                        ->select(
                            DB::raw("jt.jo_summary_id as idJO"),
                            DB::raw("ts.qty_per_sheet as qty_per_sheet"),
                            DB::raw("ts.iso_code as iso_code"),
                            DB::raw("IFNULL(ts.id,'') as id"),
                            DB::raw("jt.jo_no as jo_no"),
                            DB::raw("jt.sc_no as sc_no"),
                            DB::raw("jt.prod_code as product_code"),
                            DB::raw("ifnull(pc.code_description,jt.description) as description"),
                            DB::raw("SUM(jt.order_qty) as back_order_qty"),
                            DB::raw("SUM(jt.sched_qty) as sched_qty"),
                            DB::raw("IFNULL(ts.issued_qty,0) as issued_qty"),
                            DB::raw("jt.material_used as material_used"),
                            DB::raw("jt.material_heat_no as material_heat_no"),
                            DB::raw("DATE_FORMAT(jt.created_at,'%Y-%m-%d %H:%i:%s') AS created_at"),
                            DB::raw("IFNULL(ts.status,0) as status")
                        )->get();

                foreach ($data as $key => $dt) {
                    array_push($jo_details, [
                        'qty_per_sheet' => $dt->qty_per_sheet,
                        'iso_code' => $dt->iso_code,
                        'id' => $dt->id,
                        'idJO' => $dt->idJO,
                        'jo_no' => $dt->jo_no,
                        'sc_no' => $dt->sc_no,
                        'product_code' => $dt->product_code,
                        'description' => $dt->description,
                        'back_order_qty' => $dt->back_order_qty,
                        'sched_qty' => $dt->sched_qty,
                        'issued_qty' => $dt->issued_qty,
                        'material_used' => $dt->material_used,
                        'material_heat_no' => $dt->material_heat_no,
                        'created_at' => $dt->created_at,
                        'status' => $dt->status
                    ]);
                }
        }else{
            $data = DB::table('ppc_jo_travel_sheets as jt')
                    ->leftJoin('ppc_pre_travel_sheets as ts', 'ts.jo_no','=','jt.jo_no')
                    ->leftjoin('ppc_product_codes as pc', 'jt.prod_code', '=', 'pc.product_code')
                    ->leftjoin('admin_assign_production_lines as pl', 'pl.product_line', '=', 'pc.product_type')
                    ->where('pl.user_id' ,Auth::user()->id)
                    ->where('jt.create_user' , Auth::user()->id)
                    ->whereRaw("1=1 ".$status)
                    ->where('jt.status' , '!=',3)
                    ->orderBy('jt.jo_no','desc')
                    ->groupBy(
                        'jt.jo_summary_id',
                        // 'ts.id',
                        'ts.qty_per_sheet',
                        'ts.iso_code',
                        'jt.jo_no',
                        'jt.sc_no',
                        'jt.prod_code',
                        'jt.description',
                        'ts.issued_qty',
                        'jt.material_used',
                        'jt.material_heat_no',
                        'jt.order_qty',
                        'jt.sched_qty',
                        'jt.created_at',
                        'ts.status')
                    ->select(
                        DB::raw("jt.jo_summary_id as idJO"),
                        DB::raw("ts.qty_per_sheet as qty_per_sheet"),
                        DB::raw("ts.iso_code as iso_code"),
                        DB::raw("IFNULL(ts.id,'') as id"),
                        DB::raw("jt.jo_no as jo_no"),
                        DB::raw("jt.sc_no as sc_no"),
                        DB::raw("jt.prod_code as product_code"),
                        DB::raw("ifnull(pc.code_description,jt.description) as description"),
                        DB::raw("jt.order_qty as back_order_qty"),
                        DB::raw("jt.sched_qty as sched_qty"),
                        // DB::raw("SUM(jt.order_qty) as back_order_qty"),
                        // DB::raw("SUM(jt.sched_qty) as sched_qty"),
                        DB::raw("IFNULL(ts.issued_qty,0) as issued_qty"),
                        DB::raw("jt.material_used as material_used"),
                        DB::raw("jt.material_heat_no as material_heat_no"),
                        DB::raw("DATE_FORMAT(jt.created_at,'%Y-%m-%d %H:%i:%s') AS created_at"),
                        DB::raw("IFNULL(ts.status,0) as status")
                    )->get();

            foreach ($data as $key => $dt) {
                array_push($jo_details, [
                    'qty_per_sheet' => $dt->qty_per_sheet,
                    'iso_code' => $dt->iso_code,
                    'id' => $dt->id,
                    'idJO' => $dt->idJO,
                    'jo_no' => $dt->jo_no,
                    'sc_no' => $dt->sc_no,
                    'product_code' => $dt->product_code,
                    'description' => $dt->description,
                    'back_order_qty' => $dt->back_order_qty,
                    'sched_qty' => $dt->sched_qty,
                    'issued_qty' => $dt->issued_qty,
                    'material_used' => $dt->material_used,
                    'material_heat_no' => $dt->material_heat_no,
                    'created_at' => $dt->created_at,
                    'status' => $dt->status
                ]);
            }

            $data = DB::table('ppc_jo_travel_sheets as jt')
                        ->leftJoin('ppc_pre_travel_sheets as ts', 'ts.jo_no','=','jt.jo_no')
                        ->leftjoin('ppc_product_codes as pc', 'jt.prod_code', '=', 'pc.product_code')
                        ->leftjoin('admin_assign_production_lines as pl', 'pl.product_line', '=', 'pc.product_type')
                        ->where('pl.user_id' ,Auth::user()->id)
                        ->where('jt.create_user','!=',Auth::user()->id)
                        ->whereRaw("1=1 ".$status)
                        ->where('jt.status' , '!=',3)
                        ->orderBy('jt.jo_no','desc')
                        ->groupBy(
                            'jt.id',
                            // 'ts.id',
                            'ts.qty_per_sheet',
                            'ts.iso_code',
                            'jt.jo_no',
                            'jt.sc_no',
                            'jt.prod_code',
                            'jt.description',
                            'ts.issued_qty',
                            'jt.material_used',
                            'jt.material_heat_no',
                            'jt.order_qty',
                            'jt.sched_qty',
                            'ts.status')
                        ->select(
                            DB::raw("jt.id as idJO"),
                            DB::raw("ts.qty_per_sheet as qty_per_sheet"),
                            DB::raw("ts.iso_code as iso_code"),
                            DB::raw("IFNULL(ts.id,'') as id"),
                            DB::raw("jt.jo_no as jo_no"),
                            DB::raw("jt.sc_no as sc_no"),
                            DB::raw("jt.prod_code as product_code"),
                            DB::raw("ifnull(pc.code_description,jt.description) as description"),
                            DB::raw("jt.order_qty as back_order_qty"),
                            DB::raw("jt.sched_qty as sched_qty"),
                            // DB::raw("SUM(jt.order_qty) as back_order_qty"),
                            // DB::raw("SUM(jt.sched_qty) as sched_qty"),
                            DB::raw("IFNULL(ts.issued_qty,0) as issued_qty"),
                            DB::raw("jt.material_used as material_used"),
                            DB::raw("jt.material_heat_no as material_heat_no"),
                            DB::raw("IFNULL(ts.status,0) as status")
                        )->get();

            foreach ($data as $key => $dt) {
                array_push($jo_details, [
                    'qty_per_sheet' => $dt->qty_per_sheet,
                    'iso_code' => $dt->iso_code,
                    'id' => $dt->id,
                    'idJO' => $dt->idJO,
                    'jo_no' => $dt->jo_no,
                    'sc_no' => $dt->sc_no,
                    'product_code' => $dt->product_code,
                    'description' => $dt->description,
                    'back_order_qty' => $dt->back_order_qty,
                    'sched_qty' => $dt->sched_qty,
                    'issued_qty' => $dt->issued_qty,
                    'material_used' => $dt->material_used,
                    'material_heat_no' => $dt->material_heat_no,
                    'created_at' => $dt->created_at,
                    'status' => $dt->status
                ]);
            }
        }
        return response()->json($jo_details);
    }

    public function getProcess(Request $req)
    {
        $data = [];
        $processes = [];
        $div_ids = [];
        if($req->sets == 'None'){
             $prod_processes = PpcProductProcess::where('prod_code',$req->prod_code)
                                    ->select('process','sequence')
                                    ->get();
        }else{
            $prod_processes = PpcProcess::where('set_id',$req->sets)
                                    ->select('process','sequence')
                                    ->get();
        }

        foreach ($prod_processes as $key => $process) {
            array_push($data,[
                'process' => [
                    $process->process,
                    DB::select("SELECT d.div_code as div_code
                                from ppc_division_processes as dp
                                inner join ppc_divisions as d
                                on d.id = dp.division_id
                                inner join ppc_division_productlines as dpl
                                on d.id = dpl.division_id
                                inner join admin_assign_production_lines as pl
                                on dpl.productline = pl.product_line
                                where dp.process = '".$process->process."'
                                AND pl.user_id = ".Auth::user()->id."
                                AND d.is_disable = 0
                                group by d.div_code"),
                    $process->sequence
                ],
            ]);
        }

        return response()->json($data);
    }

    public function save_travel_sheet_setup(Request $req)
    {
        if(!isset($req->issued_qty_per_sheet)){
            $data = [ 'msg' => "Please Input item in the Product Code table .", 'status' => "warning" ];
            return $data;
        }

        if(!isset($req->processes)){
            $data = [ 'msg' => "Please Input some Procces.", 'status' => "warning" ];
            return $data;
        }
        foreach ($req->processes as $key => $process) {
            if ($req->div_code[$key] == ''){
                $data = [ 'msg' => "Please Fill up all the Division Code", 'status' => "warning" ];
                return $data;
            }
        }

        foreach ($req->issued_qty_per_sheet as $key => $issued_qty_per_sheet) {
            if ($issued_qty_per_sheet == '' || $req->scno[$key] == ''){
                $data = [ 'msg' => "Please Fill up all the fields on Product Table", 'status' => "warning" ];
                return $data;
            }
        }

        $total_issued_qty = 0;
        $msgIssuedQty = 0;
        if (isset($req->travel_sheet_id) && !empty($req->travel_sheet_id)) {
            $this->validate($req, [
                'prod_code' => 'required',
                'issued_qty' => 'required|numeric',
                'qty_per_sheet' => 'required|numeric',
                'set' => 'required',
                'iso_no' => 'required'
            ]);
            $pre_ts = PpcPreTravelSheet::find($req->travel_sheet_id);
            $pre_ts->jo_no = strtoupper($req->jo_no);
            $pre_ts->prod_code = strtoupper($req->prod_code);
            $pre_ts->issued_qty = $req->issued_qty;
            $pre_ts->iso_code = $req->iso_no;
            $pre_ts->iso_name = $this->getISObyCode($req->iso_no)->iso_name;
            $pre_ts->iso_photo = $this->getISObyCode($req->iso_no)->photo;
            $pre_ts->qty_per_sheet = $req->qty_per_sheet;
            $pre_ts->update_user = Auth::user()->id;


            if ($pre_ts->update()) {
                PpcPreTravelSheetProducts::where('jo_no',$req->jo_no)->delete();

                $jo_sequence = 0;
                $page_count = count($req->issued_qty_per_sheet);
                foreach ($req->issued_qty_per_sheet as $key => $issued_qty_per_sheet) {
                     $total_issued_qty += $issued_qty_per_sheet;
                     if($total_issued_qty > $req->issued_qty){
                        $msgIssuedQty = 1;
                     }
                    if ($page_count > 1) {
                        $jo_sequence = $key + 1;
                    } else {
                        $jo_sequence = '';
                    }

                    PpcPreTravelSheetProducts::create([
                        'pre_travel_sheet_id' => $pre_ts->id,
                        'jo_no' => strtoupper($req->jo_no),
                        'prod_code' => strtoupper($req->prod_code),
                        'issued_qty_per_sheet' => $issued_qty_per_sheet,
                        'jo_sequence' => ($jo_sequence == '')? $req->jo_no : $req->jo_no.'-'.$jo_sequence,
                        'sc_no' => strtoupper(implode(',', $req->scno[$key])),
                        'create_user' => Auth::user()->id,
                        'update_user' => Auth::user()->id,
                    ]);
                }

                PpcPreTravelSheetProcess::where('pre_travel_sheet_id',$req->travel_sheet_id)->delete();
                foreach ($req->processes as $key => $process) {
                    PpcPreTravelSheetProcess::create([
                        'pre_travel_sheet_id' => $pre_ts->id,
                        'jo_no' => strtoupper($req->jo_no),
                        'set' => $req->set,
                        'process_name' => strtoupper($process),
                        'div_code' => strtoupper($req->div_code[$key]),
                        'sequence' => $req->sequence[$key],
                        'create_user' => Auth::user()->id,
                        'update_user' => Auth::user()->id,
                    ]);
                }

            }
            $this->saveProdTravelSheet($req->jo_no,$req->iso_no);

            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module_id' => $this->_moduleID,
                'module' => 'Travel Sheet',
                'action' => 'Edited Travel Sheet Jo_No: '.strtoupper($req->jo_no),
                'user' => Auth::user()->id
            ]);
        } else {
            $this->validate($req, [
                'prod_code' => 'required',
                'issued_qty' => 'required|numeric',
                'qty_per_sheet' => 'required|numeric',
                'set' => 'required',
                'iso_no' => 'required'
            ]);
            $pre_ts = new PpcPreTravelSheet();
            $pre_ts->jo_no = strtoupper($req->jo_no);
            $pre_ts->prod_code = strtoupper($req->prod_code);
            $pre_ts->issued_qty = $req->issued_qty;
            $pre_ts->qty_per_sheet = $req->qty_per_sheet;
            $pre_ts->status = 1;
            $pre_ts->iso_code = $req->iso_no;
            $pre_ts->iso_name = $this->getISObyCode($req->iso_no)->iso_name;
            $pre_ts->iso_photo = $this->getISObyCode($req->iso_no)->photo;
            $pre_ts->create_user = Auth::user()->id;
            $pre_ts->update_user = Auth::user()->id;

            if ($pre_ts->save()) {

                $jo_sequence = 0;
                $page_count = count($req->issued_qty_per_sheet);
                foreach ($req->issued_qty_per_sheet as $key => $issued_qty_per_sheet) {
                    if ($page_count > 1) {
                        $jo_sequence = $key + 1;
                    } else {
                        $jo_sequence = '';
                    }

                    PpcPreTravelSheetProducts::create([
                        'pre_travel_sheet_id' => $pre_ts->id,
                        'jo_no' => strtoupper($req->jo_no),
                        'prod_code' => strtoupper($req->prod_code),
                        'issued_qty_per_sheet' => $issued_qty_per_sheet,
                        'jo_sequence' => ($jo_sequence == '')? $req->jo_no : $req->jo_no.'-'.$jo_sequence,
                        'sc_no' => strtoupper(implode(',', $req->scno[$key])),
                        'create_user' => Auth::user()->id,
                        'update_user' => Auth::user()->id,
                    ]);
                }

                foreach ($req->processes as $key => $process) {
                    PpcPreTravelSheetProcess::create([
                        'pre_travel_sheet_id' => $pre_ts->id,
                        'jo_no' => strtoupper($req->jo_no),
                        'set' => $req->set,
                        'process_name' => strtoupper($process),
                        'sequence' => $req->sequence[$key],
                        'div_code' => strtoupper($req->div_code[$key]),
                        'create_user' => Auth::user()->id,
                        'update_user' => Auth::user()->id,
                    ]);
                }

                PpcJoTravelSheet::where('jo_no' , $req->jo_no)->update(['status' => 1 ]);
                
                $this->saveProdTravelSheet($req->jo_no,$req->iso_no);
            }
            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module_id' => $this->_moduleID,
                'module' => 'Travel Sheet',
                'action' => 'Preparation for Travel Sheet Jo_No: '.strtoupper($req->jo_no),
                'user' => Auth::user()->id
            ]);
        }
            $data = [ 
            'msg' => "Preparation for Travel Sheet was successfully saved.",
            'status' => "success",
            'travel_sheet_id' => $pre_ts->id,
            'issuedQty' => $msgIssuedQty,
            ];
        return $data;
    }

    private function saveProdTravelSheet($jo_no,$iso_code)
    {
        $ProdTrav = ProdTravelSheet::select('id')->where('jo_no',$jo_no)->get();
        foreach ($ProdTrav as $key => $pd) {
            ProdTravelSheetProcess::where('travel_sheet_id', $pd->id)->delete();
        }    
        ProdTravelSheet::where('jo_no',$jo_no)->delete();

        $travel_sheet = DB::table('ppc_pre_travel_sheet_products as tsp')
                            ->join('ppc_jo_travel_sheets as jts','tsp.jo_no','=','jts.jo_no')
                            ->where('tsp.jo_no',$jo_no)
                            ->select(
                                DB::raw('tsp.pre_travel_sheet_id as id'),
                                DB::raw('tsp.prod_code as prod_code'),
                                DB::raw('tsp.issued_qty_per_sheet as issued_qty'),
                                DB::raw('tsp.jo_sequence as jo_sequence'),
                                DB::raw('jts.jo_no as jo_no'),
                                DB::raw('tsp.sc_no as sc_no'),
                                DB::raw('jts.description as description'),
                                DB::raw('jts.order_qty as order_qty'),
                                DB::raw('jts.sched_qty as sched_qty'),
                                DB::raw('jts.material_used as material_used'),
                                DB::raw('jts.material_heat_no as material_heat_no'),
                                DB::raw('jts.lot_no as prod_heat_no'),
                                DB::raw("IF(LEFT(jts.prod_code,1) = 'Z','Finish','Semi-Finish') as type")
                            )
                            ->get();

        foreach ($travel_sheet as $key => $ts) {
            $jo = ProdTravelSheet::create([
                    'jo_no' => $jo_no,
                    'jo_sequence' => $ts->jo_sequence,
                    'prod_order_no' => $ts->sc_no,
                    'prod_code' => $ts->prod_code,
                    'description' => $ts->description,
                    'material_used' => $ts->material_used,
                    'material_heat_no' => $ts->material_heat_no,
                    'lot_no' => $ts->prod_heat_no,
                    'type' => $ts->type,
                    'order_qty' => $ts->order_qty,
                    'issued_qty' => $ts->issued_qty,
                    'total_issued_qty' => $this->getTotalIssuedQty($ts->id),
                    'status' => 0,
                    'iso_code' => $iso_code,
                    'iso_name' => $this->getISObyCode($iso_code)->iso_name,
                    'iso_photo' => $this->getISObyCode($iso_code)->photo,
                    'pre_travel_sheet_id' => $ts->id,
                    'create_user' => Auth::user()->id,
                    'update_user' => Auth::user()->id,
                ]);

            $processes = DB::table('ppc_pre_travel_sheet_processes')
                            ->where('pre_travel_sheet_id',$ts->id)
                            ->select('div_code','process_name','sequence')
                            ->get();

            foreach ($processes as $key => $proc) {
                ProdTravelSheetProcess::create([
                    'travel_sheet_id' => $jo->id,
                    'unprocessed' => ($proc->sequence == 1)? $ts->issued_qty : 0,
                    'process' => $proc->process_name,
                    'previous_process' => $this->getPrevProcess($ts->id,$proc->sequence),
                    'div_code' => $proc->div_code,
                    'sequence' => $proc->sequence,
                    'status' => 0,
                    'create_user' => Auth::user()->id,
                    'update_user' => Auth::user()->id,
                ]);
            }
        }
    }

    private function getISObyCode($code)
    {
        $iso = AdminSettingIso::where('iso_code',$code)->first();
        return $iso;
    }

    public function getPreparedby()
    {
        $users = User::select(
                        DB::raw("CONCAT(firstname,' ',lastname) as name")
                    )->where('user_type','PPC')
                    ->where('user_id','<>',Auth::user()->id)->get();

        return response()->json($users);
    }

    public function getLeader()
    {
        $leaders = PpcDivision::select('leader')->groupBy('leader')->get();
                    
        return response()->json($leaders);
    }

    public function getPreTravelSheetData(Request $req)
    {
        $data = [
            'prod' => '',
            'process' => '',
            'prod_code' => '',
            'sets' => ''
        ];

        DB::statement(DB::raw('set @row_num:=0'));

        $prod = DB::select("SELECT prod_code,
                                    issued_qty_per_sheet as issued_qty,
                                    @row_num := ifnull(@row_num,0) + 1 as id,
                                    sc_no
                            FROM ppc_pre_travel_sheet_products
                            where pre_travel_sheet_id = ".$req->id);

        $trav_sheet = PpcPreTravelSheetProducts::where('pre_travel_sheet_id',$req->id)
                                        ->select('prod_code')
                                        ->first();

        $process = PpcPreTravelSheetProcess::where('pre_travel_sheet_id',$req->id)->get();
        $process_set = PpcPreTravelSheetProcess::where('pre_travel_sheet_id',$req->id)
                            ->select('set')->first();

        $data = [
            'prod' => $prod,
            'process' => $process,
            'prod_code' => (isset($trav_sheet->prod_code))? $trav_sheet->prod_code: '',
            'sets' => (isset($process_set->set))? $process_set->set: ''
        ];

        return response()->json($data);
    }

    private function getPrevProcess($id,$sequence)
    {
        $sequence--;
        $prev = DB::table('ppc_pre_travel_sheet_processes')
                    ->where('pre_travel_sheet_id',$id)
                    ->where('sequence',$sequence)
                    ->select('process_name')
                    ->first();
        if (count((array)$prev) > 0) {
            return $prev->process_name;
        }

        return '';
    }

    private function getTotalIssuedQty($id)
    {
        $data = DB::table('ppc_pre_travel_sheet_products')
                    ->where('pre_travel_sheet_id',$id)
                    ->select(
                        DB::raw('SUM(issued_qty_per_sheet) as total_issued_qty')
                    )->first();

        if (count((array)$data) > 0) {
            return $data->total_issued_qty;
        }
    }

    public function getSc_no(Request $req)
    {
        $scno = [];
        $back_order_qty = [];

        $sc_no = DB::table('ppc_jo_details')
                    ->where('jo_summary_id',$req->id)
                    ->select('sc_no',DB::raw('SUM(back_order_qty) as back_order_qty'))
                    ->groupBy('sc_no')
                    ->get();

        foreach ($sc_no as $key => $sc) {
            array_push($scno, [$sc->sc_no]);
            array_push($back_order_qty, [$sc->back_order_qty]);
        }

        $data = [
            'sc_no' => $scno,
            'back_order_qty' => $back_order_qty
        ];
        
        return response()->json($data);
    }
}
