<?php

namespace App\Http\Controllers\PPC;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use Illuminate\Support\Facades\Auth;
use App\ProdTransferItem;
use App\Notification;
use Event;
use App\Events\Notify;
use DB;

class ApprovalController extends Controller
{
	protected $_helper;
    protected $_audit;
    protected $_moduleID;

    public function __construct()
    {
        // $this->middleware('auth');
        $this->_helper = new HelpersController;
        $this->_audit = new AuditTrailController;

        // $query = AdminModule::select('id')->where('code','A0005')->first();
        // $this->_moduleID = $query->id;
    }

    public function index()
    {
        $user_accesses = $this->_helper->UserAccess();

        return view('ppc.for-approval', [
            'user_accesses' => $user_accesses,
            'permission_access' => 0
        ]);
    }

    public function getTransferItems(Request $req)
    {
        $items = DB::table('prod_transfer_items as t')
                    ->join('notifications as n','n.content_id','=','t.id')
                    ->where('t.item_status',0)
                    ->where('n.to',Auth::user()->id)
                    ->select(
                        DB::raw('t.id as id'),
                        DB::raw('t.jo_no as jo_no'),
                        DB::raw('t.prod_order_no as prod_order_no'),
                        DB::raw('t.prod_code as prod_code'),
                        DB::raw('t.description as description'),
                        DB::raw('(SELECT process from prod_travel_sheet_processes as p
                                    WHERE p.id = t.current_process) as current_process'),
                        DB::raw('(SELECT div_code from prod_travel_sheet_processes as p
                                    WHERE p.id = t.current_process) as current_div_code'),
                        DB::raw('t.process as process'),
                        DB::raw('(SELECT div_code FROM ppc_divisions as d
                                    WHERE d.id = t.div_code) as div_code'),
                        DB::raw('t.qty as qty'),
                        DB::raw('t.status as status'),
                        DB::raw('t.remarks as remarks'),
                        DB::raw('n.from as from_user')
                    )
                    ->get();

        return response()->json($items);
    }

    public function answerToRequest(Request $req)
    {
        $item = ProdTransferItem::find($req->id);
        $item->item_status = $req->status;
        $item->update();

        $noti = Notification::where('content_id',$req->id)->update(['read' => 1]);

        $to_notify = DB::table('ppc_divisions as d')
                        ->join('ppc_division_processes as p','p.division_id','=','d.id')
                        ->where('d.id',$item->div_code)
                        ->where('p.process',$item->process)
                        ->select('d.user_id')
                        ->get();

        foreach ($to_notify as $key => $notis) {
            $msg = "Items from Division Code [".$this->_helper->currentDivCodeID($item->current_process).
                    "] and is currently in process of ".$this->_helper->currentProcessID($item->current_process).
                    " were approved to transfer to your Division Code [".
                    $this->_helper->getDivCodeByID($item->div_code).
                    "] in process of ".$item->process.".";

            Notification::create([
                'title' => "Accept Transferred Items",
                'content' => $msg,
                'from' => Auth::user()->firstname." ".Auth::user()->lastname,
                'from_id' => Auth::user()->id,
                'to' => $notis->user_id,
                'read' => 0,
                'module' => 'T0007',
                'link' => '../prod/transfer-item?receive_items=true',
                'content_id' => $item->id,
                'create_user' => Auth::user()->id,
                'update_user' => Auth::user()->id
            ]);
        }
        
        $noti = Notification::where('read',0)->get();

        Event::fire(new Notify($noti));

        // $this->_audit->insert([
        //     'user_type' => Auth::user()->user_type,
        //     'module_id' => $this->_moduleID,
        //     'module' => 'Request Approval',
        //     'action' => ($req->status == 1)? 'Approved' : '' . 'transfered items from Division : '.
        //     			$this->_helper->currentDivCodeID($item->current_process).
        //     			' in process of '.$this->_helper->currentProcessID($item->current_process).
        //     			'to Division Code: '.$this->_helper->getDivCodeByID($item->div_code).
        //     			'in process of '.$item->process,
        //     'user' => Auth::user()->id
        // ]);

        return response()->json($req->status);
    }
}
