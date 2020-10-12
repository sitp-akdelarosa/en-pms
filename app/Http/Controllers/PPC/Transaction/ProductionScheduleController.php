<?php

namespace App\Http\Controllers\PPC\Transaction;

use App\Http\Controllers\Admin\AuditTrailController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HelpersController;
use App\PpcJoDetails;
use App\PpcJoDetailsSummary;
use App\PpcJoTravelSheet;
use App\PpcPreTravelSheet;
use App\PpcProductCode;
use App\PpcProductionSummary;
use App\PpcRawMaterialWithdrawalDetails;
use App\PpcUpdateInventory;
use App\ProdTravelSheet;
use App\ProdTravelSheetProcess;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductionScheduleController extends Controller
{

    protected $_helper = '';
    protected $_audit;
    protected $_moduleID;

    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
        $this->_helper = new HelpersController;
        $this->_audit = new AuditTrailController;

        $this->_moduleID = $this->_helper->moduleID('T0004');
    }

    public function index()
    {
        $user_accesses = $this->_helper->UserAccess();
        return view('ppc.transaction.production-schedule', ['user_accesses' => $user_accesses]);
    }

    public function GetProductionList(Request $req)
    {
        $TO = '';
        $FROM = '';
        if (isset($req->fromvalue)) {

            $from1 = PpcProductionSummary::select('id')
                ->where('sc_no', 'like', '%' . $req->fromvalue . '%')
                ->where('create_user', Auth::user()->id)
                ->orderBy('id', 'DESC')
                ->first();
            $from2 = PpcProductionSummary::select('id')
                ->where('prod_code', 'like', '%' . $req->fromvalue . '%')
                ->where('create_user', Auth::user()->id)
                ->orderBy('id', 'DESC')
                ->first();
            $to1 = PpcProductionSummary::select(DB::raw("Max(id) as ids"))
                ->where('sc_no', 'like', '%' . $req->tovalue . '%')
                ->where('create_user', Auth::user()->id)
                ->orderBy('id', 'DESC')
                ->first();
            $to2 = PpcProductionSummary::select('id')
                ->where('prod_code', 'like', '%' . $req->tovalue . '%')
                ->where('create_user', Auth::user()->id)
                ->orderBy('id', 'DESC')
                ->first();

            if (isset($from1) && isset($to1)) {
                $FROM = $from1->id;
                $TO = $to1->ids;

                if ($req->tovalue == '') {
                    $toNULL = PpcProductionSummary::select(DB::raw("Max(id) as ids"))
                        ->where('sc_no', 'like', '%' . $req->fromvalue . '%')
                        ->where('create_user', Auth::user()->id)
                        ->orderBy('id', 'DESC')
                        ->first();
                    $TO = $toNULL->ids;
                }

            }
            if (isset($from2) && isset($to2)) {
                $FROM = $from2->id;
                $TO = $to2->id;

                if ($req->tovalue == '') {
                    $TO = $FROM;
                }

            }

            $Datalist = DB::select("SELECT ps.id as id,
                                            ps.sc_no as sc_no,
                                            ps.prod_code as prod_code,
                                            ps.description as description,
                                            ps.sched_qty as sched_qty,
                                            ps.quantity as quantity,
                                            ps.po as po,
                                            ps.status as status,
                                            DATE_FORMAT(ps.date_upload, '%m/%d/%Y') as date_upload
                                    from ppc_production_summaries as ps
                                    left join ppc_product_codes as pc on ps.prod_code = pc.product_code
                                    left join admin_assign_production_lines as pl on pl.product_line = pc.product_type
                                    where pl.user_id = ".Auth::user()->id."
                                    and ps.sched_qty < ps.quantity
                                    and ps.id between ".$FROM." and ".$TO."
                                    group by ps.id,
                                            ps.sc_no,
                                            ps.prod_code,
                                            ps.description,
                                            ps.sched_qty,
                                            ps.quantity,
                                            ps.po,
                                            ps.status,
                                            DATE_FORMAT(ps.date_upload, '%m/%d/%Y')");

            return response()->json($Datalist);
        } else {
            $Datalist = DB::select("SELECT ps.id as id,
                                            ps.sc_no as sc_no,
                                            ps.prod_code as prod_code,
                                            ifnull(pc.code_description,ps.description) as description,
                                            ps.sched_qty as sched_qty,
                                            ps.quantity as quantity,
                                            ps.po as po,
                                            ps.status as status,
                                            DATE_FORMAT(ps.date_upload, '%Y-%m-%d') as date_upload
                                    from ppc_production_summaries as ps
                                    left join ppc_product_codes as pc on ps.prod_code = pc.product_code
                                    left join admin_assign_production_lines as pl on pl.product_line = pc.product_type
                                    where pl.user_id = ".Auth::user()->id."
                                    and ps.sched_qty < ps.quantity
                                    group by ps.id,
                                            ps.sc_no,
                                            ps.prod_code,
                                            ps.description,
                                            ps.sched_qty,
                                            ps.quantity,
                                            ps.po,
                                            ps.status,
                                            DATE_FORMAT(ps.date_upload, '%m/%d/%Y')"); //and ps.sched_qty < ps.quantity
            return response()->json($Datalist);
        }
    }

    public function SaveJODetails(Request $req)
    {
        $jo_no_count = [];
        $qty = [];
        $jocode = '';
        $back_order_qty_total = 0;
        $total_sched_qty = 0;
        $result = 'nothing happens';
        $jo_no = '';
        $prod_code_unique = $req->prod_code[0];
        if (empty($req->jo_no)) {

            foreach ($req->id as $key => $id) {
                if ($req->prod_code[$key] == $prod_code_unique) {
                    $back_order_qty_total += $req->quantity[$key];
                    $total_sched_qty += $req->sched_qty[$key];
                } else {
                    $back_order_qty_total = $req->quantity[$key];
                    $total_sched_qty = $req->sched_qty[$key];
                }

                if (sizeof($req->id) - 1 == $key) {
                    array_push($qty, ['order_qty' => $back_order_qty_total, 'sched_qty' => $total_sched_qty]);
                } else if ($req->prod_code[$key] != $req->prod_code[$key + 1] && $key <= sizeof($req->id) - 2) {
                    array_push($qty, ['order_qty' => $back_order_qty_total, 'sched_qty' => $total_sched_qty]);
                    $back_order_qty_total = 0;
                    $total_sched_qty = 0;
                }

                $prod_code_unique = $req->prod_code[$key];
            }

            $row = 0;
            foreach (array_unique($req->prod_code, SORT_REGULAR) as $key => $id) {

                $f = Auth::user()->firstname;
                $l = Auth::user()->lastname;

                $jocode = $this->_helper->TransactionNo($f[0] . $l[0] . '-JO');

                $jo_sum = new PpcJoDetailsSummary();
                $jo_sum->jo_no = $jocode;
                $jo_sum->total_sched_qty = $qty[$row]['sched_qty'];
                $jo_sum->rmw_no = (isset($req->rmw_no))? $req->rmw_no : '';
                $jo_sum->create_user = Auth::user()->id;
                $jo_sum->update_user = Auth::user()->id;
                $jo_sum->save();

                PpcJoTravelSheet::create([
                    'jo_no' => $jocode,
                    'sc_no' => $req->sc_no[$key],
                    'prod_code' => $req->prod_code[$key],
                    'description' => $req->description[$key],
                    'order_qty' => $qty[$row]['order_qty'],
                    'sched_qty' => $qty[$row]['sched_qty'],
                    'issued_qty' => 0,
                    'material_used' => $req->material_used[$key],
                    'material_heat_no' => $req->material_heat_no[$key],
                    'uom' => $req->uom[$key],
                    'lot_no' => $req->lot_no[$key],
                    'status' => 0,
                    'create_user' => Auth::user()->id,
                    'update_user' => Auth::user()->id,
                ]);
                $row++;
                $jo_no_count[] = $jo_sum->id;
                $jo_no = $jo_no . ' ' . $jocode;
            }

            //Overwrite
            // foreach ($req->id as $key => $id) {
            //     PpcRawMaterialWithdrawalDetails::where('material_heat_no',$req->material_heat_no[$key])
            //         ->where('create_user' , Auth::user()->id)
            //         ->update(['sc_no' =>'']);
            // }

            $prod_code_unique = $req->prod_code[0];
            $jo_no_lenght = 0;
            foreach ($req->id as $key => $id) {
                PpcProductionSummary::where('id', $id)->increment('sched_qty', $req->sched_qty[$key]);

                $rawmats = PpcRawMaterialWithdrawalDetails::where('material_heat_no', $req->material_heat_no[$key])
                    ->where('create_user', Auth::user()->id)
                    ->first();
                if (isset($rawmats->id)) {
                    $Coma = ', ';
                    if ($rawmats->sc_no == '') {
                        $Coma = ' ';
                    }
                    if (strpos($rawmats->sc_no, $req->sc_no[$key]) === false) {
                        PpcRawMaterialWithdrawalDetails::where('material_heat_no', $req->material_heat_no[$key])
                            ->where('create_user', Auth::user()->id)
                            ->update(['sc_no' => $req->sc_no[$key] . $Coma . $rawmats->sc_no]);
                    }
                }

                if ($req->prod_code[$key] == $prod_code_unique) {
                    PpcJoDetails::create([
                        'jo_summary_id' => $jo_no_count[$jo_no_lenght],
                        'sc_no' => $req->sc_no[$key],
                        'product_code' => $req->prod_code[$key],
                        'description' => $req->description[$key],
                        'back_order_qty' => $req->quantity[$key],
                        'sched_qty' => $req->sched_qty[$key],
                        'material_used' => $req->material_used[$key],
                        'material_heat_no' => $req->material_heat_no[$key],
                        'uom' => $req->uom[$key],
                        'lot_no' => $req->lot_no[$key],
                        'create_user' => Auth::user()->id,
                        'update_user' => Auth::user()->id,
                    ]);

                } else {
                    $jo_no_lenght++;
                    PpcJoDetails::create([
                        'jo_summary_id' => $jo_no_count[$jo_no_lenght],
                        'sc_no' => $req->sc_no[$key],
                        'product_code' => $req->prod_code[$key],
                        'description' => $req->description[$key],
                        'back_order_qty' => $req->quantity[$key],
                        'sched_qty' => $req->sched_qty[$key],
                        'material_used' => $req->material_used[$key],
                        'material_heat_no' => $req->material_heat_no[$key],
                        'uom' => $req->uom[$key],
                        'lot_no' => $req->lot_no[$key],
                        'create_user' => Auth::user()->id,
                        'update_user' => Auth::user()->id,
                    ]);
                }
                $prod_code_unique = $req->prod_code[$key];
            }

            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module_id' => $this->_moduleID,
                'module' => 'Production Schedule',
                'action' => 'Inserted data Jo_No:' . $jo_no,
                'user' => Auth::user()->id,
            ]);

        } else {
            $past_details = [];

            $jo_summary = PpcJoDetailsSummary::select('id', 'jo_no','rmw_no')
                ->where('jo_no', $req->jo_no)
                ->first();

            if ($jo_summary->rmw_no == null) {
                PpcJoDetailsSummary::where('id',$jo_summary->id)
                                    ->update(['rmw_no' => $req->rmw_no]);
            }

            $jo_no = $jo_summary->jo_no;

            $detailsDecrement = PpcJoDetails::select('sc_no', 'product_code', 'sched_qty')->where('jo_summary_id', $jo_summary->id)->get();

            foreach ($detailsDecrement as $key => $dt) {
                PpcProductionSummary::where('sc_no', $dt->sc_no)
                    ->where('prod_code', $dt->product_code)
                    ->decrement('sched_qty', (int) $dt->sched_qty);
            }

            PpcJoDetails::where('jo_summary_id', $jo_summary->id)->delete();

            PpcJoDetailsSummary::where('jo_no', $req->jo_no)
                ->where('create_user', Auth::user()->id)
                ->update([
                    'total_sched_qty' => $req->total_sched_qty,
                    'update_user' => Auth::user()->id,
                    'updated_at' => date('Y-m-d h:i:s'),
                ]);

            foreach ($req->id as $key => $id) {

                PpcProductionSummary::where('sc_no', $req->sc_no[$key])
                    ->where('prod_code', $req->prod_code[$key])
                    ->increment('sched_qty', $req->sched_qty[$key]);

                PpcJoDetails::create([
                    'jo_summary_id' => $jo_summary->id,
                    'sc_no' => $req->sc_no[$key],
                    'product_code' => $req->prod_code[$key],
                    'description' => $req->description[$key],
                    'back_order_qty' => $req->quantity[$key],
                    'sched_qty' => $req->sched_qty[$key],
                    'material_used' => $req->material_used[$key],
                    'material_heat_no' => $req->material_heat_no[$key],
                    'uom' => $req->uom[$key],
                    'lot_no' => $req->lot_no[$key],
                    'create_user' => Auth::user()->id,
                    'update_user' => Auth::user()->id,
                ]);

                $back_order_qty_total += $req->quantity[$key];
            }

            PpcJoTravelSheet::where('jo_no', $jo_no)->update([
                'sc_no' => $req->sc_no[0],
                'prod_code' => $req->prod_code[0],
                'description' => $req->description[0],
                'order_qty' => $back_order_qty_total,
                'sched_qty' => $req->total_sched_qty,
                'issued_qty' => 0,
                'material_used' => $req->material_used[0],
                'material_heat_no' => $req->material_heat_no[0],
                'uom' => $req->uom[0],
                'lot_no' => $req->lot_no[0],
                'status' => 0,
                'update_user' => Auth::user()->id,
            ]);

            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module_id' => $this->_moduleID,
                'module' => 'Production Schedule',
                'action' => 'Edited data Jo_No: ' . $jo_no,
                'user' => Auth::user()->id,
            ]);
        }
        return response()->json(['jocode' => $jo_no, 'result' => $result]);
    }

    public function JOsuggest(Request $req)
    {
        $jocode = PpcJoTravelSheet::select('jo_no', 'status')
            ->where('jo_no', 'like', '%' . $req->data . '%')
            ->whereIn('status', array(0, 1))
            ->get();
        return response()->json($jocode);
    }

    public function getJOviaJOno(Request $req)
    {
        $details = DB::select("SELECT d.sc_no AS sc_no,
                                    d.product_code as product_code,
                                    d.description as description,
                                    d.back_order_qty as back_order_qty,
                                    d.sched_qty as sched_qty,
                                    d.material_used as material_used,
                                    d.material_heat_no as material_heat_no,
                                    d.uom,
                                    d.lot_no as lot_no,
                                    d.create_user as create_user,
                                    d.created_at as created_at,
                                    d.update_user as update_user,
                                    d.updated_at as updated_at,
                                    s.rmw_no as rmw_no
                            FROM ppc_jo_details_summaries as s
                            JOIN ppc_jo_details as d ON d.jo_summary_id = s.id
                            LEFT JOIN ppc_jo_travel_sheets as ts ON ts.id = d.jo_summary_id
                            LEFT JOIN ppc_product_codes as pc ON d.product_code = pc.product_code
                            LEFT JOIN admin_assign_production_lines as pl ON pl.product_line = pc.product_type
                            WHERE s.jo_no = '".$req->JOno."'
                            AND ts.status IN (1,0)
                            AND pl.user_id = ".Auth::user()->id."
                            GROUP BY d.sc_no,
                                    d.product_code,
                                    d.description,
                                    d.back_order_qty,
                                    d.sched_qty,
                                    d.material_used,
                                    d.material_heat_no,
                                    d.uom,
                                    d.lot_no,
                                    d.create_user,
                                    d.created_at,
                                    d.update_user,
                                    d.updated_at,
                                    s.rmw_no");

        return response()->json($details);
    }

    public function getJOALL(Request $req)
    {
        $details = DB::table('ppc_jo_details_summaries as s')
            ->join('ppc_jo_details as d', 'd.jo_summary_id', '=', 's.id')
            ->leftjoin('ppc_jo_travel_sheets as ts', 'ts.id', '=', 'd.jo_summary_id')
            ->leftjoin('ppc_product_codes as pc', 'd.product_code', '=', 'pc.product_code')
            ->leftjoin('admin_assign_production_lines as pl', 'pl.product_line', '=', 'pc.product_type')
            ->select('d.sc_no',
                DB::raw('d.product_code as product_code'),
                DB::raw('d.description as description'),
                DB::raw('d.back_order_qty as back_order_qty'),
                DB::raw('d.sched_qty as sched_qty'),
                DB::raw('d.material_used as material_used'),
                DB::raw('d.material_heat_no as material_heat_no'),
                DB::raw('d.uom as uom'),
                DB::raw('d.lot_no as lot_no'))
            ->where('pl.user_id', Auth::user()->id)
            ->whereIn('ts.status', array(1, 0))
            ->groupBy('d.sc_no',
                'd.product_code',
                'd.description',
                'd.back_order_qty',
                'd.sched_qty',
                'd.material_used',
                'd.material_heat_no',
                'd.uom',
                'd.lot_no')
            ->get();
        return response()->json($details);
    }

    public function getMaterialUsed(Request $req)
    {
        $inv = PpcUpdateInventory::groupBy('description')
                                    ->where('heat_no', $req->heat_no)
                                    ->select('description','size','schedule')->get();
        return response()->json($inv);
    }

    public function getStandardMaterialUsed(Request $req)
    {
        $p_code = PpcProductCode::where('product_code', $req->product_code)
            ->select('standard_material_used')->first();
        return response()->json($p_code);
    }

    public function getMaterialHeatNo(Request $req)
    {
        $data = [
            'msg' => 'No items withdrawed for Withdrawal Slip # '.$req->rmw_no.'.',
            'status' => 'failed',
            'materials' => []
        ];

        $rmw = DB::table('ppc_raw_material_withdrawal_infos')
                    ->select('id')->where('trans_no',$req->rmw_no)->first();

        $with_rmw = '';

        if (count((array)$rmw) > 0) {
            $with_rmw = " AND rmw.trans_id = ".$rmw->id;
        }

        $heat_no = [];

        $materials = DB::select("SELECT pui.heat_no as heat_no,
                                    ifnull(rmw.issued_uom,'') as uom,
                                    ifnull(rmw.issued_qty,0) as rmw_issued_qty
                            FROM ppc_update_inventories as pui
                            left join admin_assign_production_lines as apl on apl.product_line = pui.materials_type
                            left join ppc_raw_material_withdrawal_details as rmw on pui.heat_no = rmw.material_heat_no
                            WHERE apl.user_id = ".Auth::user()->id.$with_rmw."
                            group by pui.heat_no,
                                    rmw.issued_qty
                            ORDER BY pui.id desc");

        if ($this->_helper->check_if_exists($materials) > 0) {
            foreach ($materials as $key => $material) {
                $exists = PpcJoDetails::where('material_heat_no', $material->heat_no)
                                        ->count();
                if ($exists < 1) {
                    array_push($heat_no,[
                        'heat_no' => $material->heat_no,
                        'uom' => $material->uom, 
                        'rmw_issued_qty' => $material->rmw_issued_qty
                    ]);
                }
            }

            $data = [
                'msg' => '',
                'status' => '',
                'materials' => $heat_no
            ];

            if (count($heat_no) < 1) {
                $data = [
                    'msg' => 'All of Heat Number in Withdrawal Slip # '.$req->rmw_no.' is already scheduled.',
                    'status' => 'failed',
                    'materials' => $heat_no
                ];
            }

            
        }
        
        return response()->json($data);
    }

    public function getTravel_sheet(Request $req)
    {
        $jo_details = [];
        $data = DB::table('ppc_jo_travel_sheets as jt')
            ->leftJoin('ppc_pre_travel_sheets as ts', 'ts.jo_no', '=', 'jt.jo_no')
            ->leftjoin('ppc_product_codes as pc', 'jt.prod_code', '=', 'pc.product_code')
            ->leftjoin('admin_assign_production_lines as pl', 'pl.product_line', '=', 'pc.product_type')
            ->where('pl.user_id', Auth::user()->id)
            ->where('jt.create_user', Auth::user()->id)
            ->whereIn('jt.status', array(0, 1))
            ->orderBy('jt.jo_no', 'desc')
            ->groupBy(
                'jt.id',
                'ts.id',
                'ts.qty_per_sheet',
                'ts.iso_code',
                'jt.jo_no',
                'jt.sc_no',
                'jt.prod_code',
                'jt.description',
                'ts.issued_qty',
                'jt.material_used',
                'jt.material_heat_no',
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
                DB::raw("SUM(jt.order_qty) as back_order_qty"),
                DB::raw("SUM(jt.sched_qty) as sched_qty"),
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
                'status' => $dt->status,
            ]);
        }

        $data = DB::table('ppc_jo_travel_sheets as jt')
            ->leftJoin('ppc_pre_travel_sheets as ts', 'ts.jo_no', '=', 'jt.jo_no')
            ->leftjoin('ppc_product_codes as pc', 'jt.prod_code', '=', 'pc.product_code')
            ->leftjoin('admin_assign_production_lines as pl', 'pl.product_line', '=', 'pc.product_type')
            ->where('pl.user_id', Auth::user()->id)
            ->where('jt.create_user', '!=', Auth::user()->id)
            ->whereIn('jt.status', array(0, 1))
            ->orderBy('jt.jo_no', 'desc')
            ->groupBy(
                'jt.id',
                'ts.id',
                'ts.qty_per_sheet',
                'ts.iso_code',
                'jt.jo_no',
                'jt.sc_no',
                'jt.prod_code',
                'jt.description',
                'ts.issued_qty',
                'jt.material_used',
                'jt.material_heat_no',
                'ts.status')
            ->select(
                DB::raw("jt.id as idJO"),
                DB::raw("ts.qty_per_sheet as qty_per_sheet"),
                DB::raw("ts.iso_code as iso_code"),
                DB::raw("IFNULL(ts.id,'') as id"),
                DB::raw("jt.jo_no as jo_no"),
                DB::raw("jt.sc_no as sc_no"),
                DB::raw("jt.prod_code as product_code"),
                DB::raw("jt.description as description"),
                DB::raw("SUM(jt.order_qty) as back_order_qty"),
                DB::raw("SUM(jt.sched_qty) as sched_qty"),
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
                'status' => $dt->status,
            ]);
        }
        return response()->json($jo_details);
    }

    public function cancel_TravelSheet(Request $req)
    {
        PpcJoTravelSheet::where('id', $req->idJTS)->update(['status' => 3]);

        $detailsDecrement = PpcJoDetails::select('sc_no', 'product_code', 'sched_qty', 'material_heat_no')->where('jo_summary_id', $req->idJTS)->get();
        foreach ($detailsDecrement as $key => $dt) {
            PpcProductionSummary::where('sc_no', $dt->sc_no)
                ->where('prod_code', $dt->product_code)
                ->decrement('sched_qty', (int) $dt->sched_qty);

            PpcRawMaterialWithdrawalDetails::where('material_heat_no', $dt->material_heat_no)
                ->where('create_user', Auth::user()->id)
                ->update(['sc_no' => DB::raw("REPLACE(sc_no, '" . $dt->sc_no . ", ', '')")]);

            PpcRawMaterialWithdrawalDetails::where('material_heat_no', $dt->material_heat_no)
                ->where('create_user', Auth::user()->id)
                ->update(['sc_no' => DB::raw("REPLACE(sc_no, '" . $dt->sc_no . "', '')")]);
        }

        PpcPreTravelSheet::where('id', $req->id)->update(['status' => 3]);

        ProdTravelSheet::where('pre_travel_sheet_id', $req->id)->update(['status' => 3]);

        if (isset($pts)) {
            foreach ($pts as $k => $v) {
                ProdTravelSheetProcess::where('travel_sheet_id', $v->id)->update(['status' => 3]);
            }
        }
        $data = ['status' => "success"];

        return response()->json($data);
    }
}
