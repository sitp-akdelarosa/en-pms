<?php

namespace App\Http\Controllers\PPC\Transaction;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use Illuminate\Support\Str;
use App\AdminSettingIso;
use App\PpcUpdateInventory;
use App\PpcUploadOrder;
use App\PpcRawMaterialWithdrawalInfo;
use App\PpcRawMaterialWithdrawalDetails;
use App\PpcMaterialCode;
use App\PpcJoDetails;
use App\Inventory;
use DataTables;
use Excel;
use DB;

class RawMaterialWithdrawalController extends Controller
{
    protected $_helper = '';
    protected $_audit;
    protected $_moduleID;

    public function __construct()
    {
        // $this->middleware('ajax-session-expired');
        // $this->middleware('auth');
        $this->_helper = new HelpersController;
        $this->_audit = new AuditTrailController;

        $this->_moduleID = $this->_helper->moduleID('T0003');
    }

    public function index()
    {
        $iso = AdminSettingIso::select('iso_name','iso_code')->where('iso_name','like','%WITHDRAW SLIP')->get();
        $user_accesses = $this->_helper->UserAccess();
        $permission_access = $this->_helper->check_permission('T0003');

        return view('ppc.transaction.raw-material-withdrawal',[
                    'user_accesses' => $user_accesses,
                    'permission_access' => $permission_access,
                    'iso' => $iso
                ]);
    }

    public function getScNo()
    {
        $sc_no = PpcUploadOrder::select('sc_no')
                                ->where('create_user',Auth::user()->id)
                                ->groupBy('sc_no')
                                ->get();
        return response()->json($sc_no);
    }

    public function getHeatNo()
    {
        $heat_no = DB::table('ppc_update_inventories as pui')
                        ->leftjoin('admin_assign_warehouses as aw', 'aw.warehouse', '=', 'pui.warehouse')
                        ->select([ 'pui.heat_no as heat_no' ])
                        ->where('aw.user_id' ,Auth::user()->id)->get();
        return response()->json($heat_no);
    }

    public function searchTransNo(Request $req)
    {
        if (empty($req->to) && !empty($req->trans_no)) {
            $info = PpcRawMaterialWithdrawalInfo::select('id','trans_no','status')
                                            ->where('create_user',Auth::user()->id)
                                            ->where('trans_no',$req->trans_no)
                                            ->first();

            if (count((array)$info) > 0) {
                $details = DB::table('ppc_raw_material_withdrawal_details as d')
                            ->where('d.trans_id',$info->id)
                            ->where('d.deleted','<>','1')
                            ->select('d.id as id',
                                    'd.id as detail_id',
                                    'd.trans_id as trans_id',
                                    'd.mat_code as mat_code',
                                    'd.alloy as alloy',
                                    'd.item as item',
                                    'd.size as size',
                                    'd.schedule as schedule',
                                    'd.lot_no as lot_no',
                                    'd.material_heat_no as material_heat_no',
                                    'd.sc_no as sc_no',
                                    'd.remarks as remarks',
                                    'd.issued_qty as issued_qty',
                                    'd.needed_qty as needed_qty',
                                    'd.returned_qty as returned_qty',
                                    'd.issued_uom as issued_uom',
                                    'd.needed_uom as needed_uom',
                                    'd.returned_uom as returned_uom',
                                    'd.inv_id as inv_id',
                                    DB::raw("(SELECT cu.nickname
                                            FROM users as cu
                                            where cu.id = d.create_user) as create_user"),
                                    DB::raw("(SELECT uu.nickname
                                            FROM users as uu
                                            where uu.id = d.update_user) as update_user"),
                                    'd.created_at as created_at',
                                    'd.updated_at as updated_at',
                                    'd.deleted as deleted',
                                    DB::raw("(SELECT du.nickname
                                            FROM users as du
                                            where du.id = d.delete_user) as delete_user"),
                                    'd.deleted_at as deleted_at'
                                )
                            ->get();

                $data = [
                    'status' => 'success',
                    'trans_id' => $info->id,
                    'trans_no' => $info->trans_no,
                    'istatus' => $info->status,
                    'details' => $details
                ];
                return response()->json($data);
            }else{
                $data = [
                    'status' => 'failed',
                ];
                 return response()->json($data);
            }
        }

        if (!empty($req->to) && !empty($req->trans_no)) {
            return $this->navigate($req->to,$req->trans_no);
        }

        if (empty($req->to) && empty($req->trans_no)) {
            return $this->last();
        }
    }

    private function navigate($to,$trans_no)
    {
        switch ($to) {
            case 'first':
                return $this->first();
                break;

            case 'prev':
                return $this->prev($trans_no);
                break;

            case 'next':
                return $this->next($trans_no);
                break;

            case 'last':
                return $this->last();
                break;

            default:
                return $this->last();
                break;
        }
    }

