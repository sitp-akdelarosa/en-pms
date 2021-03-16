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
use App\TempItemMaterial;
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
        $permission_access = $this->_helper->check_permission('T0004');

        return view('ppc.transaction.production-schedule', [
            'user_accesses' => $user_accesses,
            'permission_access' => $permission_access
        ]);
    }

    /**
     * Get Orders
     */

    public function getOrders(Request $req)
    {
        $Datalist = DB::select(DB::raw("CALL GET_production_summaries(".Auth::user()->id.",NULL,NULL,NULL,NULL,NULL,NULL)"));
        return DataTables::of($Datalist)
						->editColumn('id', function($data) {
							return $data->id;
                        })
                        ->editColumn('status', function($data) {
                            switch ($data->status) {
                                case 0:
                                    return '';
                                    break;
                                case 1:
                                    return 'Travel Sheet Prepared';
                                    break;
                                case 2:
                                    return 'On Production';
                                    break;
                                case 3:
                                    return 'Travel Sheet Cancelled';
                                    break;
                                case 4:
                                    return 'Scheduled';
                                    break;
                                case 5:
                                    return 'CLOSED';
                                    break;
                            }
                        })
						->make(true);
    }

    /**
     * Filter Orders
     */
    public function filterOrders(Request $req)
    {
         $Datalist = $this->getFilteredOrders($req);
         return DataTables::of($Datalist)
						->editColumn('id', function($data) {
							return $data->id;
                        })
                        ->editColumn('status', function($data) {
                            switch ($data->status) {
                                case 0:
                                    return '';
                                    break;
                                case 1:
                                    return 'Travel Sheet Prepared';
                                    break;
                                case 2:
                                    return 'On Production';
                                    break;
                                case 3:
                                    return 'Travel Sheet Cancelled';
                                    break;
                                case 4:
                                    return 'Scheduled';
                                    break;
                                case 5:
                                    return 'CLOSED';
                                    break;
                            }
                        })
						->make(true);
    }

    /**
     * Get Filtered Orders
     */
    public function getFilteredOrders($req)
    {
        $srch_date_upload_from = "NULL";
        $srch_date_upload_to = "NULL";
        $srch_sc_no = "NULL";
        $srch_prod_code = "NULL";
        $srch_description = "NULL";
        $srch_po = "NULL";

        if (!is_null($req->srch_date_upload_from) && !is_null($req->srch_date_upload_to)) {
            $srch_date_upload_from = "'".$req->srch_date_upload_from."'";
            $srch_date_upload_to = "'".$req->srch_date_upload_to."'";
        }

        if (!is_null($req->srch_sc_no)) {
            $srch_sc_no = "'".$req->srch_sc_no."'";
        }

        if (!is_null($req->srch_prod_code)) {
            $srch_prod_code = "'".$req->srch_prod_code."'";
        }

        if (!is_null($req->srch_description)) {
            $srch_description = "'".$req->srch_description."'";
        }

        if (!is_null($req->srch_po)) {
            $srch_po = "'".$req->srch_po."'";
        }

        $Datalist = DB::select(
                        DB::raw(
                            "CALL GET_production_summaries(
                                ".Auth::user()->id.",
                                ".$srch_date_upload_from.",
                                ".$srch_date_upload_to.",
                                ".$srch_sc_no.",
                                ".$srch_prod_code.",
                                ".$srch_description.",
                                ".$srch_po."
                                )"
                        )
                    );
        return $Datalist;
    }

    /**
     * Get Materials
     */
    public function getMaterials(Request $req)
    {
        $data = DB::select(DB::raw("CALL GET_withdrawals('".$req->rmw_no."','".$req->_token."',".Auth::user()->id.")"));
        return DataTables::of($data)
                        ->editColumn('action', function($data) {
                            return "<button class='btn btn-sm bg-red btn_remove_material'>
                                        <i class='fa fa-times'></i>
                                    </button>
                                    <button class='btn btn-sm bg-blue btn_open_modal' 
                                        data-rmwd_id='".$data->rmwd_id."' 
                                        data-inv_id='".$data->inv_id."'
                                        data-rmw_no='".$data->rmw_no."'
                                        data-item_code='".$data->item_code."' 
                                        data-description='".$data->description."'
                                        data-heat_no='".$data->heat_no."'
                                        data-rmw_qty='".$data->rmw_qty."'
                                        >
                                        <i class='fa fa-edit'></i>
                                    </button>";
                        })->make(true);
    }

    /**
     * Get Products
     */
    public function getProducts(Request $req)
    {
        $Datalist = DB::table('v_item_order_details')->where('user_id', Auth::user()->id);
        return DataTables::of($Datalist)
						->editColumn('action', function($data) {
							return "<input type='checkbox' class='table-checkbox chk_product' value='".$data->id."'/>";
                        })
                        ->editColumn('status', function($data) {
                            switch ($data->status) {
                                case 0:
                                    return '';
                                    break;
                                case 1:
                                    return 'Travel Sheet Prepared';
                                    break;
                                case 2:
                                    return 'On Production';
                                    break;
                                case 3:
                                    return 'Travel Sheet Cancelled';
                                    break;
                                case 4:
                                    return 'Scheduled';
                                    break;
                                case 5:
                                    return 'CLOSED';
                                    break;
                            }
                        })
						->make(true);
    }

    /**
     * Save Items Materials
     */
    public function SaveItemMaterials(Request $req)
    {
        $data = [
            'msg' => 'Saving materials has failed.',
            'status' => 'failed',
            'ref_id' => 0
        ];

        $prods = '';

        $params = [];

        $deleted = DB::table('temp_item_materials')
                    ->where('token', $req->_token)
                    ->whereIn('inv_id', $req->inv_id)
                    ->where('create_user', Auth::user()->id)
                    ->delete();

        if (isset($req->count)) {
            $comma = '';
            foreach ($req->count as $key => $count) {
                array_push($params, [
                    'prod_sum_id' => $req->prod_sum_id[$key],
                    'prod_code' => $req->prod_code[$key],
                    'description' => $req->description[$key],
                    'back_order_qty' => $req->back_order_qty[$key],
                    'sc_no' => $req->sc_no[$key],
                    'sched_qty' => $req->sched_qty[$key],
                    'material_code' => $req->material_code[$key],
                    'heat_no' => $req->heat_no[$key],
                    'rmw_qty' => $req->rmw_qty[$key],
                    'material_used' => $req->material_used[$key],
                    'lot_no' => $req->lot_no[$key],
                    'blade_consumption' => $req->blade_consumption[$key],
                    'cut_weight' => $req->cut_weight[$key],
                    'cut_length' => $req->cut_length[$key],
                    'cut_width' => $req->cut_width[$key],
                    'mat_length' => $req->mat_length[$key],
                    'mat_weight' => $req->mat_weight[$key],
                    'mat_width' => $req->mat_width[$key],
                    'assign_qty' => $req->assign_qty[$key],
                    'remaining_qty' => $req->remaining_qty[$key],
                    'standard_material_used' => $req->standard_material_used[$key],
                    'upd_inv_id' => $req->upd_inv_id[$key],
                    'inv_id' => $req->inv_id[$key],
                    'rmwd_id' => $req->rmwd_id[$key],
                    'size' => $req->size[$key],
                    'material_type' => $req->material_type[$key],
                    'qty_per_piece' => $req->qty_per_piece[$key],
                    'ship_date' => $req->ship_date[$key],
                    'rmw_no' => $req->rmw_no,
                    'create_user' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'token' => $req->_token
                ]);

                if ($prods !== '') {
                    $comma = ', ';
                }

                $prods .= $comma.$req->prod_code[$key];
            }

            $insert = array_chunk($params, 1000);

            $saved = false;
            foreach ($insert as $batch) {
                $saved = DB::table('temp_item_materials')->insert($batch);
            }

            if ($saved) {
                $data = [
                    'msg' => 'Materials were successfully saved.',
                    'status' => 'success',
                    'token' => $req->_token
                ];

                $this->_audit->insert([
                    'user_type' => Auth::user()->user_type,
                    'module_id' => $this->_moduleID,
                    'module' => 'Production Schedule',
                    'action' => 'Prepared BOM for '.$prods,
                    'user' => Auth::user()->id,
                    'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
                ]);
            }
        }

        return $data;
    }
    
    /**
     * Get Items Materials
     */
    public function getItemMaterials(Request $req)
    {
        $data = DB::table('temp_item_materials')
                ->where([
                    ['inv_id', '=', $req->inv_id],
                    ['upd_inv_id', '=', $req->upd_inv_id],
                    ['rmwd_id', '=', $req->rmwd_id],
                    ['token', '=', $req->_token],
                    ['create_user', '=', Auth::user()->id]
                ])
                ->get();

        return response()->json($data);
    }

    /**
     * Save JO Details
     */
    public function SaveJODetails(Request $req)
    {
        /*
            JOB ORDER = 1 JO
            1 same Product code
            2 or more SC no.
            1 RM Heat no.
            1 Lot No.
        **/

        $data = [
            'msg' => 'Saving J.O. Details has failed.',
            'status' => 'failed'
        ];

        $arr_jo = [];
        $jo_no = '';

        if (isset($req->_token)) {
            // get potential J.O. designation
            $bom_count = DB::table('temp_item_materials')
                            ->select(
                                'token','prod_code','heat_no','lot_no', DB::raw("sum(sched_qty) as sched_qty"),'rmw_no'
                            )
                            ->where('token',$req->_token)
                            ->where('create_user', Auth::user()->id)
                            ->groupBy('token','prod_code','heat_no','lot_no','rmw_no')
                            ->count();

            if ($bom_count < 1) {
                $data = [
                    'msg' => "You haven't picked Items yet.",
                    'status' => 'failed'
                ];
                return response()->json($data);
            } else {

                $boms = DB::table('temp_item_materials')
                            ->select(
                                'token','prod_code','heat_no','lot_no', DB::raw("sum(sched_qty) as sched_qty"),'rmw_no'
                            )
                            ->where('token',$req->_token)
                            ->where('create_user', Auth::user()->id)
                            ->groupBy('token','prod_code','heat_no','lot_no','rmw_no')
                            ->get();

                foreach ($boms as $key => $bom) {
                    // $f = Auth::user()->firstname;
                    // $l = Auth::user()->lastname;

                    // $jocode = $this->_helper->TransactionNo($f[0] . $l[0] . '-JO');
                    $jocode = '';

                    $whs = DB::select(
                                        DB::raw(
                                            "CALL GET_warehouse_for__prod_sched(
                                                '".$bom->heat_no."',
                                                '".$bom->rmw_no."'
                                                )"
                                        )
                                    );

                    if (count((array)$whs) > 0) {
                        // $whs = DB::table('ppc_update_inventories')
                        //             ->select('warehouse')
                        //             ->where('heat_no',$bom->heat_no)
                        //             ->distinct()
                        //             ->first();

                        $jocode = $this->_helper->TransactionNo('JO', $whs[0]->warehouse);
                    } else {
                        $data = [
                            'msg' => "There is an error occured when getting warehouse.",
                            'status' => 'failed'
                        ];
                        return response()->json($data);
                    }

                    $jo_sum = new PpcJoDetailsSummary();
                    $jo_sum->jo_no = $jocode;
                    $jo_sum->status = 0;
                    $jo_sum->total_sched_qty = $bom->sched_qty;
                    $jo_sum->rmw_no = (isset($bom->rmw_no))? $bom->rmw_no : '';
                    $jo_sum->create_user = Auth::user()->id;
                    $jo_sum->update_user = Auth::user()->id;
                    $jo_sum->save();

                    array_push($arr_jo, [
                        'jo_id' => $jo_sum->id,
                        'jo_no' => $jo_sum->jo_no,
                        'token' => $bom->token,
                        'prod_code' => $bom->prod_code,
                        'heat_no' => $bom->heat_no,
                        'lot_no' => $bom->lot_no,
                        'rmw_no' => $bom->rmw_no
                    ]);

                    $com = '';
                    if ($jo_no !== '') {
                        $com = ', ';
                    }

                    $jo_no .= $com.$jo_sum->jo_no;
                }

                foreach ($arr_jo as $key => $jo) {
                    $details = DB::table('temp_item_materials')->where([
                                    ['token', '=', $jo['token']], 
                                    ['prod_code', '=', $jo['prod_code']], 
                                    ['heat_no', '=', $jo['heat_no']], 
                                    ['lot_no', '=', $jo['lot_no']], 
                                    ['rmw_no', '=', $jo['rmw_no']],
                                    ['create_user', '=', Auth::user()->id]
                                ])->get();

                    

                    foreach ($details as $key => $dt) {

                        $rmwd = DB::table('ppc_raw_material_withdrawal_details')->where('id', $dt->rmwd_id)
                                    ->select('sc_no','id')
                                    ->where('create_user', Auth::user()->id)
                                    ->first();

                        if (isset($rmwd->id)) {
                            $Coma = ', ';
                            if ($rmwd->sc_no == '') {
                                $Coma = ' ';
                            }
                            if (strpos($rmwd->sc_no, $dt->sc_no) === false) {
                                PpcRawMaterialWithdrawalDetails::where('id', $dt->rmwd_id)
                                    ->where('create_user', Auth::user()->id)
                                    ->increment('assigned_qty', $dt->assign_qty, ['sc_no' => $dt->sc_no . $Coma . $rmwd->sc_no]);
                            }
                        }

                        PpcProductionSummary::where('id', $dt->prod_sum_id)
                                            ->increment('sched_qty', $dt->sched_qty,[
                                                'jo_summary_id' => $jo['jo_id'],
                                                'status' => 4
                                            ]);

                        PpcJoDetails::create([
                            'jo_summary_id' => $jo['jo_id'],
                            'sc_no' => $dt->sc_no,
                            'product_code' => $dt->prod_code,
                            'description' => $dt->description,
                            'back_order_qty' => $dt->back_order_qty,
                            'sched_qty' => $dt->sched_qty,
                            'material_used' => $dt->material_used,
                            'material_heat_no' => $dt->heat_no,
                            'uom' => 'PCS',
                            'lot_no' => $dt->lot_no,
                            'create_user' => Auth::user()->id,
                            'update_user' => Auth::user()->id,
                            'inv_id' => $dt->inv_id,
                            'rmw_id' => $dt->rmwd_id,
                            'rmw_issued_qty' => $dt->rmw_qty,
                            'material_type' => $dt->material_type,
                            'blade_consumption' => $dt->blade_consumption,
                            'computed_per_piece' => $dt->qty_per_piece,
                            'assign_qty' => $dt->assign_qty,
                            'remaining_qty' => $dt->remaining_qty,
                            'heat_no_id' => $dt->upd_inv_id,
                            'ship_date' => $dt->ship_date,
                            'prod_sched_id' => $dt->prod_sum_id,
                            'cut_weight' => $dt->cut_weight,
                            'cut_length' => $dt->cut_length,
                            'cut_width' => $dt->cut_width,
                            'mat_length' => $dt->mat_length,
                            'mat_weight' => $dt->mat_weight,
                            'mat_width' => $dt->mat_width,
                            'upd_inv_id' => $dt->upd_inv_id,
                            'size' => $dt->size
                        ]);
                    }
                }

                DB::table('temp_item_materials')
                        ->where('token',$req->_token)
                        ->where('create_user', Auth::user()->id)
                        ->delete();

                $data = [
                    'msg' => "Job Order # " .$jo_no. " Has made.",
                    'status' => 'success'
                ];
            }
        } else {
            $data = [
                'msg' => "You haven't picked Items yet.",
                'status' => 'failed'
            ];
        }

        return response()->json($data);
    }

    /**
     * Get Travel sheet / List of J.O.
     */
    public function getTravel_sheet(Request $req)
    {
        $data = DB::select(
                            DB::raw("CALL GET_prod_sched_jo_list(".Auth::user()->id.",NULL,NULL,NULL)")
                        );

        // return response()->json($data);

        return DataTables::of($data)
						->addColumn('action', function($data) {
                            return "<button type='button' class='btn btn-sm bg-blue btn_show_jo'
                                        data-jo_no='".$data->jo_no."' data-prod_code='".$data->product_code."'
                                        data-issued_qty='".$data->issued_qty."' title='Edit J.O. Details'
                                        data-status='".$data->status."' data-sched_qty='".$data->sched_qty."'
                                        data-sc_no='".$data->sc_no."' data-idJO='".$data->jo_summary_id."'
                                        data-id='".$data->travel_sheet_id."' data-status='".$data->status."'
                                    >
                                        <i class='fa fa-edit'></i>
                                    </button>
                                    <button type='button' class='btn btn-sm bg-red btn_cancel_jo'
                                        data-jo_no='".$data->jo_no."' data-prod_code='".$data->product_code."'
                                        data-issued_qty='".$data->issued_qty."' title='Cancel J.O. Details'
                                        data-status='".$data->status."' data-sched_qty='".$data->sched_qty."'
                                        data-sc_no='".$data->sc_no."' data-idJO='".$data->jo_summary_id."'
                                        data-id='".$data->travel_sheet_id."' data-status='".$data->status."'
                                    >
                                        <i class='fa fa-times'></i>
                                    </button>";
                        })
						->make(true);
    }

    /**
     * Cancel Travel sheet / List of J.O.
     */
    public function CancelTravelSheet(Request $req)
    {
        // 3 = Cancel
        //PpcJoTravelSheet::where('id', $req->jo_summary_id)->update(['status' => 3]);
        $data = [
            'msg'=> 'Cancelling J.O. has failed.',
            'status' => "failed"
        ];

        $pts = [];

        if (!is_null($req->travel_sheet_id)) {
            $checkStatus = [0, 1, 4];
            if (in_array((int)$req->status, $checkStatus)) {
                // DB::select(
                //     DB::raw("CALL CANCEL_job_order_p1(". $req->jo_summary_id .",". Auth::user()->id .")")
                // );

                // DB::select(
                //     DB::raw("CALL CANCEL_job_order_p2(" . $req->travel_sheet_id . "," . Auth::user()->id . ")")
                // );
                $detailsDecrement = DB::table('ppc_jo_details')
                    ->select('sc_no', 'product_code', 'sched_qty', 'material_heat_no', 'prod_sched_id', 'rmw_id')
                    ->where('jo_summary_id', $req->jo_summary_id)
                    ->get();

                foreach ($detailsDecrement as $key => $dt) {
                    $check_prod_sched = PpcProductionSummary::where('id', $dt->prod_sched_id)->select('sched_qty')->first();

                    if ($check_prod_sched->sched_qty > 0) {
                        PpcProductionSummary::where('id', $dt->prod_sched_id)->decrement('sched_qty', (float) $dt->sched_qty);
                    }

                    PpcRawMaterialWithdrawalDetails::where('id', $dt->rmw_id)
                        // ->where('create_user', Auth::user()->id)
                        ->update(['sc_no' => DB::raw("REPLACE(sc_no, '" . $dt->sc_no . ", ', '')")]);

                    PpcRawMaterialWithdrawalDetails::where('id', $dt->rmw_id)
                        // ->where('create_user', Auth::user()->id)
                        ->update(['sc_no' => DB::raw("REPLACE(sc_no, '" . $dt->sc_no . "', '')")]);
                }

                $summary_update = PpcJoDetailsSummary::where('id', $req->jo_summary_id)->update([
                    'cancelled' => 1,
                    'status' => 3,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'update_user' => Auth::user()->id
                ]);

                if ($summary_update) {
                    PpcJoDetails::where('jo_summary_id', $req->jo_summary_id)->update([
                        'cancelled' => 1,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'update_user' => Auth::user()->id
                    ]);
                }

                PpcPreTravelSheet::where('id', $req->travel_sheet_id)->update(['status' => 3]);

                ProdTravelSheet::where('pre_travel_sheet_id', $req->travel_sheet_id)->update(['status' => 3]);

                $pts = ProdTravelSheet::where('pre_travel_sheet_id', $req->travel_sheet_id)->select('travel_sheet_id');

                if (count((array) $pts) > 0) {
                    foreach ($pts as $key => $ts) {
                        ProdTravelSheetProcess::where('travel_sheet_id', $ts->travel_sheet_id)->update(['status' => 3]);
                    }
                }

                $data = [
                    'msg' => 'J.O. # ' . $req->jo_no . ' has successfully cancelled.',
                    'status' => "success"
                ];
            } else {
                switch ($req->status) {
                    case '2':
                        $data = [
                            'msg' => 'J.O. # ' . $req->jo_no . ' is already On-going process',
                            'status' => "failed"
                        ];
                        break;
                    case '3':
                        $data = [
                            'msg' => 'J.O. # ' . $req->jo_no . ' is already Cancelled.',
                            'status' => "failed"
                        ];
                        break;
                    case '5':
                        $data = [
                            'msg' => 'J.O. # ' . $req->jo_no . ' is already Closed.',
                            'status' => "failed"
                        ];
                        break;
                    case '2':
                        $data = [
                            'msg' => 'J.O. # ' . $req->jo_no . ' is already In Production',
                            'status' => "failed"
                        ];
                        break;
                }
            }
                
        } else {
            $data = [
                'msg'=> 'J.O. # '.$req->jo_no.' had already issued a Travel Sheet.',
                'status' => "failed"
            ];
        }
        

        return response()->json($data);
    }

    /**
     * Filter Travel sheet / List of J.O.
     */

    public function filterJO(Request $req)
    {
        $srch_date= "";
        $srch_jo_no = "";
        $srch_prod_code = "";
        $srch_sc_no = "";
        $srch_description = "";
        $srch_material_used = "";
        $srch_material_heat_no = "";
        $srch_status = "";

        if (!is_null($req->srch_jdate_from) && !is_null($req->srch_jdate_to)) {
            $srch_date = " AND DATE_FORMAT(updated_at, '%Y-%m-%d') BETWEEN '".$req->srch_jdate_from."' AND '".$req->srch_jdate_to."'";
        }

        if (!is_null($req->srch_jjo_no)) {
            $srch_jo_no = " AND jo_no = '".$req->srch_jjo_no."'";
        }

        if (!is_null($req->srch_jprod_code)) {
            $srch_prod_code = " AND product_code = '".$req->srch_jprod_code."'";
        }

        if (!is_null($req->srch_jdescription)) {
            $srch_description = " AND `description` = '".$req->srch_jdescription."'";
        }

        if (!is_null($req->srch_jmaterial_used)) {
            $srch_material_used = " AND material_used = '".$req->srch_jmaterial_used."'";
        }

        if (!is_null($req->srch_jmaterial_heat_no)) {
            $srch_material_heat_no = " AND material_heat_no = '".$req->srch_jmaterial_heat_no."'";
        }

        if (!is_null($req->srch_jstatus)) {
            $srch_status = " AND `status` = '".$req->srch_jstatus."'";
        }

        $data = DB::table('v_jo_list')
                    ->where('user_id', Auth::user()->id)
                    ->whereRaw("user_id = ".Auth::user()->id.$srch_date.
                        $srch_jo_no.
                        $srch_prod_code.
                        $srch_description.
                        $srch_material_used.
                        $srch_material_heat_no.
                        $srch_status
                    );

        return DataTables::of($data)
						->addColumn('action', function($data) {
                            return "<button type='button' class='btn btn-sm bg-blue btn_show_jo'
                                        data-jo_no='".$data->jo_no."' data-prod_code='".$data->product_code."'
                                        data-issued_qty='".$data->issued_qty."' title='Edit J.O. Details'
                                        data-status='".$data->status."' data-sched_qty='".$data->sched_qty."'
                                        data-qty_per_sheet='".$data->qty_per_sheet."' data-iso_code='".$data->iso_code."'
                                        data-sc_no='".$data->sc_no."' data-idJO='".$data->jo_summary_id."'
                                    >
                                        <i class='fa fa-edit'></i>
                                    </button>
                                    <button type='button' class='btn btn-sm bg-red btn_cancel_jo'
                                        data-jo_no='".$data->jo_no."' data-prod_code='".$data->product_code."'
                                        data-issued_qty='".$data->issued_qty."' title='Cancel J.O. Details'
                                        data-status='".$data->status."' data-sched_qty='".$data->sched_qty."'
                                        data-qty_per_sheet='".$data->qty_per_sheet."' data-iso_code='".$data->iso_code."'
                                        data-sc_no='".$data->sc_no."' data-idJO='".$data->jo_summary_id."'
                                    >
                                        <i class='fa fa-times'></i>
                                    </button>";
                        })
						->make(true);
    }

    /**
     * Delete of J.O. Detail
     */
    public function deleteJoDetailItem(Request $req)
    {
        $deleted = false;
        $data = [
            'msg' => 'Deleting J.O. item has failed.',
            'status' => 'failed'
        ];

        $deleted_items = PpcJoDetails::where('id', $req->id)->first();

        if (isset($req->id)) {
            $deleted = PpcJoDetails::where('id', $req->id)->delete();
        }

        if ($deleted) {
            $data = [
                'msg' => 'J.O. item has successfully deleted.',
                'status' => 'success'
            ];

            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module_id' => $this->_moduleID,
                'module' => 'Production Schedule',
                'action' => 'deleted SC No. '.$deleted_items->sc_no,
                'user' => Auth::user()->id,
                'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
            ]);
        }

        return response()->json($data);
    }

    /**
     * Edit of J.O. Detail
     */
    public function editJoDetailItem(Request $req)
    {
        $updated = false;
        $upd_count = 0;

        $data = [
            'msg' => 'Updating J.O. Detail item has failed.',
            'status' => 'failed',
            'jo_summary_id' => 0
        ];

        if (isset($req->j_jd_id)) {
            foreach ($req->j_jd_id as $key => $id) {
                $updated = DB::table('ppc_jo_details')->where('id',$id)
                            ->update([
                                'sc_no' => $req->j_sc_no[$key],
                                'product_code' => $req->j_prod_code[$key],
                                'description' => $req->j_description[$key],
                                'back_order_qty' => $req->j_order_qty[$key],
                                'sched_qty' => $req->j_sched_qty[$key],
                                'material_used' => $req->j_material_used[$key],
                                'material_heat_no' => $req->j_material_heat_no[$key],
                                'lot_no' => $req->j_lot_no[$key],
                                'inv_id' => $req->j_inv_id[$key],
                                'rmw_issued_qty' => $req->j_rmw_issued_qty[$key],
                                'material_type' => $req->j_material_type[$key],
                                'computed_per_piece' => $req->j_computed_per_piece[$key],
                                'rmw_id' => $req->j_rmwd_id[$key],
                                'heat_no_id' => $req->j_heat_no_id[$key],
                                'ship_date' => $req->j_ship_date,
                                'assign_qty' => $req->j_assign_qty[$key],
                                'remaining_qty' => $req->j_remaining_qty[$key],
                                'prod_sched_id' => $req->j_prod_sched_id[$key],
                                'blade_consumption' => $req->j_blade_consumption[$key],
                                'cut_weight' => $req->j_cut_weight[$key],
                                'cut_length' => $req->j_cut_length[$key],
                                'cut_width' => $req->j_cut_width[$key],
                                'mat_length' => $req->j_mat_length[$key],
                                'mat_weight' => $req->j_mat_weight[$key],
                                'upd_inv_id' => $req->j_upd_inv_id[$key],
                                'size' => $req->j_size[$key],
                                'update_user' => Auth::user()->id,
                                'updated_at' => date('Y-m-d H:i:s')
                                
                            ]);
                if ($updated) {
                    $upd_count++;

                    $this->_audit->insert([
                        'user_type' => Auth::user()->user_type,
                        'module_id' => $this->_moduleID,
                        'module' => 'Production Schedule',
                        'action' => 'edited SC No. '.$req->j_sc_no[$key],
                        'user' => Auth::user()->id,
                        'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
                    ]);
                }
            }

            if ($upd_count > 0) {
                

                $data = [
                    'msg' => 'J.O. Detail item has successfully updated.',
                    'status' => 'success',
                    'jo_summary_id' => $req->j_jo_summary_id[0]
                ];
            }
        }

        return response()->json($data);
    }

    /**
     * Excel Data
     */
    public function excelFilteredData(Request $req)
    {
        $data = $this->getFilteredOrders($req);
        $date = date('Ymd');

        Excel::create('UploadedOrders_'.$date, function($excel) use($data)
        {
            $excel->sheet('Summary', function($sheet) use($data)
            {
                $sheet->setHeight(4, 20);
                $sheet->mergeCells('A2:F2');
                $sheet->cells('A2:F2', function($cells) {
                    $cells->setAlignment('center');
                    $cells->setFont([
                        'family'     => 'Calibri',
                        'size'       => '14',
                        'bold'       =>  true,
                        'underline'  =>  true
                    ]);
                });
                $sheet->cell('A2',"Uploaded Orders Summary");

                $sheet->setHeight(6, 15);
                $sheet->cells('A4:F4', function($cells) {
                    $cells->setFont([
                        'family'     => 'Calibri',
                        'size'       => '11',
                        'bold'       =>  true,
                    ]);
                    // Set all borders (top, right, bottom, left)
                    $cells->setBorder('solid', 'solid', 'solid', 'solid');
                });

                $sheet->cell('A4', function($cell) {
                    $cell->setValue("SC No.");
                    $cell->setBorder('thick','thick','thick','thick');
                });
            
                $sheet->cell('B4', function($cell) {
                    $cell->setValue("Product Code");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('C4', function($cell) {
                    $cell->setValue("Description");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('D4', function($cell) {
                    $cell->setValue("Quantity");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('E4', function($cell) {
                    $cell->setValue("P.O.");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('F4', function($cell) {
                    $cell->setValue("Date Uploaded");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cells('A4:F4', function($cells) {
                    $cells->setBorder('thick', 'thick', 'thick', 'thick');
                });

                $row = 5;

                foreach ($data as $key => $dt) {
                    $sheet->setHeight($row, 15);

                    $sheet->cell('A'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->sc_no);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('B'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->prod_code);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('C'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->description);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('D'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->quantity);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('E'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->po);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('F'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->date_upload);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    
                    $row++;
                }
                
                $sheet->cells('A4:F'.$row, function($cells) {
                    $cells->setBorder('thick', 'thick', 'thick', 'thick');
                });
            });
        })->download('xlsx');
    }

    /**
     * Getting of J.O. Details data
     */
    public function getJODetails(Request $req)
    {
        $data = PpcJoDetails::where('jo_summary_id', $req->jo_summary_id);
        return DataTables::of($data)->make(true);
    }

    




    public function SaveJODetails1(Request $req)
    {
        $jo_no_count = [];
        $qty = [];
        $jocode = '';
        $back_order_qty_total = 0;
        $total_sched_qty = 0;
        $result = 'nothing happens';
        $jo_no = '';
        $prod_code_unique = $req->prod_code[0];
        $lot_no_unique = '';
        $heat_no_unique = '';
        $one_JO = [];
        $multiple_JO = [];

        $ref_id = 0;

        if (empty($req->jo_no)) {
            $ref_id = rand(); // randomize ref_id for this save session;

            foreach ($req->id as $key => $id) {

                $materials = DB::table('temp_item_materials')->where('sc_id',$id)->get();

                foreach ($materials as $km => $mat) {
                    DB::table('temp_save_boms')->insert([
                        'upd_inv_id' => $mat->upd_inv_id,
                        'inv_id' => $mat->inv_id,
                        'rmwd_id' => $mat->rmwd_id,
                        'size' => $mat->size,
                        'computed_per_piece' => $mat->computed_per_piece,
                        'material_type' => $mat->material_type,
                        'sched_qty' => $mat->sched_qty,
                        'material_heat_no' => $mat->material_heat_no,
                        'rmw_issued_qty' => $mat->rmw_issued_qty,
                        'material_used' => $mat->material_used,
                        'lot_no' => $mat->lot_no,
                        'blade_consumption' => $mat->blade_consumption,
                        'cut_weight' => $mat->cut_weight,
                        'cut_length' => $mat->cut_length,
                        'cut_width' => $mat->cut_width,
                        'mat_length' => $mat->mat_length,
                        'mat_weight' => $mat->mat_weight,
                        'assign_qty' => $mat->assign_qty,
                        'remaining_qty' => $mat->remaining_qty,
                        'rmw_no' => $mat->rmw_no,
                        'ship_date' => $mat->ship_date,
                        'sc_no' => $req->sc_no[$key],
                        'prod_code' => $req->prod_code[$key],
                        'description' => $req->description[$key],
                        'quantity' => $req->quantity[$key],
                        'create_user' => Auth::user()->id,
                        'sc_id' => $id,
                        'ref_id' => $ref_id,
                    ]);
                }
            }

            $jo_no = $this->SegregateBOMs($ref_id);
            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module_id' => $this->_moduleID,
                'module' => 'Production Schedule',
                'action' => 'Inserted J.O. Number:' . $jo_no,
                'user' => Auth::user()->id,
                'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
            ]);

        }

        // delete materials
        foreach ($req->id as $key => $id) {
            TempItemMaterial::where('sc_id',$id)->delete();
        }

        return response()->json(['jocode' => $jo_no, 'result' => $result]);
    }

    public function SegregateBOMs($ref_id)
    {
        /*
            JOB ORDER = 1 JO
            1 same Product code
            2 or more SC no.
            1 RM Heat no.
            1 Lot No.
        **/

        $arr_jo = [];
        $jo_no = '';

        // get potential J.O. designation
        $boms = DB::table('temp_save_boms')
                ->select(
                    'ref_id','prod_code','material_heat_no','lot_no', DB::raw("sum(sched_qty) as sched_qty"),'rmw_no'
                )
                ->where('ref_id',$ref_id)
                ->groupBy('ref_id','prod_code','material_heat_no','lot_no','rmw_no')
                ->get();

        foreach ($boms as $key => $bom) {
            // $f = Auth::user()->firstname;
            // $l = Auth::user()->lastname;
            $jocode = '';

            $check_whs = DB::table('ppc_update_inventories')->select('warehouse')->where('heat_no',$bom->material_heat_no)->distinct()->count();

            if ($check_whs > 0) {
                $whs = DB::table('ppc_update_inventories')
                            ->select('warehouse')
                            ->where('heat_no',$bom->material_heat_no)
                            ->distinct()
                            ->first();

                $jocode = $this->_helper->TransactionNo('JO', $whs->warehouse);
            }

            //$jocode = $this->_helper->TransactionNo($f[0] . $l[0] . '-JO');

            $jo_sum = new PpcJoDetailsSummary();
            $jo_sum->jo_no = $jocode;
            $jo_sum->status = 0;
            $jo_sum->total_sched_qty = $bom->sched_qty;
            $jo_sum->rmw_no = (isset($bom->rmw_no))? $bom->rmw_no : '';
            $jo_sum->create_user = Auth::user()->id;
            $jo_sum->update_user = Auth::user()->id;
            $jo_sum->save();

            array_push($arr_jo, [
                'jo_id' => $jo_sum->id,
                'jo_no' => $jo_sum->jo_no,
                'ref_id' => $bom->ref_id,
                'prod_code' => $bom->prod_code,
                'material_heat_no' => $bom->material_heat_no,
                'lot_no' => $bom->lot_no,
                'rmw_no' => $bom->rmw_no
            ]);

            $com = '';
            if ($jo_no !== '') {
                $com = ', ';
            }

            $jo_no .= $com.$jo_sum->jo_no;
        }

        foreach ($arr_jo as $key => $jo) {
            $details = DB::table('temp_save_boms')->where([
                            ['ref_id', '=', $jo['ref_id']], 
                            ['prod_code', '=', $jo['prod_code']], 
                            ['material_heat_no', '=', $jo['material_heat_no']], 
                            ['lot_no', '=', $jo['lot_no']], 
                            ['rmw_no', '=', $jo['rmw_no']]
                        ])->get();

            

            foreach ($details as $key => $dt) {

                $rmwd = DB::table('ppc_raw_material_withdrawal_details')->where('id', $dt->rmwd_id)
                            ->select('sc_no','id')
                            ->where('create_user', Auth::user()->id)
                            ->first();

                if (isset($rmwd->id)) {
                    $Coma = ', ';
                    if ($rmwd->sc_no == '') {
                        $Coma = ' ';
                    }
                    if (strpos($rmwd->sc_no, $dt->sc_no) === false) {
                        PpcRawMaterialWithdrawalDetails::where('id', $dt->rmwd_id)
                            ->where('create_user', Auth::user()->id)
                            ->increment('assigned_qty', $dt->assign_qty, ['sc_no' => $dt->sc_no . $Coma . $rmwd->sc_no]);
                    }
                }

                PpcProductionSummary::where('id', $dt->sc_id)
                                    ->increment('sched_qty', $dt->sched_qty,[
                                        'jo_summary_id' => $jo['jo_id'],
                                        'status' => 4
                                    ]);

                PpcJoDetails::create([
                    'jo_summary_id' => $jo['jo_id'],
                    'sc_no' => $dt->sc_no,
                    'product_code' => $dt->prod_code,
                    'description' => $dt->description,
                    'back_order_qty' => $dt->quantity,
                    'sched_qty' => $dt->sched_qty,
                    'material_used' => $dt->material_used,
                    'material_heat_no' => $dt->material_heat_no,
                    'uom' => 'PCS',
                    'lot_no' => $dt->lot_no,
                    'create_user' => Auth::user()->id,
                    'update_user' => Auth::user()->id,
                    'inv_id' => $dt->inv_id,
                    'rmw_id' => $dt->rmwd_id,
                    'rmw_issued_qty' => $dt->rmw_issued_qty,
                    'material_type' => $dt->material_type,
                    'blade_consumption' => $dt->blade_consumption,
                    'computed_per_piece' => $dt->computed_per_piece,
                    'assign_qty' => $dt->assign_qty,
                    'remaining_qty' => $dt->remaining_qty,
                    'heat_no_id' => $dt->upd_inv_id,
                    'ship_date' => $dt->ship_date,
                    'prod_sched_id' => $dt->sc_id,
                    'cut_weight' => $dt->cut_weight,
                    'cut_length' => $dt->cut_length,
                    'cut_width' => $dt->cut_width,
                    'mat_length' => $dt->mat_length,
                    'mat_weight' => $dt->mat_weight,
                    'upd_inv_id' => $dt->upd_inv_id,
                    'size' => $dt->size
                ]);
            }
        }

        return $jo_no;
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
                                    d.heat_no_id,
                                    d.ship_date
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
                                    d.ship_date,
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
                    ->select('id','status')->where('trans_no',$req->rmw_no)->first();

        $with_rmw = '';
        $materials = [];

        if (count((array)$rmw) > 0) {
            if ($rmw->status !== 'CONFIRMED') {
                $data = [
                    'msg' => 'Withdrawal Slip # '.$req->rmw_no.' is not yet confirmed or it was cancelled.',
                    'status' => 'failed',
                    'materials' => []
                ];
                return response()->json($data);
            } 
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
                                        pui.width as rmw_width,
                                        pmc.std_weight as std_weight,
                                        pui.id as upd_inv_id,
                                        pui.item_code as item_code,
                                        pmc.material_code as mat_code,
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
                                        pui.`schedule` as `schedule`,
                                        (SELECT cut_weight from ppc_product_codes
                                                    where product_code = '".$req->prod_code."' limit 1) as cut_weight,
                                        (SELECT cut_length from ppc_product_codes
                                                    where product_code = '".$req->prod_code."' limit 1) as cut_length,
                                        (SELECT cut_width from ppc_product_codes
                                                    where product_code = '".$req->prod_code."' limit 1) as cut_width
                                    FROM ppc_update_inventories as pui
                                    left join admin_assign_material_types as apl 
                                    on apl.material_type = pui.materials_type

                                    inner join ppc_raw_material_withdrawal_details as rmw 
                                    on pui.heat_no = rmw.material_heat_no
                                    and pui.id = rmw.inv_id

                                    inner join ppc_raw_material_withdrawal_infos as rmwi 
                                    on rmw.trans_id = rmwi.id
                                    
                                    inner join ppc_material_codes as pmc
                                    on pmc.material_code = pui.item_code

                                    WHERE rmw.issued_qty <> 0 AND rmwi.`status` <> 'UNCONFIRMED' 
                                    AND apl.user_id = ".Auth::user()->id." 
                                    AND rmwi.trans_no = '".$req->rmw_no."'
                                    group by rmwi.trans_no,
                                        rmw.trans_id,
                                        pui.id,
                                        pui.heat_no,
                                        rmw.issued_qty,
                                        rmw.scheduled_qty,
                                        rmw.id,
                                        rmw.inv_id,
                                        pui.item_code,
                                        pmc.material_code,
                                        pui.length,
                                        pui.width,
                                        pui.description,
                                        pui.size,
                                        pui.`schedule`,
                                        pui.materials_type
                                    ORDER BY pui.id desc");

            
        } else {
            $pw = DB::table('ppc_product_withdrawal_infos')
                    ->select('id','status')->where('trans_no',$req->rmw_no)->first();

            if (count((array)$pw) > 0) {
                if ($pw->status !== 'CONFIRMED') {
                    $data = [
                        'msg' => 'Withdrawal Slip # '.$req->rmw_no.' is not yet confirmed or it was cancelled.',
                        'status' => 'failed',
                        'materials' => []
                    ];
                    return response()->json($data);
                } 

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
                                            pui.width as rmw_width,
                                            pmc.std_weight as std_weight,
                                            pui.id as upd_inv_id,
                                            pui.item_code as item_code,
                                            pmc.material_code as mat_code,
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
                                            pui.`schedule` as `schedule`,
                                            (SELECT cut_weight from ppc_product_codes
                                                    where product_code = '".$req->prod_code."' limit 1) as cut_weight,
                                            (SELECT cut_length from ppc_product_codes
                                                        where product_code = '".$req->prod_code."' limit 1) as cut_length,
                                            (SELECT cut_width from ppc_product_codes
                                                        where product_code = '".$req->prod_code."' limit 1) as cut_width

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

                                        inner join ppc_material_codes as pmc
                                        on pmc.material_code = pui.item_code

                                        WHERE pw.issued_qty <> 0 
                                        AND pwi.`status` <> 'UNCONFIRMED'
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
                                            pmc.material_code,
                                            pui.length,
                                            pui.width,
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
                        $jo = DB::table('ppc_jo_details as jd')
                                    ->select(
                                        DB::raw("jd.rmw_id as rmw_id"), 
                                        DB::raw("rmwd.issued_qty as issued_qty"), 
                                        DB::raw("sum(jd.assign_qty) as assign_qty"),
                                        DB::raw("case
                                                    when rmwd.issued_qty <= sum(jd.assign_qty) then 1
                                                    else 0
                                                end as scheduled")
                                    )
                                    ->join('ppc_raw_material_withdrawal_details as rmwd','rmwd.id','=','jd.rmw_id')
                                    ->where('jd.rmw_id', $material->rmw_id)
                                    ->groupBy('jd.rmw_id','rmwd.issued_qty')
                                    ->first();

                        //if ($jo->scheduled == 0) {
                            array_push($heat_no,[
                                'heat_no' => $material->heat_no,
                                'uom' => $material->uom, 
                                'rmw_issued_qty' => $material->rmw_issued_qty,
                                'rmw_scheduled_qty' => $material->rmw_scheduled_qty,
                                'rmw_id' => $material->rmw_id,
                                'inv_id' => $material->inv_id,
                                'rmw_length' => $material->rmw_length,
                                'rmw_width' => $material->rmw_width,
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
                                'lot_no' => $material->lot_no,
                                'std_weight' => $material->std_weight,
                                'cut_weight' => $material->cut_weight,
                                'cut_length' => $material->cut_length,
                                'cut_width' => $material->cut_width,
                                'mat_code' => $material->mat_code
                            ]);
                        //}
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
                                    $inventory,
                                    $req
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

    public function SaveMaterials(Request $req)
    {
        $data = [
            'msg' => 'Saving materials has failed.',
            'status' => 'failed'
        ];

        $params = [];

        TempItemMaterial::where('sc_id',$req->sc_id)->delete();

        if (count($req->count) > 0) {
            foreach ($req->count as $key => $cnt) {
                array_push($params, [
                    'sc_id' => $req->sc_id,
                    'upd_inv_id' => $req->upd_inv_id[$key],
                    'inv_id' => $req->inv_id[$key],
                    'rmwd_id' => $req->rmwd_id[$key],
                    'size' => $req->size[$key],
                    'computed_per_piece' => $req->computed_per_piece[$key],
                    'material_type' => $req->material_type[$key],
                    'sched_qty' => $req->sched_qty[$key],
                    'material_heat_no' => $req->material_heat_no[$key],
                    'rmw_issued_qty' => $req->rmw_issued_qty[$key],
                    'material_used' => $req->material_used[$key],
                    'lot_no' => $req->lot_no[$key],
                    'blade_consumption' => $req->blade_consumption[$key],
                    'cut_weight' => $req->cut_weight[$key],
                    'cut_length' => $req->cut_length[$key],
                    'cut_width' => $req->cut_width[$key],
                    'mat_length' => $req->mat_length[$key],
                    'mat_weight' => $req->mat_weight[$key],
                    'assign_qty' => $req->assign_qty[$key],
                    'remaining_qty' => $req->remaining_qty[$key],
                    'rmw_no' => $req->rmw_no,
                    'ship_date' => $req->ship_date,
                    'sc_no' => $req->sc_no,
                    'prod_code' => $req->prod_code,
                    'description' => $req->description,
                    'quantity' => $req->quantity,
                    'create_user' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }

            $insert = array_chunk($params, 1000);

            $saved = false;
            foreach ($insert as $batch) {
                $saved = TempItemMaterial::insert($batch);
            }

            if ($saved) {
                $data = [
                    'msg' => 'Materials were successfully saved.',
                    'status' => 'success'
                ];

                $this->_audit->insert([
                    'user_type' => Auth::user()->user_type,
                    'module_id' => $this->_moduleID,
                    'module' => 'Production Schedule',
                    'action' => 'Added materials for item '.$req->prod_no,
                    'user' => Auth::user()->id,
                    'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
                ]);
            }
        } 
        
        return $data;
    }

    public function Materials(Request $req)
    {
        $data = TempItemMaterial::where('sc_id',$req->sc_id)->get();

        return $data;
    }

    
}