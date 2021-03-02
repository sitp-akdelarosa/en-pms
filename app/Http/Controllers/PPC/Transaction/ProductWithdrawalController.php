<?php

namespace App\Http\Controllers\PPC\Transaction;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use Illuminate\Support\Str;
use App\PpcUpdateInventory;
use App\PpcProductWithdrawalInfo;
use App\PpcProductWithdrawalDetail;
use App\PpcUploadOrder;
use App\PpcProductCode;
use App\PpcJoDetails;
use App\Inventory;
use DataTables;
use Excel;
use DB;

class ProductWithdrawalController extends Controller
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

        $this->_moduleID = $this->_helper->moduleID('T0009');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_accesses = $this->_helper->UserAccess();
        $permission_access = $this->_helper->check_permission('T0009');

        return view('ppc.transaction.product-withdrawal', [
            'user_accesses' => $user_accesses,
            'permission_access' => $permission_access
        ]);
    }

    public function getWithdrawalTransaction(Request $req)
    {
        return $this->navigate($req->to,$req->trans_no);
    }

    private function getProductsWithdrawed($trans_id = null)
    {
        $products = DB::table('ppc_product_withdrawal_details as d')
                        ->select(
                            DB::raw("id as id"),
                            DB::raw("trans_id as trans_id"),
                            DB::raw("ifnull(item_class,'') as item_class"),
                            DB::raw("ifnull(item_code,'') as item_code"),
                            DB::raw("ifnull(jo_no,'') as jo_no"),
                            DB::raw("ifnull(lot_no,'') as lot_no"),
                            DB::raw("ifnull(heat_no,'') as heat_no"),
                            DB::raw("ifnull(alloy,'') as alloy"),
                            DB::raw("ifnull(item,'') as item"),
                            DB::raw("ifnull(size,'') as size"),
                            DB::raw("ifnull(schedule,'') as schedule"),
                            DB::raw("ifnull(sc_no,'') as sc_no"),
                            DB::raw("ifnull(remarks,'') as remarks"),
                            DB::raw("ifnull(issued_qty,0) as issued_qty"),
                            DB::raw("ifnull(issued_uom,'') as issued_uom"),
                            DB::raw("inv_id as inv_id"),
                            DB::raw("(SELECT cu.nickname
                                FROM users as cu
                                where cu.id = d.create_user) as create_user"),
                            DB::raw("(SELECT uu.nickname
                                FROM users as uu
                                where uu.id = d.update_user) as update_user"),
                            DB::raw("created_at as created_at"),
                            DB::raw("updated_at as updated_at"),
                            DB::raw("deleted as deleted"),
                            DB::raw("delete_user as delete_user"),
                            DB::raw("deleted_at as deleted_at")
                        )
                        ->where('d.create_user',Auth::user()->id)
                        ->where('d.deleted','<>',1);

        if (!is_null($trans_id)) {
            $products->where('trans_id',$trans_id);
        }

        return $products->get();
        
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
                return $this->last(null);
                break;

            default:
                return $this->last($trans_no);
                break;
        }
    }

    private function first()
    {
        $data = [
                'trans_id' => '',
                'trans_no' => '',
                'info' => [],
                'details' => []
            ];

        $info = DB::table('ppc_product_withdrawal_infos as i')
                    ->select(
                        'i.id as id',
                        'i.trans_no as trans_no',
                        'i.status as status',
                        DB::raw("(SELECT cu.nickname
                                FROM users as cu
                                where cu.id = i.create_user) as create_user"),
                        DB::raw("(SELECT uu.nickname
                                FROM users as uu
                                where uu.id = i.update_user) as update_user"),
                        'i.created_at as created_at',
                        'i.updated_at as updated_at'
                    )
                    ->where("i.id", "=", function ($query) {
                        $query->select(DB::raw(" MIN(id)"))
                            ->from('ppc_product_withdrawal_infos')
                        ->where('create_user',Auth::user()->id);
                        })
                    ->first();
        if (count((array)$info) > 0) {
            $details = $this->getProductsWithdrawed($info->id);

            $data = [
                'trans_id' => $info->id,
                'trans_no' => $info->trans_no,
                'info' => $info,
                'details' => $details
            ];

            
        }
        
        return response()->json($data);
    }

    private function prev($trans_no)
    {
        $info = [];

        $latest = DB::table('ppc_product_withdrawal_infos')
                    ->select('id')
                    ->where('trans_no',$trans_no)
                    ->where('create_user',Auth::user()->id)
                    ->first();

        if (count((array)$latest) > 0) {
            $info = DB::table('ppc_product_withdrawal_infos as i')
                    ->select(
                        'i.id as id',
                        'i.trans_no as trans_no',
                        'i.status as status',
                        DB::raw("(SELECT cu.nickname
                                FROM users as cu
                                where cu.id = i.create_user) as create_user"),
                        DB::raw("(SELECT uu.nickname
                                FROM users as uu
                                where uu.id = i.update_user) as update_user"),
                        'i.created_at as created_at',
                        'i.updated_at as updated_at'
                    )
                    ->where('i.id','<',$latest->id)
                    ->orderBy("i.id","DESC")
                    ->where('i.create_user',Auth::user()->id)
                    ->first();
        }

        if (count((array)$info) > 0) {
            $details = $this->getProductsWithdrawed($info->id);

            $data = [
                'trans_id' => $info->id,
                'trans_no' => $info->trans_no,
                'info' => $info,
                'details' => $details
            ];

            return response()->json($data);
        } else {
            return $this->first();
        }
    }

    private function next($trans_no)
    {
        $info = [];

        $latest = DB::table('ppc_product_withdrawal_infos')
                    ->select('id')
                    ->where('trans_no',$trans_no)
                    ->where('create_user',Auth::user()->id)
                    ->first();

        if (count((array)$latest) > 0) {
            $info = DB::table('ppc_product_withdrawal_infos as i')
                    ->select(
                        'i.id as id',
                        'i.trans_no as trans_no',
                        'i.status as status',
                        DB::raw("(SELECT cu.nickname
                                FROM users as cu
                                where cu.id = i.create_user) as create_user"),
                        DB::raw("(SELECT uu.nickname
                                FROM users as uu
                                where uu.id = i.update_user) as update_user"),
                        'i.created_at as created_at',
                        'i.updated_at as updated_at'
                    )
                    ->where('id','>',$latest->id)
                    ->where('create_user',Auth::user()->id)
                    ->orderBy("id")
                    ->first();
        }

        

        if (count((array)$info) > 0) {
            $details = $this->getProductsWithdrawed($info->id);

            $data = [
                'trans_id' => $info->id,
                'trans_no' => $info->trans_no,
                'info' => $info,
                'details' => $details
            ];

            return response()->json($data);
        } else {
            return $this->last(null);
        }
    }

    private function last($trans_no)
    {
        $info = [];

        if (!is_null($trans_no)) {
            $info = DB::table('ppc_product_withdrawal_infos as i')
                        ->select(
                            'i.id as id',
                            'i.trans_no as trans_no',
                            'i.status as status',
                            DB::raw("(SELECT cu.nickname
                                    FROM users as cu
                                    where cu.id = i.create_user) as create_user"),
                            DB::raw("(SELECT uu.nickname
                                    FROM users as uu
                                    where uu.id = i.update_user) as update_user"),
                            'i.created_at as created_at',
                            'i.updated_at as updated_at'
                        )
                        ->where("i.trans_no", "=", $trans_no)
                        ->first();
        } else {
            $info = DB::table('ppc_product_withdrawal_infos as i')
                    ->select(
                        'i.id as id',
                        'i.trans_no as trans_no',
                        'i.status as status',
                        DB::raw("(SELECT cu.nickname
                                FROM users as cu
                                where cu.id = i.create_user) as create_user"),
                        DB::raw("(SELECT uu.nickname
                                FROM users as uu
                                where uu.id = i.update_user) as update_user"),
                        'i.created_at as created_at',
                        'i.updated_at as updated_at'
                    )
                    ->where("i.id", "=", function ($query) {
                        $query->select(DB::raw(" MAX(id)"))
                            ->from('ppc_product_withdrawal_infos')
                        ->where('create_user',Auth::user()->id);
                        })
                    ->first();
        }
        

        if (count((array)$info) > 0) {                                            
            $details = $this->getProductsWithdrawed($info->id);

            $data = [
                'trans_id' => $info->id,
                'trans_no' => $info->trans_no,
                'info' => $info,
                'details' => $details
            ];
        } else {
            $data = [
                'trans_id' => '',
                'trans_no' => '',
                'info' => [],
                'details' => []
            ];
        }

        return response()->json($data);
    }

    public function getInventory(Request $req)
    {
        $item_code = "";
        $inv_id = "";
        $with_0 = " AND qty_pcs > 0 ";

        if (!is_null($req->item_code)) {
            $equal = "= ";
            $_value = $req->item_code;

            if (Str::contains($req->item_code, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->item_code);
            }
            $item_code = " AND item_code ".$equal."'".$_value."'";
        }

        if (!is_null($req->inv_id)) {
            $inv_id = " AND id = '". $req->inv_id ."' ";
        }

        if ($req->state == 'edit') {
            $with_0 = " AND qty_pcs > -1 ";
        }

        $data = DB::select("SELECT id,
                                    item_class,
                                    jo_no,
                                    item_code,
                                    product_line,
                                    `description`,
                                    item,
                                    alloy,
                                    size,
                                    ifnull(schedule,class) as schedule,
                                    qty_weight,
                                    orig_quantity as qty_pcs,
                                    qty_pcs as current_stock,
                                    heat_no,
                                    lot_no,
                                    received_id
                            FROM inventories
                            where deleted <> 1 
                            AND item_class = '". $req->item_class ."'
                            ".$with_0.$item_code.$inv_id);

        return $data;
    }

    public function save(Request $req)
    {
        $data = [
            'msg' => 'Transaction was successfully saved.',
            'status' => 'success',
            'info' => [],
            'datails' => []
        ];

        if (!is_null($req->id)) {
            # update transaction
            $inserted_data = 0;
            $updated_data = 0;

            $update_trans = DB::table('ppc_product_withdrawal_infos')
                        ->where('id',$req->id)
                        ->update([
                            'update_user' => Auth::user()->id,
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

            if ($update_trans) {
                foreach ($req->detail_count as $key => $count) {
                    if (!is_null($req->detail_item_id[$key])) {
                        $old_qty = (int)$req->detail_old_issued_qty[$key];
                        $issued_qty = (int)$req->detail_issued_qty[$key];

                        $delete_user = 0;
                        $deleted_at = null;

                        if ($old_qty == $issued_qty) {
                            if ($req->detail_deleted[$key] > 0) {
                                $delete_user = Auth::user()->id;
                                $deleted_at = date('Y-m-d H:i:s');
                            }

                            $updated = DB::table('ppc_product_withdrawal_details')->where('id',$req->detail_item_id[$key])
                                        ->update([
                                            'item_class' => $req->detail_item_class[$key],
                                            'item_code' => $req->detail_item_code[$key],
                                            'jo_no' => $req->detail_jo_no[$key],
                                            'lot_no' => $req->detail_lot_no[$key],
                                            'heat_no' => $req->detail_heat_no[$key],
                                            'alloy' => $req->detail_alloy[$key],
                                            'item' => $req->detail_item[$key],
                                            'size' => $req->detail_size[$key],
                                            'schedule' => $req->detail_schedule[$key],
                                            'sc_no' => $req->detail_sc_no[$key],
                                            'remarks' => $req->detail_remarks[$key],
                                            'issued_uom' => 'PCS',
                                            'inv_id' => $req->detail_inv_id[$key],
                                            'update_user' => Auth::user()->id,
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'deleted' => $req->detail_deleted[$key],
                                            'delete_user' => $delete_user,
                                            'deleted_at' => $deleted_at
                                        ]);
                            
                            if ($updated) {
                                # do not deduct issued qty to inventory qty
                                # because same issued qy from old issued qty
                                $updated_data ++;
                            }
                        } else {
                            if ($req->detail_deleted[$key] > 0) {
                                $delete_user = Auth::user()->id;
                                $deleted_at = date('Y-m-d H:i:s');
                            }

                            $updated = DB::table('ppc_product_withdrawal_details')->where('id',$req->detail_item_id[$key])
                                        ->update([
                                            'item_class' => $req->detail_item_class[$key],
                                            'item_code' => $req->detail_item_code[$key],
                                            'jo_no' => $req->detail_jo_no[$key],
                                            'lot_no' => $req->detail_lot_no[$key],
                                            'heat_no' => $req->detail_heat_no[$key],
                                            'alloy' => $req->detail_alloy[$key],
                                            'item' => $req->detail_item[$key],
                                            'size' => $req->detail_size[$key],
                                            'schedule' => $req->detail_schedule[$key],
                                            'sc_no' => $req->detail_sc_no[$key],
                                            'remarks' => $req->detail_remarks[$key],
                                            'issued_qty' => $req->detail_issued_qty[$key],
                                            'issued_uom' => 'PCS',
                                            'inv_id' => $req->detail_inv_id[$key],
                                            'update_user' => Auth::user()->id,
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'deleted' => $req->detail_deleted[$key],
                                            'delete_user' => $delete_user,
                                            'deleted_at' => $deleted_at
                                        ]);

                            if ($updated) {
                                # add old issued qty first before deduct
                                // commented due to confirm button
                                // DB::table('inventories')
                                //             ->where('id',$req->detail_inv_id[$key])
                                //             ->increment('qty_pcs',$req->detail_old_issued_qty[$key]);

                                # deduct issued qty to inventory qty
                                // commented due to confirm button
                                // $inv = DB::table('inventories')
                                //             ->where('id',$req->detail_inv_id[$key])
                                //             ->decrement('qty_pcs',$req->detail_issued_qty[$key]);

                                $updated_data ++;
                            }
                        }
                        

                        
                    } else {
                        $inserted = DB::table('ppc_product_withdrawal_details')
                                        ->insert([
                                            'trans_id' => $req->id,
                                            'item_class' => $req->detail_item_class[$key],
                                            'item_code' => $req->detail_item_code[$key],
                                            'jo_no' => $req->detail_jo_no[$key],
                                            'lot_no' => $req->detail_lot_no[$key],
                                            'heat_no' => $req->detail_heat_no[$key],
                                            'alloy' => $req->detail_alloy[$key],
                                            'item' => $req->detail_item[$key],
                                            'size' => $req->detail_size[$key],
                                            'schedule' => $req->detail_schedule[$key],
                                            'sc_no' => $req->detail_sc_no[$key],
                                            'remarks' => $req->detail_remarks[$key],
                                            'issued_qty' => $req->detail_issued_qty[$key],
                                            'issued_uom' => 'PCS',
                                            'inv_id' => $req->detail_inv_id[$key],
                                            'create_user' => Auth::user()->id,
                                            'update_user' => Auth::user()->id,
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'updated_at' => date('Y-m-d H:i:s'),
                                        ]);

                        if ($inserted) {
                            # deduct issued qty to inventory qty
                            // commented due to confirm button
                            // $inv = DB::table('inventories')
                            //             ->where('id',$req->detail_inv_id[$key])
                            //             ->decrement('qty_pcs',$req->detail_issued_qty[$key]);

                            $inserted_data ++;
                        }
                    }
                    
                }

                if ($updated_data > 0 || $inserted_data > 0) {

                    $info = DB::table('ppc_product_withdrawal_infos as i')
                                ->select(
                                    'i.id as id',
                                    'i.trans_no as trans_no',
                                    'i.status as status',
                                    DB::raw("(SELECT cu.nickname
                                            FROM users as cu
                                            where cu.id = i.create_user) as create_user"),
                                    DB::raw("(SELECT uu.nickname
                                            FROM users as uu
                                            where uu.id = i.update_user) as update_user"),
                                    'i.created_at as created_at',
                                    'i.updated_at as updated_at'
                                )
                                ->where('trans_no',$req->trans_no)
                                ->first();
                    $details = $this->getProductsWithdrawed($info->id);

                    $data = [
                        'msg' => 'Transaction was successfully saved.',
                        'status' => 'success',
                        'info' => $info,
                        'details' => $details
                    ];
                }

                $pw = DB::table('ppc_product_withdrawal_infos')->select('trans_no')
                        ->where('id',$req->id)->first();

                $this->_audit->insert([
                    'user_type' => Auth::user()->user_type,
                    'module_id' => $this->_moduleID,
                    'module' => 'Product Withdrawal',
                    'action' => 'Updated data Product Withdrawal #: ' . $pw->trans_no,
                    'user' => Auth::user()->id,
                    'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
                ]);

                return $data;
            } else {
                $info = DB::table('ppc_product_withdrawal_infos as i')
                            ->select(
                                'i.id as id',
                                'i.trans_no as trans_no',
                                'i.status as status',
                                DB::raw("(SELECT cu.nickname
                                        FROM users as cu
                                        where cu.id = i.create_user) as create_user"),
                                DB::raw("(SELECT uu.nickname
                                        FROM users as uu
                                        where uu.id = i.update_user) as update_user"),
                                'i.created_at as created_at',
                                'i.updated_at as updated_at'
                            )
                            ->where('trans_no',$req->trans_no)
                            ->first();
                $details = $this->getProductsWithdrawed($info->id);

                $data = [
                    'msg' => 'Saving Transaction failed.',
                    'status' => 'failed',
                    'info' => $info,
                    'details' => $details
                ];
            }

            
        } else {
            $inserted_data = 0;
            $trans_no = '';
            # add transaction
            // $f = Auth::user()->firstname;
            // $l = Auth::user()->lastname;

            $check_whs = DB::table('ppc_update_inventories')->select('warehouse')->whereIn('id',$req->detail_inv_id)->distinct()->count();

            if ($check_whs > 0) {
                $whs = DB::table('ppc_update_inventories')
                            ->select('warehouse')
                            ->whereIn('id',$req->detail_inv_id)
                            ->distinct()
                            ->first();

                $trans_no = $this->_helper->TransactionNo('PW', $whs->warehouse);
            } else {
                $return_data = [
                    'msg' => "No warehouse was specified. Please check Material's warehouse in inventory module.",
                    'status' => 'failed',
                    'info' => [],
                    'datails' => []
                ];

                return response()->json($return_data); 
            }

            //$trans_no = $this->_helper->TransactionNo($f[0].$l[0].'-PW');

            $trans = DB::table('ppc_product_withdrawal_infos')->insertGetId([
                        'trans_no' => $trans_no,
                        'create_user' => Auth::user()->id,
                        'update_user' => Auth::user()->id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

            foreach ($req->detail_count as $key => $count) {
                if ((int)$req->detail_deleted[$key] == 0) {
                    $inserted = DB::table('ppc_product_withdrawal_details')->insert([
                            'trans_id' => $trans,
                            'item_class' => $req->detail_item_class[$key],
                            'item_code' => $req->detail_item_code[$key],
                            'jo_no' => $req->detail_jo_no[$key],
                            'lot_no' => $req->detail_lot_no[$key],
                            'heat_no' => $req->detail_heat_no[$key],
                            'alloy' => $req->detail_alloy[$key],
                            'item' => $req->detail_item[$key],
                            'size' => $req->detail_size[$key],
                            'schedule' => $req->detail_schedule[$key],
                            'sc_no' => $req->detail_sc_no[$key],
                            'remarks' => $req->detail_remarks[$key],
                            'issued_qty' => $req->detail_issued_qty[$key],
                            'issued_uom' => 'PCS',
                            'inv_id' => $req->detail_inv_id[$key],
                            'create_user' => Auth::user()->id,
                            'update_user' => Auth::user()->id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

                    if ($inserted) {
                        # deduct issued qty to inventory qty
                        // commented due to confirm button
                        // $inv = DB::table('inventories')
                        //             ->where('id',$req->detail_inv_id[$key])
                        //             ->decrement('qty_pcs',$req->detail_issued_qty[$key]);

                        $inserted_data ++;
                    }
                } else {

                }
                
            }

            $info = DB::table('ppc_product_withdrawal_infos as i')
                        ->select(
                            'i.id as id',
                            'i.trans_no as trans_no',
                            'i.status as status',
                            DB::raw("(SELECT cu.nickname
                                    FROM users as cu
                                    where cu.id = i.create_user) as create_user"),
                            DB::raw("(SELECT uu.nickname
                                    FROM users as uu
                                    where uu.id = i.update_user) as update_user"),
                            'i.created_at as created_at',
                            'i.updated_at as updated_at'
                        )
                        ->where('trans_no',$trans_no)
                        ->first();
            $details = $this->getProductsWithdrawed($info->id);


            if ($inserted_data > 0) {
                $this->_audit->insert([
                    'user_type' => Auth::user()->user_type,
                    'module_id' => $this->_moduleID,
                    'module' => 'Product Withdrawal',
                    'action' => 'Inserted data Product Withdrawal #: ' . $trans_no,
                    'user' => Auth::user()->id,
                    'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
                ]);

                $data = [
                    'msg' => 'Transaction was successfully saved.',
                    'status' => 'success',
                    'info' => $info,
                    'details' => $details
                ];
            } else {
                $data = [
                    'msg' => 'Saving Transaction failed.',
                    'status' => 'failed',
                    'info' => $info,
                    'details' => $details
                ];
            }

            return $data;
        }
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

        $transaction = PpcProductWithdrawalInfo::where('id',$req->id)->select('trans_no')->first();

        
        
        $raw = PpcProductWithdrawalInfo::where('id',$req->id)->update([
                                                'status' => 'CANCELLED',
                                                'updated_at' => date('Y-m-d H:i:s'),
                                                'update_user' => Auth::user()->id 
                                            ]);
        if ($raw) {
            $rmwd = PpcProductWithdrawalDetail::where('trans_id',$req->id)->select('inv_id','issued_qty')->get();

            foreach ($rmwd as $key => $rm) {
                DB::table('inventories')->where('id',$rm->inv_id)
                    ->increment('qty_pcs', $rm->issued_qty);
            }

            PpcProductWithdrawalDetail::where('trans_id',$req->id)->update([
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
            'module' => 'Product Withdrawal',
            'action' => 'Product Withdrawal Transaction '.$transaction.' was cancelled.',
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
        
        $transaction = PpcProductWithdrawalInfo::where('id',$req->id)->select('trans_no')->first();
        
        $raw = PpcProductWithdrawalInfo::where('id',$req->id)->delete();
        if ($raw) {
            PpcProductWithdrawalDetail::where('trans_id',$req->id)->delete();
            $data = [
                'msg' => "Data was successfully deleted.",
                'status' => "success"
            ];
        }
        $this->_audit->insert([
            'user_type' => Auth::user()->user_type,
            'module_id' => $this->_moduleID,
            'module' => 'Product Withdrawal',
            'action' => 'Product Withdrawal Transaction '.$transaction.' was deleted.',
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
        $details = DB::table('ppc_product_withdrawal_details')
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
                'msg' => 'There are no Items selected in this transaction.',
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

        $update = DB::table('ppc_product_withdrawal_infos')
                    ->where('id',$req->id)
                    ->update([
                        'status' => 'CONFIRMED',
                        'update_user' => Auth::user()->id,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

        if ($update) {
            $details = DB::table('ppc_product_withdrawal_details')
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

        $update = DB::table('ppc_product_withdrawal_infos')
                    ->where('id',$req->id)
                    ->update([
                        'status' => 'UNCONFIRMED',
                        'update_user' => Auth::user()->id,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

        if ($update) {
            $details = DB::table('ppc_product_withdrawal_details')
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

    public function searchFilter(Request $req) 
    {
        return $this->getFilteredData($req);
    }

    private function getFilteredData($req)
    {
        $srch_date_withdrawal = "";
        $srch_trans_no = "";
        $srch_item_class = "";
        $srch_item_code = "";
        $srch_jo_no = "";
        $srch_lot_no = "";
        $srch_heat_no = "";
        $srch_sc_no = "";
        $srch_alloy = "";
        $srch_item = "";
        $srch_size = "";
        $srch_schedule = "";

        if (!is_null($req->srch_date_withdrawal_from) && !is_null($req->srch_date_withdrawal_to)) {
            $srch_date_withdrawal = " AND DATE_FORMAT(d.created_at,'%Y-%m-%d') BETWEEN '".$req->srch_date_withdrawal_from."' AND '".$req->srch_date_withdrawal_to."'";
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

        if (!is_null($req->srch_item_class)) {
            $equal = "= ";
            $_value = $req->srch_item_class;

            if (Str::contains($req->srch_item_class, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_item_class);
            }
            $srch_item_class = " AND d.item_class ".$equal."'".$_value."'";
        }

        if (!is_null($req->srch_item_code)) {
            $equal = "= ";
            $_value = $req->srch_item_code;

            if (Str::contains($req->srch_item_code, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_item_code);
            }
            $srch_item_code = " AND d.item_code ".$equal."'".$_value."'";
        }

        if (!is_null($req->srch_jo_no)) {
            $equal = "= ";
            $_value = $req->srch_jo_no;

            if (Str::contains($req->srch_jo_no, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_jo_no);
            }
            $srch_jo_no = " AND d.jo_no ".$equal."'".$_value."'";
        }

        if (!is_null($req->srch_lot_no)) {
            $equal = "= ";
            $_value = $req->srch_lot_no;

            if (Str::contains($req->srch_lot_no, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_lot_no);
            }
            $srch_lot_no = " AND d.lot_no ".$equal."'".$_value."'";
        }

        if (!is_null($req->srch_heat_no)) {
            $equal = "= ";
            $_value = $req->srch_heat_no;

            if (Str::contains($req->srch_heat_no, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_heat_no);
            }
            $srch_heat_no = " AND d.heat_no ".$equal."'".$_value."'";
        }

        if (!is_null($req->srch_sc_no)) {
            $equal = "= ";
            $_value = $req->srch_sc_no;

            if (Str::contains($req->srch_sc_no, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_sc_no);
            }
            $srch_sc_no = " AND d.sc_no ".$equal."'".$_value."'";
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

        if (!is_null($req->srch_schedule)) {
            $equal = "= ";
            $_value = $req->srch_schedule;

            if (Str::contains($req->srch_schedule, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_schedule);
            }
            $srch_schedule = " AND d.schedule ".$equal."'".$_value."'";
        }

        $products = DB::table('ppc_product_withdrawal_details as d')
                        ->select(
                            DB::raw("d.id as id"),
                            DB::raw("i.trans_no as trans_no"),
                            DB::raw("ifnull(d.item_class,'') as item_class"),
                            DB::raw("ifnull(d.item_code,'') as item_code"),
                            DB::raw("ifnull(d.jo_no,'') as jo_no"),
                            DB::raw("ifnull(d.lot_no,'') as lot_no"),
                            DB::raw("ifnull(d.heat_no,'') as heat_no"),
                            DB::raw("ifnull(d.alloy,'') as alloy"),
                            DB::raw("ifnull(d.item,'') as item"),
                            DB::raw("ifnull(d.size,'') as size"),
                            DB::raw("ifnull(d.schedule,'') as schedule"),
                            DB::raw("ifnull(d.sc_no,'') as sc_no"),
                            DB::raw("ifnull(d.remarks,'') as remarks"),
                            DB::raw("ifnull(d.issued_qty,0) as issued_qty"),
                            DB::raw("d.inv_id as inv_id"),
                            DB::raw("(SELECT cu.nickname
                                FROM users as cu
                                where cu.id = d.create_user) as create_user"),
                            DB::raw("(SELECT uu.nickname
                                FROM users as uu
                                where uu.id = d.update_user) as update_user"),
                            DB::raw("d.created_at as created_at"),
                            DB::raw("d.updated_at as updated_at"),
                            DB::raw("d.deleted as deleted"),
                            DB::raw("d.delete_user as delete_user"),
                            DB::raw("d.deleted_at as deleted_at")
                        )
                        ->join('ppc_product_withdrawal_infos as i','i.id','=','d.trans_id')
                        ->where('d.create_user',Auth::user()->id)
                        ->whereRaw(
                            "d.deleted <> 1 "
                            .$srch_date_withdrawal
                            .$srch_trans_no
                            .$srch_item_class
                            .$srch_item_code
                            .$srch_jo_no
                            .$srch_lot_no
                            .$srch_heat_no
                            .$srch_sc_no
                            .$srch_alloy
                            .$srch_item
                            .$srch_size
                            .$srch_schedule
                        )
                        ->get();
        return $products;
    }

    public function excelFilteredData(Request $req) 
    {
        $data = $this->getFilteredData($req);
        $date = date('Ymd');

        Excel::create('ProductWithdrawals'.$date, function($excel) use($data)
        {
            $excel->sheet('Summary', function($sheet) use($data)
            {
                $sheet->setHeight(4, 20);
                $sheet->mergeCells('A2:N2');
                $sheet->cells('A2:N2', function($cells) {
                    $cells->setAlignment('center');
                    $cells->setFont([
                        'family'     => 'Calibri',
                        'size'       => '14',
                        'bold'       =>  true,
                        'underline'  =>  true
                    ]);
                });
                $sheet->cell('A2',"Product Withdrawal Summary");

                $sheet->setHeight(6, 15);
                $sheet->cells('A4:N4', function($cells) {
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
                    $cell->setValue("Item Class");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('C4', function($cell) {
                    $cell->setValue("Item Code");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('D4', function($cell) {
                    $cell->setValue("J.O. No.");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('E4', function($cell) {
                    $cell->setValue("Lot No.");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('F4', function($cell) {
                    $cell->setValue("Heat No.");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('G4', function($cell) {
                    $cell->setValue("SC No.");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('H4', function($cell) {
                    $cell->setValue("Alloy");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('I4', function($cell) {
                    $cell->setValue("Item");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('J4', function($cell) {
                    $cell->setValue("Size");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('K4', function($cell) {
                    $cell->setValue("Schedule / Class");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('L4', function($cell) {
                    $cell->setValue("Qty. Withdrawed");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('M4', function($cell) {
                    $cell->setValue("Remarks");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('N4', function($cell) {
                    $cell->setValue("Date Withdrawed");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $row = 5;

                foreach ($data as $key => $dt) {
                    $sheet->setHeight($row, 15);

                    $sheet->cell('A'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->trans_no);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('B'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->item_class);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('C'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->item_code);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('D'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->jo_no);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('E'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->lot_no);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('F'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->heat_no);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('G'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->sc_no);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('H'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->alloy);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('I'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->item);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('J'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->size);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('K'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->schedule);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('L'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->issued_qty);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('M'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->remarks);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('N'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->created_at);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    
                    
                    $row++;
                }
            });
        })->download('xlsx');
    }

    public function checkRMWithdrawalCancellation(Request $req)
    {
        $data = [
            'exist' => 0
        ];

        $prevent = DB::table("ppc_jo_details_summaries")
                        ->where('rmw_no',$req->pw_no)
                        ->count();

        if ($prevent > 0) {
            $data = [
                'exist' => 1,
            ];
        }

        return $data;
    }


}