    private function first()
    {
        $info = PpcRawMaterialWithdrawalInfo::select('id','trans_no','status','created_at')
                                            ->where("id", "=", function ($query) {
                                                $query->select(DB::raw(" MIN(id)"))
                                                  ->from('ppc_raw_material_withdrawal_infos')
                                                ->where('create_user',Auth::user()->id);
                                              })
                                            ->first();
        if (count((array)$info) > 0) {
            $details = DB::table('ppc_raw_material_withdrawal_details as d')
                            ->where('trans_id',$info->id)
                            ->where('d.deleted','<>','1')
                            ->select('d.id as id',
                                'd.id as detail_id',
                                'd.trans_id as trans_id',
                                'd.mat_code as mat_code',
                                'd.alloy as alloy',
                                'd.item as item',
                                'd.size as size',
                                'd.schedule as schedule',
                                'd.lot_no as lot_no',
                                'd.material_heat_no as material_heat_no',
                                'd.sc_no as sc_no',
                                'd.remarks as remarks',
                                'd.issued_qty as issued_qty',
                                'd.needed_qty as needed_qty',
                                'd.returned_qty as returned_qty',
                                'd.issued_uom as issued_uom',
                                'd.needed_uom as needed_uom',
                                'd.returned_uom as returned_uom',
                                'd.inv_id as inv_id',
                                DB::raw("(SELECT cu.nickname
                                        FROM users as cu
                                        where cu.id = d.create_user) as create_user"),
                                DB::raw("(SELECT uu.nickname
                                        FROM users as uu
                                        where uu.id = d.update_user) as update_user"),
                                'd.created_at as created_at',
                                'd.updated_at as updated_at',
                                'd.deleted as deleted',
                                DB::raw("(SELECT du.nickname
                                        FROM users as du
                                        where du.id = d.delete_user) as delete_user"),
                                'd.deleted_at as deleted_at'
                            )
                            ->where('create_user',Auth::user()->id)->get();

            $data = [
                'trans_id' => $info->id,
                'trans_no' => $info->trans_no,
                'istatus' => $info->status,
                'details' => $details,
                'created_at' => $info->created_at
            ];

            return response()->json($data);
        }
    }

    private function prev($trans_no)
    {
        $latest = PpcRawMaterialWithdrawalInfo::select('id','trans_no','status')
                                            ->where('trans_no',$trans_no)
                                            ->where('create_user',Auth::user()->id)
                                            ->first();

        $info = PpcRawMaterialWithdrawalInfo::select('id','trans_no','status','created_at')
                                            ->where('id','<',$latest->id)
                                            ->orderBy("id","DESC")
                                            ->where('create_user',Auth::user()->id)
                                            ->first();
        if (count((array)$info) > 0) {
            $details = DB::table('ppc_raw_material_withdrawal_details as d')
                            ->where('trans_id',$info->id)
                            ->where('d.deleted','<>','1')
                            ->select('d.id as id',
                                'd.id as detail_id',
                                'd.trans_id as trans_id',
                                'd.mat_code as mat_code',
                                'd.alloy as alloy',
                                'd.item as item',
                                'd.size as size',
                                'd.schedule as schedule',
                                'd.lot_no as lot_no',
                                'd.material_heat_no as material_heat_no',
                                'd.sc_no as sc_no',
                                'd.remarks as remarks',
                                'd.issued_qty as issued_qty',
                                'd.needed_qty as needed_qty',
                                'd.returned_qty as returned_qty',
                                'd.issued_uom as issued_uom',
                                'd.needed_uom as needed_uom',
                                'd.returned_uom as returned_uom',
                                'd.inv_id as inv_id',
                                DB::raw("(SELECT cu.nickname
                                        FROM users as cu
                                        where cu.id = d.create_user) as create_user"),
                                DB::raw("(SELECT uu.nickname
                                        FROM users as uu
                                        where uu.id = d.update_user) as update_user"),
                                'd.created_at as created_at',
                                'd.updated_at as updated_at',
                                'd.deleted as deleted',
                                DB::raw("(SELECT du.nickname
                                        FROM users as du
                                        where du.id = d.delete_user) as delete_user"),
                                'd.deleted_at as deleted_at'
                            )
                            ->where('create_user',Auth::user()->id)->get();

            $data = [
                'trans_id' => $info->id,
                'trans_no' => $info->trans_no,
                'istatus' => $info->status,
                'details' => $details,
                'created_at' => $info->created_at
            ];

            return response()->json($data);
        } else {
            return $this->first();
        }
    }

