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
                                            ifnull(pc.code_description,ps.description) as description,
                                            ps.sched_qty as sched_qty,
                                            ps.quantity as quantity,
                                            ps.po as po,
                                            ps.status as status,
                                            DATE_FORMAT(ps.date_upload, '%Y-%m-%d') as date_upload
                                    from ppc_production_summaries as ps
                                    inner join ppc_product_codes as pc on ps.prod_code = pc.product_code
                                    inner join admin_assign_production_lines as pl on pl.product_line = pc.product_type
                                    where pl.user_id = ".Auth::user()->id." AND ps.create_user = ".Auth::user()->id."
                                     and ps.sched_qty < ps.quantity
                                    and ps.id between ".$FROM." and ".$TO."
                                    group by ps.id,
                                            ps.sc_no,
                                            ps.prod_code,
                                            pc.code_description,
                                            ps.description,
                                            ps.sched_qty,
                                            ps.quantity,
                                            ps.po,
                                            ps.status,
                                            DATE_FORMAT(ps.date_upload, '%Y-%m-%d')");

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
                                    inner join ppc_product_codes as pc on ps.prod_code = pc.product_code
                                    inner join admin_assign_production_lines as pl on pl.product_line = pc.product_type
                                    where pl.user_id = ".Auth::user()->id." AND ps.create_user = ".Auth::user()->id."
                                     and ps.sched_qty < ps.quantity
                                    group by ps.id,
                                            ps.sc_no,
                                            ps.prod_code,
                                            pc.code_description,
                                            ps.description,
                                            ps.sched_qty,
                                            ps.quantity,
                                            ps.po,
                                            ps.status,
                                            DATE_FORMAT(ps.date_upload, '%Y-%m-%d')"); //and ps.sched_qty < ps.quantity
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
                    'jo_summary_id' => $jo_sum->id,
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

                $rawmats = PpcRawMaterialWithdrawalDetails::where('id', $req->rmw_id[$key])
                    ->where('create_user', Auth::user()->id)
                    ->first();
                if (isset($rawmats->id)) {
                    $Coma = ', ';
                    if ($rawmats->sc_no == '') {
                        $Coma = ' ';
                    }
                    if (strpos($rawmats->sc_no, $req->sc_no[$key]) === false) {
                        PpcRawMaterialWithdrawalDetails::where('id', $req->rmw_id[$key])
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
                        'inv_id' => $req->inv_id[$key],
                        'rmw_id' => $req->rmw_id[$key],
                        'rmw_issued_qty' => $req->rmw_issued_qty[$key],
                        'material_type' => $req->material_type[$key],
                        'for_over_issuance' => $req->for_over_issuance[$key],
                        'heat_no_id' => $req->heat_no_id[$key]
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
                        'inv_id' => $req->inv_id[$key],
                        'rmw_id' => $req->rmw_id[$key],
                        'rmw_issued_qty' => $req->rmw_issued_qty[$key],
                        'material_type' => $req->material_type[$key],
                        'for_over_issuance' => $req->for_over_issuance[$key],
                        'heat_no_id' => $req->heat_no_id[$key]
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
                'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
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
                    'inv_id' => $req->inv_id[$key],
                    'rmw_id' => $req->rmw_id[$key],
                    'rmw_issued_qty' => $req->rmw_issued_qty[$key],
                    'material_type' => $req->material_type[$key],
                    'for_over_issuance' => $req->for_over_issuance[$key],
                    'heat_no_id' => $req->heat_no_id[$key]
                ]);

                $back_order_qty_total += $req->quantity[$key];
            }

            PpcJoTravelSheet::where('jo_summary_id', $jo_summary->id)->update([
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
                // 'inv_id' => $req->inv_id[$key],
                // 'rmw_id' => $req->rmw_id[$key],
                // 'rmw_issued_qty' => $req->rmw_issued_qty[$key],
                // 'material_type' => $req->material_type[$key],
                // 'for_over_issuance' => $req->for_over_issuance[$key],
                // 'heat_no_id' => $req->heat_no_id[$key]
            ]);

            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module_id' => $this->_moduleID,
                'module' => 'Production Schedule',
                'action' => 'Edited data Jo_No: ' . $jo_no,
                'user' => Auth::user()->id,
                'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
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
                                    ifnull(d.uom,'') as uom,
                                    d.lot_no as lot_no,
                                    (SELECT cu.nickname
                                        FROM users as cu
                                        where cu.id = d.create_user) as create_user,
                                    (SELECT uu.nickname
                                        FROM users as uu
                                        where uu.id = d.update_user) as update_user,
                                    d.created_at as created_at,
                                    d.updated_at as updated_at,
                                    s.rmw_no as rmw_no,
                                    ifnull(pui.id,0) as inv_id,
                                    d.rmw_id,
                                    d.rmw_issued_qty,
                                    d.material_type,
                                    d.for_over_issuance,
                                    d.heat_no_id
                            FROM ppc_jo_details_summaries as s
                            JOIN ppc_jo_details as d ON d.jo_summary_id = s.id
                            LEFT JOIN ppc_jo_travel_sheets as ts ON ts.id = d.jo_summary_id and ts.sc_no = d.sc_no
                            LEFT JOIN ppc_product_codes as pc ON d.product_code = pc.product_code
                            LEFT JOIN admin_assign_production_lines as pl ON pl.product_line = pc.product_type
                            LEFT JOIN inventories as pui ON d.inv_id = pui.id
                            WHERE s.jo_no = '".$req->JOno."'
                            -- AND ts.status IN (1,0)
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
                                    s.rmw_no,
                                    d.rmw_id,
                                    d.rmw_issued_qty,
                                    d.material_type,
                                    d.for_over_issuance,
                                    d.heat_no_id,
                                    pui.id");

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
        $rmw_id = $req->heat_no; // this is now id from raw mats withdrawal 
        $inv = DB::table('ppc_update_inventories as pui')
                    ->join('ppc_raw_material_withdrawal_details as rmw','rmw.inv_id','=','pui.id')
                    ->select('pui.description',
                            'pui.size',
                            'pui.schedule',
                            'pui.heat_no as heat_no',
                            DB::raw("ifnull(rmw.issued_uom,'') as uom"),
                            DB::raw("ifnull(rmw.issued_qty,0) as rmw_issued_qty"),
                            DB::raw("ifnull(rmw.scheduled_qty,0) as rmw_scheduled_qty"),
                            'rmw.id as rmw_id',
                            'rmw.inv_id as inv_id',
                            'pui.length as rmw_length',
                            'pui.id as upd_inv_id'
                    )->where('rmw.id', $rmw_id)
                    ->groupBy('pui.description',
                            'pui.size',
                            'pui.schedule',
                            'rmw.id',
                            'rmw.inv_id',
                            'pui.length',
                            'pui.id',
                            'rmw.issued_uom',
                            'rmw.issued_qty',
                            'rmw.scheduled_qty'
                    )->get();
        return response()->json($inv);
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
            $with_rmw = " AND rmwi.trans_no = '".$req->rmw_no."'";

            $heat_no = [];

            $materials = DB::select("SELECT rmwi.trans_no,
                                        rmw.trans_id,
                                        pui.heat_no as heat_no,
                                        ifnull(rmw.issued_uom,'') as uom,
                                        ifnull(rmw.issued_qty,0) as rmw_issued_qty,
                                        ifnull(rmw.scheduled_qty,0) as rmw_scheduled_qty,
                                        rmw.id as rmw_id,
                                        rmw.inv_id as inv_id,
                                        pui.length as rmw_length,
                                        pui.id as upd_inv_id,
                                        pui.item_code as item_code,
                                        '' as lot_no,
                                        CASE 
                                            WHEN pui.materials_type LIKE '%BAR%' THEN 'BAR'
                                            WHEN pui.materials_type LIKE '%PIPE%' THEN 'PIPE'
                                            WHEN pui.materials_type LIKE '%PLATE%' THEN 'PLATE'
                                            ELSE ''
                                        END as material_type,
                                        CONCAT(
                                            pui.heat_no,
                                            IF(pui.length = 'N/A','', CONCAT(' | (',pui.length,')') ),
                                            CONCAT( ' | (' ,ifnull(rmw.issued_qty,0), ')' )
                                        ) as `text`,
                                        CASE
                                            WHEN pui.materials_type LIKE '%BAR%' THEN
                                                pui.length / ((((SELECT cut_weight from ppc_product_codes
                                                where product_code = '".$req->prod_code."') / TRIM(BOTH 'MM' FROM TRIM(BOTH 'mm' FROM pui.size )) / TRIM(BOTH 'MM' FROM TRIM(BOTH 'mm' FROM pui.size ))/6.2)*1000000)+1.5)
                                            WHEN pui.materials_type LIKE '%PIPE%' THEN
                                                (pui.length / ((SELECT cut_length from ppc_product_codes
                                                where product_code = '".$req->prod_code."')+1.5) )*2
                                            WHEN pui.materials_type LIKE '%PLATE%' THEN
                                                (
                                                    (pui.length*pui.width)/((SELECT cut_length from ppc_product_codes
                                                    where product_code = '".$req->prod_code."') * (SELECT cut_width from ppc_product_codes
                                                    where product_code = '".$req->prod_code."'))
                                                )
                                            ELSE 0
                                        END as for_over_issuance,
                                        (SELECT standard_material_used from ppc_product_codes
                                                    where product_code = '".$req->prod_code."') as standard_material_used,
                                        pui.description as description,
                                        pui.size as size,
                                        pui.`schedule` as `schedule`
                                    FROM ppc_update_inventories as pui
                                    left join admin_assign_material_types as apl 
                                    on apl.material_type = pui.materials_type

                                    inner join ppc_raw_material_withdrawal_details as rmw 
                                    on pui.heat_no = rmw.material_heat_no
                                    and pui.id = rmw.inv_id

                                    inner join ppc_raw_material_withdrawal_infos as rmwi 
                                    on rmw.trans_id = rmwi.id

                                    WHERE rmw.issued_qty <> 0 AND apl.user_id = ".Auth::user()->id.$with_rmw."
                                    group by rmwi.trans_no,
                                        rmw.trans_id,
                                        pui.id,
                                        pui.heat_no,
                                        rmw.issued_qty,
                                        rmw.scheduled_qty,
                                        rmw.id,
                                        rmw.inv_id,
                                        pui.item_code,
                                        pui.length,
                                        pui.description,
                                        pui.size,
                                        pui.`schedule`,
                                        pui.materials_type
                                    ORDER BY pui.id desc");

            
        } else {
            $pw = DB::table('ppc_product_withdrawal_infos')
                    ->select('id')->where('trans_no',$req->rmw_no)->first();

            if (count((array)$pw) > 0) {
                $heat_no = [];

                $materials = DB::select("SELECT pwi.trans_no,
                                            pw.trans_id,
                                            pui.heat_no as heat_no,
                                            ifnull(pw.issued_uom,'') as uom,
                                            ifnull(pw.issued_qty,0) as rmw_issued_qty,
                                            ifnull(pw.scheduled_qty,0) as rmw_scheduled_qty,
                                            pw.id as rmw_id,
                                            pw.inv_id as inv_id,
                                            pui.length as rmw_length,
                                            pui.id as upd_inv_id,
                                            pui.item_code as item_code,
                                            pui.item_class as material_type,
                                            pui.lot_no as lot_no,
                                            CONCAT(
                                                pui.heat_no,
                                                IF(pui.length = 'N/A','', CONCAT(' | (',pui.length,')') ),
                                                CONCAT( ' | (' ,ifnull(pw.issued_qty,0), ')' )
                                            ) as `text`,
                                            '0' as for_over_issuance,
                                            ppc.standard_material_used as standard_material_used,
                                            pui.description as description,
                                            pui.size as size,
                                            pui.`schedule` as `schedule`
                                        FROM ppc_update_inventories as pui
                                        inner join ppc_product_codes as ppc
                                        on pui.item_code = ppc.product_code

                                        left join admin_assign_production_lines as apl 
                                        on apl.product_line = ppc.product_type

                                        inner join ppc_product_withdrawal_details as pw 
                                        on pui.heat_no = pw.heat_no
                                        and pui.id = pw.inv_id

                                        inner join ppc_product_withdrawal_infos as pwi 
                                        on pw.trans_id = pwi.id

                                        WHERE pw.issued_qty <> 0 
                                        AND apl.user_id = ".Auth::user()->id."
                                        AND pwi.trans_no = '".$req->rmw_no."'

                                        group by pwi.trans_no,
                                            pw.trans_id,
                                            pui.id,
                                            pui.heat_no,
                                            pw.issued_qty,
                                            pw.scheduled_qty,
                                            pw.id,
                                            pw.inv_id,
                                            pui.item_code,
                                            pui.length,
                                            pui.description,
                                            pui.size,
                                            pui.`schedule`,
                                            ppc.standard_material_used,
                                            pui.item_class,
                                            pui.lot_no
                                        ORDER BY pui.id desc");
                
            }
        }

        if ($this->_helper->check_if_exists($materials) > 0) {
                if ($req->state == 'edit') {
                    $data = [
                        'msg' => '',
                        'status' => '',
                        'materials' => $materials
                    ];
                } else {
                    foreach ($materials as $key => $material) {
                        $exists = PpcJoDetails::where('rmw_id', $material->rmw_id)
                                                ->where('sched_qty',$material->rmw_issued_qty)
                                                ->count();
                        if ($exists < 1) {
                            array_push($heat_no,[
                                'heat_no' => $material->heat_no,
                                'uom' => $material->uom, 
                                'rmw_issued_qty' => $material->rmw_issued_qty,
                                'rmw_scheduled_qty' => $material->rmw_scheduled_qty,
                                'rmw_id' => $material->rmw_id,
                                'inv_id' => $material->inv_id,
                                'rmw_length' => $material->rmw_length,
                                'upd_inv_id' => $material->upd_inv_id,
                                'id' => $material->rmw_id,
                                'text' => $material->text,
                                'material_type' => $material->material_type,
                                'for_over_issuance' => $material->for_over_issuance,
                                'standard_material_used' => $material->standard_material_used,
                                'description' => $material->description,
                                'size' => $material->size,
                                'schedule' => $material->schedule,
                                'item_code' => $material->item_code,
                                'lot_no' => $material->lot_no
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
            }
        
        return response()->json($data);
    }

    public function getStandardMaterialUsed(Request $req)
    {
        $p_code = PpcProductCode::where('product_code', $req->product_code)
            ->select('standard_material_used')->first();
        return response()->json($p_code);
    }

    private function getSameArrayValues($array)
    {
        $counts = array_count_values($array);
        $filtered = array_filter($array, function ($value) use ($counts) {
            return $counts[$value] > 1;
        });

        return $filtered;
    }

    public function calculateOverIssuance(Request $req)
    {
        $material_type = '';

        $heat_by_rmw_qty = [];
        $heat_by_sched_qty = [];
        $heat_by_prod_code = [];
        $heat_by_inv_id = [];

        $data = [];

        foreach ($req->rmw_id as $key => $rmw_id) {
            if (array_key_exists($rmw_id,$heat_by_sched_qty)) {
                $heat_by_sched_qty[$rmw_id] = $heat_by_sched_qty[$rmw_id] + $req->sched_qty[$key];
            } else {
                $heat_by_sched_qty[$rmw_id] = $req->sched_qty[$key];
            }
        }

        foreach ($req->rmw_id as $key => $rmw_id) {
            $heat_by_rmw_qty[$rmw_id] = $req->rmw_issued_qty[$key];
        }

        foreach ($req->prod_code as $key => $prod_code) {
            $heat_by_prod_code[$prod_code] = $req->rmw_id[$key];
        }

        foreach ($req->rmw_id as $key => $rmw_id) {
            $heat_by_inv_id[$rmw_id] = $req->inv_id[$key];
        }
        

        foreach ($heat_by_sched_qty as $key => $sched_qty) {

            $prod_cond = "";
            $arr_prod_code = $this->getSameArrayValues($heat_by_prod_code);

            $prod = DB::table('ppc_product_codes')->select('cut_weight','cut_length','cut_width','product_code');
                        

            if (count($arr_prod_code) > 0) {
                $prod_key = array_search($key, $arr_prod_code);
                $prod->whereIn('product_code',$prod_key);
            } else {
                $prod->whereIn('product_code',$heat_by_prod_code[$key]);
            }

            $prod->first();

            $inventory = DB::table('inventories')
                            ->select(
                                'materials_type',
                                'heat_no',
                                'id',
                                DB::raw("TRIM(TRAILING 'MM' FROM length ) as length"),
                                DB::raw("TRIM(TRAILING 'MM' FROM width ) as width"),
                                DB::raw("TRIM(TRAILING 'MM' FROM size ) AS size")
                            )
                            ->where('id',$key)
                            ->first();

            // get product cut weight
            

            if ($this->_helper->check_if_exists($inventory) > 0) {
                $material_type = $inventory->materials_type;
            }

            switch ($material_type) {
                case 'S/S BAR':
                case 'C/S BAR':
                    $data = $this->calculateBar(
                                    $prod,
                                    $inventory,
                                    $heat_by_rmw_qty[$key],
                                    $sched_qty
                                );
                    break;

                case 'S/S PLATE':
                    $data = $this->calculatePlate(
                                    $prod,
                                    $inventory
                                );
                    break;
                    
                case 'S/S PIPE':
                    $data = $this->calculatePipe(
                                    $prod,
                                    $inventory,
                                    $heat_by_rmw_qty[$key]
                                );
                    break;
                
                default:
                    $data = [
                        'msg' => 'No Formula assigned for this Material.',
                        'status' => 'failed'
                    ];
                    break;
            }
        }

        return $data;
    }

    private function calculateBar($prod,$inventory,$rmw_issued_qty,$sched_qty)
    {
        $data = [
            'msg' => 'Calculating failed.',
            'status' => 'failed',
            'type' => 'BAR',
            'stock' => 0
        ];

        $OD_size = 0;
        $bar_pcs = 0;
        $product_cut_length = 0;
        $cut_weight = 0;
        $length = 0;
        $mat_length = 0;
        $stock = 0;
        $length_with_1p5 = 0;
        $rmw_qty = (int)$rmw_issued_qty;

        if ($this->_helper->check_if_exists($prod) > 0) {
            $cut_weight = $prod->cut_weight;
            
            if ($this->_helper->check_if_exists($inventory) > 0) {
                $OD_size = (float)$inventory->size;
                $mat_length = (float)$inventory->length;
                // calculate length
                $product_cut_length = ($cut_weight / $OD_size / $OD_size / 6.2) * 1000000;

                // get length
                $length_with_1p5 = $product_cut_length + 1.5;
                $length = $mat_length/$length_with_1p5;

                // calculate PCS
                $bar_pcs = (float)$sched_qty/$length;

                // Calculate stocks
                $stock = $rmw_qty - (int)$bar_pcs;

                $data = [
                    'msg' => '',
                    'status' => 'success',
                    'type' => 'BAR',
                    'stock' => $stock
                ];
            } else {
                $data = [
                    'msg' => "Material doesn't exist in Inventory",
                    'status' => 'failed',
                    'type' => 'BAR',
                    'stock' => 0
                ];
            }
        } else {
            $data = [
                'msg' => "Product Code doesn't exist in Product Master",
                'status' => 'failed',
                'type' => 'BAR',
                'stock' => 0
            ];
        }      

        return $data;
    }

    private function calculatePipe($prod,$inventory)
    {
        $data = [
            'msg' => 'Calculating failed.',
            'status' => 'failed',
            'type' => 'PIPE',
            'stock' => 0
        ];

        $pipe_pcs = 0;
        $product_cut_length = 0;
        $inv_length = 0;
        $pipe_pcs = 0;
        $stock = 0;
        $length_wth_1p8 = 0;
        $rmw_qty = (int)$rmw_issued_qty;
        $pcs = 0;

        if ($this->_helper->check_if_exists($prod) > 0) {
            $product_cut_length = $prod->cut_length;
            
            if ($this->_helper->check_if_exists($inventory) > 0) {
                $inv_length = (float)$inventory->length;

                $length_wth_1p8 = $product_cut_length + 1.5;

                // calculate pipe length
                $pipe_pcs = ($inv_length / $length_wth_1p8)*2; // somehow addition 1.5 for product cut length

                // Calculate stocks
                // $pcs = $rmw_qty * $pipe_pcs;
                // $stock =  $pcs - $req->sched_qty;
                $stock = $rmw_qty * (int)$pipe_pcs;

                $data = [
                    'msg' => '',
                    'status' => 'success',
                    'type' => 'PIPE',
                    'stock' => $stock
                ];
            } else {
                $data = [
                    'msg' => "Material doesn't exist in Inventory",
                    'status' => 'failed',
                    'type' => 'PIPE',
                    'stock' => 0
                ];
            }
        } else {
            $data = [
                'msg' => "Product Code doesn't exist in Product Master",
                'status' => 'failed',
                'type' => 'PIPE',
                'stock' => 0
            ];
        }      

        return $data;
    }

    private function calculatePlate($prod,$inventory,$req)
    {
        $data = [
            'msg' => 'Calculating failed.',
            'status' => 'failed',
            'type' => 'PLATE',
            'stock' => 0
        ];

        $product_cut_length = 0;
        $product_cut_width = 0;
        $inv_length = 0;
        $inv_width = 0;
        $prod_plate = 0;
        $mat_plate = 0;
        $stock = 0;

        if ($this->_helper->check_if_exists($prod) > 0) {
            $product_cut_length = $prod->cut_length;
            $product_cut_width = $prod->cut_width;
            
            if ($this->_helper->check_if_exists($inventory) > 0) {
                $inv_length = (float)$inventory->length;
                $inv_width = (float)$inventory->width;

                // calculate product plate
                $prod_plate = ($product_cut_length * $product_cut_width);

                // calculate material plate
                $mat_plate = ($inv_length * $inv_width); // somehow addition 1.8 for product cut length

                // Calculate stocks
                $stock = $mat_plate / $prod_plate;

                $data = [
                    'msg' => '',
                    'status' => 'success',
                    'type' => 'PLATE',
                    'stock' => $stock
                ];
            } else {
                $data = [
                    'msg' => "Material doesn't exist in Inventory",
                    'status' => 'failed',
                    'type' => 'PLATE',
                    'stock' => 0
                ];
            }
        } else {
            $data = [
                'msg' => "Product Code doesn't exist in Product Master",
                'status' => 'failed',
                'type' => 'PLATE',
                'stock' => 0
            ];
        }      

        return $data;
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
