<?php

namespace App\Http\Controllers\PPC\Transaction;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use Illuminate\Support\Str;
use App\PpcUpdateInventory;
use App\PpcUploadOrder;
use App\PpcProductWithdrawal;
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
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
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
        return view('ppc.transaction.product-withdrawal',['user_accesses' => $user_accesses]);
    }

    public function getInventory(Request $req)
    {
        $item_code = "";
        $inv_id = "";

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
                                    qty_pcs as currrent_stock,
                                    heat_no,
                                    lot_no,
                                    received_id
                            FROM inventories
                            where deleted <> 1 
                            AND item_class = '". $req->item_class ."'
                            AND qty_pcs > 0 ".$item_code.$inv_id);

        return $data;
    }

    public function save(Request $req)
    {
        if ($req->id == '') {
            # code...
        }
    }

}