    private function next($trans_no)
    {
        $latest = PpcRawMaterialWithdrawalInfo::select('id','trans_no','status')
                                            ->where('trans_no',$trans_no)
                                            ->where('create_user',Auth::user()->id)
                                            ->first();

        $info = PpcRawMaterialWithdrawalInfo::select('id','trans_no','status','created_at')
                                            ->where('id','>',$latest->id)
                                            ->where('create_user',Auth::user()->id)
                                            ->orderBy("id")
                                            ->first();
        if (count((array)$info) > 0) {
            $details = DB::table('ppc_raw_material_withdrawal_details as d')
                            ->where('trans_id',$info->id)
                            ->where('d.deleted','<>','1')
                            ->select('d.id as id',
                                'd.id as detail_id',
                                'd.trans_id as trans_id',
                                'd.mat_code as mat_code',
                                'd.alloy as alloy',
                                'd.item as item',
                                'd.size as size',
                                'd.schedule as schedule',
                                'd.lot_no as lot_no',
                                'd.material_heat_no as material_heat_no',
                                'd.sc_no as sc_no',
                                'd.remarks as remarks',
                                'd.issued_qty as issued_qty',
                                'd.needed_qty as needed_qty',
                                'd.returned_qty as returned_qty',
                                'd.issued_uom as issued_uom',
                                'd.needed_uom as needed_uom',
                                'd.returned_uom as returned_uom',
                                'd.inv_id as inv_id',
                                DB::raw("(SELECT cu.nickname
                                        FROM users as cu
                                        where cu.id = d.create_user) as create_user"),
                                DB::raw("(SELECT uu.nickname
                                        FROM users as uu
                                        where uu.id = d.update_user) as update_user"),
                                'd.created_at as created_at',
                                'd.updated_at as updated_at',
                                'd.deleted as deleted',
                                DB::raw("(SELECT du.nickname
                                        FROM users as du
                                        where du.id = d.delete_user) as delete_user"),
                                'd.deleted_at as deleted_at'
                            )
                            ->where('create_user',Auth::user()->id)->get();

            $data = [
                'trans_id' => $info->id,
                'trans_no' => $info->trans_no,
                'istatus' => $info->status,
                'details' => $details,
                'created_at' => $info->created_at
            ];

            return response()->json($data);
        } else {
            return $this->last();
        }
    }

    private function last()
    {
        $info = PpcRawMaterialWithdrawalInfo::select('id','trans_no','status','created_at')
                                            ->where("id", "=", function ($query) {
                                                $query->select(DB::raw(" MAX(id)"))
                                                  ->from('ppc_raw_material_withdrawal_infos')
                                                ->where('create_user',Auth::user()->id);
                                              })
                                            ->first();

        if (count((array)$info) > 0) {                                            
            $details = DB::table('ppc_raw_material_withdrawal_details as d')
                            ->where('trans_id',$info->id)
                            ->where('d.deleted','<>','1')
                            ->select('d.id as id',
                                'd.id as detail_id',
                                'd.trans_id as trans_id',
                                'd.mat_code as mat_code',
                                'd.alloy as alloy',
                                'd.item as item',
                                'd.size as size',
                                'd.schedule as schedule',
                                'd.lot_no as lot_no',
                                'd.material_heat_no as material_heat_no',
                                'd.sc_no as sc_no',
                                'd.remarks as remarks',
                                'd.issued_qty as issued_qty',
                                'd.needed_qty as needed_qty',
                                'd.returned_qty as returned_qty',
                                'd.issued_uom as issued_uom',
                                'd.needed_uom as needed_uom',
                                'd.returned_uom as returned_uom',
                                'd.inv_id as inv_id',
                                DB::raw("(SELECT cu.nickname
                                        FROM users as cu
                                        where cu.id = d.create_user) as create_user"),
                                DB::raw("(SELECT uu.nickname
                                        FROM users as uu
                                        where uu.id = d.update_user) as update_user"),
                                'd.created_at as created_at',
                                'd.updated_at as updated_at',
                                'd.deleted as deleted',
                                DB::raw("(SELECT du.nickname
                                        FROM users as du
                                        where du.id = d.delete_user) as delete_user"),
                                'd.deleted_at as deleted_at'
                            )
                            ->where('create_user',Auth::user()->id)->get();

            $data = [
                'trans_id' => $info->id,
                'trans_no' => $info->trans_no,
                'istatus' => $info->status,
                'details' => $details,
                'created_at' => $info->created_at
            ];

            return response()->json($data);
        }
    }

