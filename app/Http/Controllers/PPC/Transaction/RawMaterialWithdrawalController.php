<?php

namespace App\Http\Controllers\PPC\Transaction;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use App\PpcUpdateInventory;
use App\PpcUploadOrder;
use App\PpcRawMaterialWithdrawalInfo;
use App\PpcRawMaterialWithdrawalDetails;
use App\PpcMaterialCode;
use App\PpcJoDetails;
use App\Inventory;
use DataTables;
use DB;

class RawMaterialWithdrawalController extends Controller
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

        $this->_moduleID = $this->_helper->moduleID('T0003');
    }

    public function index()
    {
        $user_accesses = $this->_helper->UserAccess();
        return view('ppc.transaction.raw-material-withdrawal',['user_accesses' => $user_accesses]);
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
                        ->leftjoin('admin_assign_production_lines as apl', 'apl.product_line', '=', 'pui.materials_type')
                        ->select([ 'pui.heat_no as heat_no' ])
                        ->where('apl.user_id' ,Auth::user()->id)->get();
        return response()->json($heat_no);
    }

    public function searchTransNo(Request $req)
    {
        if (empty($req->to) && !empty($req->trans_no)) {
            $info = PpcRawMaterialWithdrawalInfo::select('id','trans_no')
                                            ->where('create_user',Auth::user()->id)
                                            ->where('trans_no',$req->trans_no)
                                            ->first();

            if (count((array)$info) > 0) {
                $details = PpcRawMaterialWithdrawalDetails::where('trans_id',$info->id)->get();

                $data = [
                    'status' => 'success',
                    'trans_id' => $info->id,
                    'trans_no' => $info->trans_no,
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
        $info = PpcRawMaterialWithdrawalInfo::select('id','trans_no')
                                            ->where("id", "=", function ($query) {
                                                $query->select(DB::raw(" MIN(id)"))
                                                  ->from('ppc_raw_material_withdrawal_infos')
                                                ->where('create_user',Auth::user()->id);
                                              })
                                            ->first();
        if (count((array)$info) > 0) {
            $details = PpcRawMaterialWithdrawalDetails::where('trans_id',$info->id)
                        ->where('create_user',Auth::user()->id)->get();

            $data = [
                'trans_id' => $info->id,
                'trans_no' => $info->trans_no,
                'details' => $details
            ];

            return response()->json($data);
        }
    }

    private function prev($trans_no)
    {
        $latest = PpcRawMaterialWithdrawalInfo::select('id','trans_no')
                                            ->where('trans_no',$trans_no)
                                            ->where('create_user',Auth::user()->id)
                                            ->first();

        $info = PpcRawMaterialWithdrawalInfo::select('id','trans_no')
                                            ->where('id','<',$latest->id)
                                            ->orderBy("id","DESC")
                                            ->where('create_user',Auth::user()->id)
                                            ->first();
        if (count((array)$info) > 0) {
            $details = PpcRawMaterialWithdrawalDetails::where('trans_id',$info->id)
                    ->where('create_user',Auth::user()->id)->get();

            $data = [
                'trans_id' => $info->id,
                'trans_no' => $info->trans_no,
                'details' => $details
            ];

            return response()->json($data);
        } else {
            return $this->first();
        }
    }

    private function next($trans_no)
    {
        $latest = PpcRawMaterialWithdrawalInfo::select('id','trans_no')
                                            ->where('trans_no',$trans_no)
                                            ->where('create_user',Auth::user()->id)
                                            ->first();

        $info = PpcRawMaterialWithdrawalInfo::select('id','trans_no')
                                            ->where('id','>',$latest->id)
                                            ->where('create_user',Auth::user()->id)
                                            ->orderBy("id")
                                            ->first();
        if (count((array)$info) > 0) {
            $details = PpcRawMaterialWithdrawalDetails::where('trans_id',$info->id)
                     ->where('create_user',Auth::user()->id)->get();

            $data = [
                'trans_id' => $info->id,
                'trans_no' => $info->trans_no,
                'details' => $details
            ];

            return response()->json($data);
        } else {
            return $this->last();
        }
    }

    private function last()
    {
        $info = PpcRawMaterialWithdrawalInfo::select('id','trans_no')
                                            ->where("id", "=", function ($query) {
                                                $query->select(DB::raw(" MAX(id)"))
                                                  ->from('ppc_raw_material_withdrawal_infos')
                                                ->where('create_user',Auth::user()->id);
                                              })
                                            ->first();

        if (count((array)$info) > 0) {                                            
            $details = PpcRawMaterialWithdrawalDetails::where('trans_id',$info->id)
                        ->where('create_user',Auth::user()->id)->get();

            $data = [
                'trans_id' => $info->id,
                'trans_no' => $info->trans_no,
                'details' => $details
            ];

            return response()->json($data);
        }
    }

    public function save(Request $req)
    {
        $params = [];
        if ($req->trans_no == '') {
            $f = Auth::user()->firstname;
            $l = Auth::user()->lastname;

            $trans_no = $this->_helper->TransactionNo($f[0].$l[0].'-RMW');
            $info = new PpcRawMaterialWithdrawalInfo();
            $info->trans_no = $trans_no;
            $info->create_user = Auth::user()->id;
            $info->update_user = Auth::user()->id;
            $info->save();

            foreach ($req->ids as $key => $detailid) {
                // PpcUpdateInventory::where('heat_no',$req->material_heat_no[$key])
                //                     ->decrement('quantity',(int)$req->issued_qty[$key]);

                Inventory::where('id',$req->inv_id[$key])
                        ->decrement('quantity',(int)$req->issued_qty[$key]);

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
                    'issued_uom' => $req->issued_uom[$key],
                    // 'needed_uom' => $req->needed_uom[$key],
                    // 'returned_uom' => $req->returned_uom[$key],
                    'create_user' => Auth::user()->id,
                    'update_user' => Auth::user()->id,
                    'created_at' => date('Y-m-d h:i:s'),
                    'updated_at' => date('Y-m-d h:i:s'),
                ]);
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
                'user' => Auth::user()->id
            ]);

        } else {
            $info = PpcRawMaterialWithdrawalInfo::select('id','trans_no')
                                               ->where('trans_no',$req->trans_no)
                                               ->first();

            $RawMatQuantity = PpcRawMaterialWithdrawalDetails::where('trans_id',$info->id)->get();

            foreach ($RawMatQuantity as $key => $rw) {
                PpcUpdateInventory::where('heat_no',$rw->material_heat_no)
                                    ->increment('quantity',(int)$rw->issued_qty);
            }

            PpcRawMaterialWithdrawalDetails::where('trans_id',$info->id)->delete();
           
            $inv_qty = 0;
            foreach ($req->ids as $key => $detailid) {
                
                // PpcUpdateInventory::where('heat_no' , $req->material_heat_no[$key])
                //                 ->decrement('quantity',(int)$req->issued_qty[$key]);

                Inventory::where('id',$req->inv_id[$key])
                        ->decrement('quantity',(int)$req->issued_qty[$key]);

                $sc_no = $req->sc_no[$key];
                if($req->sc_no[$key] == null || $req->sc_no[$key] == 'null'){
                    $sc_no = '';
                }

                array_push($params, [
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
                    'issued_uom' => $req->issued_uom[$key],
                    // 'needed_uom' => $req->needed_uom[$key],
                    // 'returned_uom' => $req->returned_uom[$key],
                    'create_user' => Auth::user()->id,
                    'update_user' => Auth::user()->id,
                    'created_at' => date('Y-m-d h:i:s'),
                    'updated_at' => date('Y-m-d h:i:s'),
                ]);
            }
            $insert = array_chunk($params, 1000);
            foreach ($insert as $batch) {
                PpcRawMaterialWithdrawalDetails::insert($batch);
            }

            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module_id' => $this->_moduleID,
                'module' => 'Raw Material Withdrawal',
                'action' => 'Edited data Transfer ID: '.$req->trans_no,
                'user' => Auth::user()->id
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

    public function destroy(Request $req)
    {
        $data = [
            'msg' => "Deleting failed",
            'status' => "warning"
        ];

        $RawMatQuantity = PpcRawMaterialWithdrawalDetails::where('trans_id',$req->id)->get();

        foreach ($RawMatQuantity as $key => $rw) {
            PpcUpdateInventory::where('heat_no',$rw->material_heat_no)
                                ->increment('quantity',(int)$rw->issued_qty);
        }
        
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
            'action' => 'Deleted data ID: '.$req->id,
            'user' => Auth::user()->id
        ]);
        return response()->json($data);
    }

    public function scnosuggest(Request $req)
    {
        //$scno = PpcUploadOrder::select('sc_no')->groupBy('sc_no')->get();
        $scno = DB::table('ppc_jo_details as jd')
                        ->join('ppc_jo_travel_sheets as ts', 'ts.id' ,'=','jd.jo_summary_id')
                        ->leftjoin('ppc_product_codes as pc', 'jd.product_code', '=', 'pc.product_code')
                        ->leftjoin('admin_assign_production_lines as pl', 'pl.product_line', '=', 'pc.product_type')
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
        $materials = DB::table('ppc_update_inventories as pui')
                        ->leftJoin('admin_assign_production_lines as apl', 'apl.product_line', '=', 'pui.materials_type')
                        ->leftJoin('inventories as i','pui.id','=','i.received_id')
                        ->select([
                            'i.id as inv_id',
                            'pui.materials_code as materials_code',
                            'pui.item as item',
                            'pui.alloy as alloy',
                            'pui.schedule as schedule',
                            'pui.size as size',
                            'i.quantity as current_stock',
                            DB::raw("pui.quantity + '".$req->issued_qty."' as quantity ")
                        ])
                        ->where('apl.user_id' ,Auth::user()->id)
                        ->where('pui.heat_no',$req->material_heat_no)
                        ->first();

        return response()->json($materials);
    }

    public function transaction_no()
    {
        $old_trans = PpcRawMaterialWithdrawalInfo::where('create_user',Auth::user()->id)
                                                ->latest()->first();
    }

    // public function getComputationIssuedQty(Request $req)
    // {
    //    $SumIssued_qty = 0;

    //         if ($req->trans_no == '') {
    //              $PpcRawMaterialWithdrawalDetails = PpcRawMaterialWithdrawalDetails::
    //              where('material_heat_no',$req->material_heat_no)->get();  
    //         }else{
    //             $info = PpcRawMaterialWithdrawalInfo::select('id','trans_no')->where('trans_no',$req->trans_no)->first();
    //             $PpcRawMaterialWithdrawalDetails = PpcRawMaterialWithdrawalDetails::where('trans_id','!=',$info->id)->  where('material_heat_no',$req->material_heat_no)->get();
    //         }

    //         foreach ($PpcRawMaterialWithdrawalDetails as $PRMWD){
    //             if(isset($PRMWD->issued_qty)){
    //                 $SumIssued_qty +=  $PRMWD->issued_qty;
    //             }
    //         }
    //         $SumIssued_qty += $req->issued_qty;

    //         if($req->inv_qty < $SumIssued_qty){
    //             $data = [
    //             'msg' => 'The total of All Issued Qty of '.$req->material_heat_no.' is Greater than Inventory quantity.',
    //             'status' => 'failed',
    //             'SumIssued_qty' => $SumIssued_qty
    //             ];
    //             return response()->json($data);
    //          }
    //         $data = [
    //             'msg' => 'success',
    //             'status' => 'success'
    //             ];
    //         return response()->json($data);
    // }
}
