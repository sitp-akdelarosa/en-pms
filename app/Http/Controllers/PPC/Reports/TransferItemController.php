<?php

namespace App\Http\Controllers\PPC\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelpersController;
use DB;

class TransferItemController extends Controller
{
    protected $_helper = '';

    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
        $this->_helper = new HelpersController;
    }

    public function index()
    {
        $user_accesses = $this->_helper->UserAccess();
        return view('ppc.reports.transfer-item',['user_accesses' => $user_accesses]);
    }

    public function getTransferEntry()
    {
        $entry = DB::table('prod_transfer_items as t')->orderBy('id','desc')
                    ->select(
                        DB::raw('id as id'),
                        DB::raw('jo_no as jo_no'),
                        DB::raw('prod_order_no as prod_order_no'),
                        DB::raw('prod_code as prod_code'),
                        DB::raw('description as description'),
                        DB::raw('current_process as current_process'),
                        DB::raw('div_code as div_code'),
                        DB::raw("(SELECT process FROM prod_travel_sheet_processes as p
                                    WHERE p.id = t.current_process) as current_process_name"),
                        DB::raw("(SELECT div_code FROM prod_travel_sheet_processes as p
                                    WHERE p.id = t.current_process) as current_div_code"),
                        't.div_code as div_code',
                        DB::raw("(SELECT div_code FROM ppc_divisions as d
                                    WHERE d.id = t.div_code) as div_code_code"),
                        DB::raw('process as process'),
                        DB::raw('qty as qty'),
                        DB::raw('status as status'),
                        DB::raw('receive_qty as receive_qty'),
                        DB::raw('receive_remarks as receive_remarks'),
                        DB::raw('remarks as remarks'),
                        DB::raw('item_status as item_status'),
                        DB::raw('create_user as create_user'),
                        DB::raw('created_at as created_at'),
                        DB::raw('update_user as update_user'),
                        DB::raw('updated_at as updated_at')
                    )->get();
        if (count((array)$entry) > 0) {
            return $entry;
        }
        
        return '';
    }
}