    public function save(Request $req)
    {
        $params = [];
        $trans_no = '';

        if ($req->trans_no == '') {
            $check_whs = DB::table('ppc_update_inventories')->select('warehouse')->whereIn('id',$req->inv_ids)->distinct()->count();

            if ($check_whs > 0) {
                $whs = DB::table('ppc_update_inventories')
                            ->select('warehouse')
                            ->whereIn('id',$req->inv_ids)
                            ->distinct()
                            ->first();

                $trans_no = $this->_helper->TransactionNo('RMW', $whs->warehouse);
            } else {
                $return_data = [
                    'msg' => "No warehouse was specified. Please check Material's warehouse in inventory module.",
                    'status' => 'failed',
                    'data' => [],
                    'trans_no' => ''
                ];

                return response()->json($return_data); 
            }

            // $f = Auth::user()->firstname;
            // $l = Auth::user()->lastname;

            //$trans_no = $this->_helper->TransactionNo($f[0].$l[0].'-RMW');
            $info = new PpcRawMaterialWithdrawalInfo();
            $info->trans_no = $trans_no;
            $info->create_user = Auth::user()->id;
            $info->update_user = Auth::user()->id;
            $info->save();

            

            foreach ($req->detail_ids as $key => $detailid) {
                $deleted = 0;
                $delete_user = 0;
                $deleted_at = NULL;                

                if ($req->deleted[$key] > 0) {
                    $deleted = 1;
                    $delete_user = Auth::user()->id;
                    $deleted_at = date('Y-m-d H:i:s');

                } else {
                    // commented due to confirmation button
                    // Inventory::where('id',$req->inv_ids[$key])
                    //     ->decrement('qty_pcs',(int)$req->issued_qty[$key]);

                    array_push($params, [
                        'trans_id' => $info->id,
                        'mat_code' => strtoupper($req->mat_code[$key]),
                        'alloy' => strtoupper($req->alloy[$key]),
                        'item' => strtoupper($req->item[$key]),
                        'size' => strtoupper($req->size[$key]),
                        'schedule' => strtoupper($req->schedule[$key]),
                        // 'lot_no' => strtoupper($req->lot_no[$key]),
                        'material_heat_no' => strtoupper($req->material_heat_no[$key]),
                        // 'sc_no' => $req->sc_no[$key],
                        'remarks' => strtoupper($req->remarks[$key]),
                        'issued_qty' => $req->issued_qty[$key],
                        // 'needed_qty' => $req->needed_qty[$key],
                        // 'returned_qty' => $req->returned_qty[$key],
                        'issued_uom' => 'PCS',
                        'inv_id' => $req->inv_ids[$key],
                        // 'needed_uom' => $req->needed_uom[$key],
                        // 'returned_uom' => $req->returned_uom[$key],
                        'create_user' => Auth::user()->id,
                        'update_user' => Auth::user()->id,
                        'created_at' => date('Y-m-d h:i:s'),
                        'updated_at' => date('Y-m-d h:i:s'),
                        'deleted' => $deleted,
                        'delete_user' => $delete_user,
                        'deleted_at' => $deleted_at
                    ]);
                }
            }
            
            $insert = array_chunk($params, 1000);

            foreach ($insert as $batch) {
                PpcRawMaterialWithdrawalDetails::insert($batch);
            }

            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module_id' => $this->_moduleID,
                'module' => 'Raw Material Withdrawal',
                'action' => 'Inserted data Transfer ID: '.$info->trans_no,
                'user' => Auth::user()->id,
                'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
            ]);

        } else {
            $info = PpcRawMaterialWithdrawalInfo::select('id','trans_no')
                                               ->where('trans_no',$req->trans_no)
                                               ->first();

            PpcRawMaterialWithdrawalInfo::where('trans_no',$req->trans_no)
                                        ->update([
                                            'update_user' => Auth::user()->id,
                                            'updated_at' => date('Y-m-d H:i:s')
                                        ]);
           
            $inv_qty = 0;
            foreach ($req->detail_ids as $key => $detailid) {
                if ($detailid == 0) {
                    // commented due to confirmation button
                    // Inventory::where('id',$req->inv_ids[$key])
                    //     ->decrement('qty_pcs',(int)$req->issued_qty[$key]);

                    $sc_no = $req->sc_no[$key];
                    if($req->sc_no[$key] == null || $req->sc_no[$key] == 'null' ){
                        $sc_no = '';
                    }

                    PpcRawMaterialWithdrawalDetails::insert([
                        'trans_id' => $info->id,
                        'mat_code' => $req->mat_code[$key],
                        'alloy' => strtoupper($req->alloy[$key]),
                        'item' => strtoupper($req->item[$key]),
                        'size' => strtoupper($req->size[$key]),
                        'schedule' => strtoupper($req->schedule[$key]),
                        // 'lot_no' => strtoupper($req->lot_no[$key]),
                        'material_heat_no' => strtoupper($req->material_heat_no[$key]),
                        'sc_no' => $sc_no,
                        'remarks' => strtoupper($req->remarks[$key]),
                        'issued_qty' => $req->issued_qty[$key],
                        // 'needed_qty' => $req->needed_qty[$key],
                        // 'returned_qty' => $req->returned_qty[$key],
                        'issued_uom' => 'PCS',
                        'inv_id' => $req->inv_ids[$key],
                        // 'needed_uom' => $req->needed_uom[$key],
                        // 'returned_uom' => $req->returned_uom[$key],
                        'create_user' => Auth::user()->id,
                        'update_user' => Auth::user()->id,
                        'created_at' => date('Y-m-d h:i:s'),
                        'updated_at' => date('Y-m-d h:i:s'),
                    ]);

                    
                } else {
                    $deleted = 0;
                    $delete_user = 0;
                    $deleted_at = NULL;

                    if ($req->deleted[$key] > 0) {
                        $deleted = 1;
                        $delete_user = Auth::user()->id;
                        $deleted_at = date('Y-m-d H:i:s');

                        // Inventory::where('id',$req->inv_ids[$key])
                        //     ->increment('qty_pcs',(int)$req->issued_qty[$key]);
                    } else {
                        // if ((int)$req->issued_qty[$key] !== (int)$req->old_issued_qtys[$key]) {
                        //     Inventory::where('id',$req->inv_ids[$key])
                        //             ->increment('qty_pcs',(int)$req->old_issued_qtys[$key]);

                        //     Inventory::where('id',$req->inv_ids[$key])
                        //             ->decrement('qty_pcs',(int)$req->issued_qty[$key]);
                        // }
                    }

                    $sc_no = $req->sc_no[$key];
                    if($req->sc_no[$key] == null || $req->sc_no[$key] == 'null' ){
                        $sc_no = '';
                    }

                    PpcRawMaterialWithdrawalDetails::where('id',$detailid)
                        ->update([
                            'trans_id' => $info->id,
                            'mat_code' => $req->mat_code[$key],
                            'alloy' => strtoupper($req->alloy[$key]),
                            'item' => strtoupper($req->item[$key]),
                            'size' => strtoupper($req->size[$key]),
                            'schedule' => strtoupper($req->schedule[$key]),
                            // 'lot_no' => strtoupper($req->lot_no[$key]),
                            'material_heat_no' => strtoupper($req->material_heat_no[$key]),
                            'sc_no' => $sc_no,
                            'remarks' => strtoupper($req->remarks[$key]),
                            'issued_qty' => (int)$req->issued_qty[$key],
                            // 'needed_qty' => $req->needed_qty[$key],
                            // 'returned_qty' => $req->returned_qty[$key],
                            'issued_uom' => 'PCS',
                            // 'needed_uom' => $req->needed_uom[$key],
                            // 'returned_uom' => $req->returned_uom[$key],
                            'inv_id' => $req->inv_ids[$key],
                            'update_user' => Auth::user()->id,
                            'updated_at' => date('Y-m-d h:i:s'),
                            'deleted' => $deleted,
                            'delete_user' => $delete_user,
                            'deleted_at' => $deleted_at
                        ]);
                }
                
                // PpcUpdateInventory::where('heat_no' , $req->material_heat_no[$key])
                //                 ->decrement('quantity',(int)$req->issued_qty[$key]);

            } 

            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module_id' => $this->_moduleID,
                'module' => 'Raw Material Withdrawal',
                'action' => 'Edited data Transfer ID: '.$req->trans_no,
                'user' => Auth::user()->id,
                'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
            ]);
        }
        $details = PpcRawMaterialWithdrawalDetails::where('trans_id',$info->id)->get();
        $return_data = [
            'msg' => "Data was successfully saved.",
            'status' => 'success',
            'data' => $details,
            'trans_no' => $info->trans_no
        ];
        return response()->json($return_data);     
    }

    public function checkRMWithdrawalCancellation(Request $req)
    {
        $data = [
            'exist' => 0
        ];

        $prevent = DB::table("ppc_jo_details_summaries")
                        ->where('rmw_no',$req->rmw_no)
                        ->count();

        if ($prevent > 0) {
            $data = [
                'exist' => 1,
            ];
        }

        return $data;
    }

    public function destroy(Request $req)
    {
        $data = [
            'msg' => "Deleting failed",
            'status' => "warning"
        ];

        switch ($req->status) {
            case 'CONFIRMED':
                $data = $this->CancelTransaction($req);
                break;
            
            default:
                $data = $this->DeleteTransaction($req);
                break;
        }

        
        return response()->json($data);
    }

    private function CancelTransaction($req)
    {
        $data = [
            'msg' => "Cancelling Transaction has failed.",
            'status' => "failed"
        ];

        $transaction = PpcRawMaterialWithdrawalInfo::where('id',$req->id)->select('trans_no')->first();

        
        
        $raw = PpcRawMaterialWithdrawalInfo::where('id',$req->id)->update([
                                                'status' => 'CANCELLED',
                                                'updated_at' => date('Y-m-d H:i:s'),
                                                'update_user' => Auth::user()->id 
                                            ]);
        if ($raw) {
            $rmwd = PpcRawMaterialWithdrawalDetails::where('trans_id',$req->id)->select('inv_id','issued_qty')->get();

            foreach ($rmwd as $key => $rm) {
                DB::table('inventories')->where('id',$rm->inv_id)
                    ->increment('qty_pcs', $rm->issued_qty);
            }

            PpcRawMaterialWithdrawalDetails::where('trans_id',$req->id)->update([
                                                'cancelled' => 1,
                                                'updated_at' => date('Y-m-d H:i:s'),
                                                'update_user' => Auth::user()->id,
                                                'cancelled_at' => date('Y-m-d H:i:s'),
                                                'cancelled_user' => Auth::user()->id 
                                            ]);

            $data = [
                'msg' => "Transaction was successfully cancelled.",
                'status' => "success"
            ];
        }
        $this->_audit->insert([
            'user_type' => Auth::user()->user_type,
            'module_id' => $this->_moduleID,
            'module' => 'Raw Material Withdrawal',
            'action' => 'Raw Material Withdrawal Transaction '.$transaction.' was cancelled.',
            'user' => Auth::user()->id,
            'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
        ]);

        return $data;
    }

    private function DeleteTransaction($req)
    {
        $data = [
                'msg' => "Deleting failed.",
                'status' => "failed"
            ];
        
        $transaction = PpcRawMaterialWithdrawalInfo::where('id',$req->id)->select('trans_no')->first();
        
        $raw = PpcRawMaterialWithdrawalInfo::where('id',$req->id)->delete();
        if ($raw) {
            PpcRawMaterialWithdrawalDetails::where('trans_id',$req->id)->delete();
            $data = [
                'msg' => "Data was successfully deleted.",
                'status' => "success"
            ];
        }
        $this->_audit->insert([
            'user_type' => Auth::user()->user_type,
            'module_id' => $this->_moduleID,
            'module' => 'Raw Material Withdrawal',
            'action' => 'Raw Material Withdrawal Transaction '.$transaction.' was deleted.',
            'user' => Auth::user()->id,
            'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
        ]);

        return $data;
    }

    public function ConfirmWithdrawal(Request $req)
    {
        $data = [
            'msg' => 'Confirmation failed.',
            'status' => 'failed'
        ];
        $details = DB::table('ppc_raw_material_withdrawal_details')
                            ->where('trans_id',$req->id)
                            ->select('inv_id','issued_qty')
                            ->get();
        if (count((array)$details) > 0) {

            switch ($req->status) {
                case 'CONFIRMED':
                    $data = $this->UnconfirmIt($req);
                    break;
                
                default:
                    $data = $this->ConfirmIt($req);
                    break;
            }
            
        } else {
            $data = [
                'msg' => 'There are no Materials selected in this transaction.',
                'status' => 'failed'
            ];
        }
            

        return $data;
    }

    private function ConfirmIt($req)
    {
        $data = [
                'msg' => 'Confirmation failed.',
                'status' => 'failed'
            ];

        $update = DB::table('ppc_raw_material_withdrawal_infos')
                    ->where('id',$req->id)
                    ->update([
                        'status' => 'CONFIRMED',
                        'update_user' => Auth::user()->id,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

        if ($update) {
            $details = DB::table('ppc_raw_material_withdrawal_details')
                            ->where([
                                ['trans_id','=',$req->id],
                                ['deleted','=',0]
                            ])
                            ->select('inv_id','issued_qty')
                            ->get();
            
            $inv_update = 0;
            foreach ($details as $key => $dt) {
                $inv = DB::table('inventories')->where('id',$dt->inv_id)->count();
                if ($inv > 0) {
                    $upd_inv = DB::table('inventories')
                                ->where('id',$dt->inv_id)
                                ->decrement('qty_pcs',$dt->issued_qty);
                    if ($upd_inv) {
                        $inv_update++;
                    }
                }
            }

            if($inv_update > 0) {
                $data = [
                    'msg' => 'Confirmation Successfully done.',
                    'status' => 'success'
                ];
            }
            
        } else {
            $data = [
                'msg' => 'Confirmation failed.',
                'status' => 'failed'
            ];
        }

        return $data;
    }

    private function UnconfirmIt($req)
    {
        $data = [
                'msg' => 'Unconfirmation failed.',
                'status' => 'failed'
            ];

        $update = DB::table('ppc_raw_material_withdrawal_infos')
                    ->where('id',$req->id)
                    ->update([
                        'status' => 'UNCONFIRMED',
                        'update_user' => Auth::user()->id,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

        if ($update) {
            $details = DB::table('ppc_raw_material_withdrawal_details')
                            ->where([
                                ['trans_id','=',$req->id],
                                ['deleted','=',0]
                            ])
                            ->select('inv_id','issued_qty')
                            ->get();
            
            $inv_update = 0;
            foreach ($details as $key => $dt) {
                $inv = DB::table('inventories')->where('id',$dt->inv_id)->count();
                if ($inv > 0) {
                    $upd_inv = DB::table('inventories')
                                ->where('id',$dt->inv_id)
                                ->increment('qty_pcs',$dt->issued_qty);
                    if ($upd_inv) {
                        $inv_update++;
                    }
                }
            }

            if($inv_update > 0) {
                $data = [
                    'msg' => 'Transaction was Unconfirmed successfully',
                    'status' => 'success'
                ];
            }
            
        } else {
            $data = [
                'msg' => 'Unconfirmation failed.',
                'status' => 'failed'
            ];
        }

        return $data;
    }

    public function scnosuggest(Request $req)
    {
        //$scno = PpcUploadOrder::select('sc_no')->groupBy('sc_no')->get();
        $scno = DB::table('ppc_jo_details as jd')
                        ->join('ppc_jo_travel_sheets as ts', 'ts.id' ,'=','jd.jo_summary_id')
                        ->leftjoin('ppc_product_codes as pc', 'jd.product_code', '=', 'pc.product_code')
                        ->leftjoin('admin_assign_prodution_lines as pl', 'pl.product_line', '=', 'pc.product_type')
                        ->where('pl.user_id' ,Auth::user()->id)
                        ->where('jd.material_heat_no' , $req->heat_no)
                        ->whereIn('ts.status',array(0,1,2))
                        ->select('jd.sc_no as sc_no')
                        ->groupBy('jd.sc_no')
                        ->get();
        return response()->json($scno);
    }

    public function material_details(Request $req)
    {
        $with_inv_id = "";
        $qty_cond = " AND i.qty_pcs <> 0";

        if (!is_null($req->inv_id)) {
            $with_inv_id = " AND i.id = '". $req->inv_id ."' ";
        }

        if ($req->state == 'edit') {
            $qty_cond = "";
        }

        $materials = DB::table('ppc_update_inventories as pui')
                        ->leftJoin('admin_assign_warehouses as aw', 'aw.warehouse', '=', 'pui.warehouse')
                        ->leftJoin('inventories as i','pui.id','=','i.received_id')
                        ->select([
                            'i.id as inv_id',
                            'pui.receiving_no as receiving_no',
                            'pui.materials_type as materials_type',
                            'pui.item_code as item_code',
                            'pui.item as item',
                            'pui.alloy as alloy',
                            'pui.schedule as schedule',
                            'pui.size as size',
                            'pui.length as length',
                            'pui.qty_weight as qty_weight',
                            'pui.qty_pcs as qty_pcs',
                            'i.qty_pcs as current_stock',
                            'pui.heat_no as heat_no', 
                            DB::raw("pui.qty_pcs + '".$req->issued_qty."' as quantity"),
                            'pui.invoice_no as invoice_no',
                            'pui.received_date as received_date',
                            'pui.supplier as supplier'
                        ])
                        ->where('aw.user_id' ,Auth::user()->id)
                        ->where('pui.heat_no',$req->material_heat_no)
                        ->where('pui.deleted','<>','1')
                        ->where('pui.item_class','RAW MATERIAL')
                        // ->whereRaw('i.qty_pcs','<>','0')
                        ->whereRaw("1=1 ".$with_inv_id.$qty_cond)
                        ->get();

        return response()->json($materials);
    }

    public function transaction_no()
    {
        $old_trans = PpcRawMaterialWithdrawalInfo::where('create_user',Auth::user()->id)
                                                ->latest()->first();
    }

    public function searchFilter(Request $req)
    {
        return response()->json($this->getFilteredOrders($req));
    }

    private function getFilteredOrders($req)
    {
        $srch_date_withdrawal = "";
        $srch_mat_code = "";
        $srch_alloy = "";
        $srch_item = "";
        $srch_size = "";
        $srch_length = "";
        $srch_schedule = "";
        $srch_trans_no = "";
        $srch_heat_no = "";

        if (!is_null($req->srch_date_withdrawal_from) && !is_null($req->srch_date_withdrawal_to)) {
            $srch_date_withdrawal = " AND DATE_FORMAT(i.created_at,'%Y-%m-%d') BETWEEN '".$req->srch_date_withdrawal_from."' AND '".$req->srch_date_withdrawal_to."'";
        }

        if (!is_null($req->srch_mat_code)) {
            $equal = "= ";
            $_value = $req->srch_mat_code;

            if (Str::contains($req->srch_mat_code, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_mat_code);
            }
            $srch_mat_code = " AND d.mat_code ".$equal."'".$_value."'";
        }

        if (!is_null($req->srch_alloy)) {
            $equal = "= ";
            $_value = $req->srch_alloy;

            if (Str::contains($req->srch_alloy, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_alloy);
            }
            $srch_alloy = " AND d.alloy ".$equal."'".$_value."'";
        }

        if (!is_null($req->srch_item)) {
            $equal = "= ";
            $_value = $req->srch_item;

            if (Str::contains($req->srch_item, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_item);
            }
            $srch_item = " AND d.item ".$equal."'".$_value."'";
        }

        if (!is_null($req->srch_size)) {
            $equal = "= ";
            $_value = $req->srch_size;

            if (Str::contains($req->srch_size, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_size);
            }
            $srch_size = " AND d.size ".$equal."'".$_value."'";
        }

        if (!is_null($req->srch_length)) {
            $equal = "= ";
            $_value = $req->srch_length;

            if (Str::contains($req->srch_length, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_length);
            }
            $srch_length = " AND inv.length ".$equal."'".$_value."'";
        }

        if (!is_null($req->srch_schedule)) {
            $equal = "= ";
            $_value = $req->srch_schedule;

            if (Str::contains($req->srch_schedule, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_schedule);
            }
            $srch_schedule = " AND d.`schedule` ".$equal."'".$_value."'";
        }

        if (!is_null($req->srch_trans_no)) {
            $equal = "= ";
            $_value = $req->srch_trans_no;

            if (Str::contains($req->srch_trans_no, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_trans_no);
            }
            $srch_trans_no = " AND i.trans_no ".$equal."'".$_value."'";
        }

        if (!is_null($req->srch_heat_no)) {
            $equal = "= ";
            $_value = $req->srch_heat_no;

            if (Str::contains($req->srch_heat_no, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_heat_no);
            }
            $srch_heat_no = " AND d.material_heat_no ".$equal."'".$_value."'";
        }

        $Datalist = DB::select("SELECT i.trans_no as trans_no,
                                d.mat_code as mat_code,
                                d.alloy as alloy,
                                d.item as item,
                                d.size as size,
                                d.`schedule` as `schedule`,
                                d.material_heat_no as heat_no,
                                d.issued_qty as issued_qty,
                                inv.length as `length`,
                                CONCAT(u.firstname,' ',u.lastname) as create_user,
                                DATE_FORMAT(i.created_at, '%Y-%m-%d') as created_at
                        FROM enpms.ppc_raw_material_withdrawal_details as d
                        INNER JOIN enpms.ppc_raw_material_withdrawal_infos as i
                        ON i.id = d.trans_id
                        INNER JOIN users as u
                        on i.create_user = u.id
                        INNER JOIN inventories as inv
                        on d.inv_id = inv.id
                        where d.deleted <> 1 AND i.create_user = '".Auth::user()->id."'
                        ".$srch_date_withdrawal.$srch_mat_code.$srch_alloy.$srch_item.$srch_size.$srch_length.$srch_schedule.$srch_trans_no.$srch_heat_no."
                        group by i.trans_no,
                                d.mat_code,
                                d.alloy,
                                d.item,
                                d.size,
                                d.`schedule`,
                                d.material_heat_no,
                                d.issued_qty,
                                inv.length,
                                CONCAT(u.firstname,' ',u.lastname),
                                DATE_FORMAT(i.created_at, '%Y-%m-%d')");

        return $Datalist;
    }
    
    public function excelFilteredData(Request $req)
    {
        $data = $this->getFilteredOrders($req);
        $date = date('Ymd');

        Excel::create('RawMaterialWithdrawals'.$date, function($excel) use($data)
        {
            $excel->sheet('Summary', function($sheet) use($data)
            {
                $sheet->setHeight(4, 20);
                $sheet->mergeCells('A2:K2');
                $sheet->cells('A2:K2', function($cells) {
                    $cells->setAlignment('center');
                    $cells->setFont([
                        'family'     => 'Calibri',
                        'size'       => '14',
                        'bold'       =>  true,
                        'underline'  =>  true
                    ]);
                });
                $sheet->cell('A2',"Material Withdrawal Summary");

                $sheet->setHeight(6, 15);
                $sheet->cells('A4:K4', function($cells) {
                    $cells->setFont([
                        'family'     => 'Calibri',
                        'size'       => '11',
                        'bold'       =>  true,
                    ]);
                    // Set all borders (top, right, bottom, left)
                    $cells->setBorder('solid', 'solid', 'solid', 'solid');
                });

                $sheet->cell('A4', function($cell) {
                    $cell->setValue("Withdrawal No.");
                    $cell->setBorder('thick','thick','thick','thick');
                });
            
                $sheet->cell('B4', function($cell) {
                    $cell->setValue("Material Code");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('C4', function($cell) {
                    $cell->setValue("Heat No.");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('D4', function($cell) {
                    $cell->setValue("Alloy");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('E4', function($cell) {
                    $cell->setValue("Item");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('F4', function($cell) {
                    $cell->setValue("Size");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('G4', function($cell) {
                    $cell->setValue("Schedule");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('H4', function($cell) {
                    $cell->setValue("Length");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('I4', function($cell) {
                    $cell->setValue("QTY Withdrawed");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('J4', function($cell) {
                    $cell->setValue("Withdrawed By");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('K4', function($cell) {
                    $cell->setValue("Withdrawal Date");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cells('A4:K4', function($cells) {
                    $cells->setBorder('thick', 'thick', 'thick', 'thick');
                });

                

                $row = 5;

                foreach ($data as $key => $dt) {
                    $sheet->setHeight($row, 15);

                    $sheet->cell('A'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->trans_no);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('B'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->mat_code);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('C'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->heat_no);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('D'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->alloy);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('E'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->item);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('F'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->size);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('G'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->schedule);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('H'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->length);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('I'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->issued_qty);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('J'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->create_user);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('K'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->created_at);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    
                    
                    $row++;
                }
                
                $sheet->cells('A4:K'.$row, function($cells) {
                    $cells->setBorder('thick', 'thick', 'thick', 'thick');
                });
            });
        })->download('xlsx');
    }
}
