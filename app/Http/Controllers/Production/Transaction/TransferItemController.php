<?php

namespace App\Http\Controllers\Production\Transaction;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use Illuminate\Support\Facades\Auth;
use App\ProdTransferItem;
use App\ProdTravelSheet;
use App\ProdTravelSheetProcess;
use App\User;
use DB;
use DataTables;
use App\PpcDivision;
use App\PpcDivisionProcess;
use App\PpcProductCode;
use App\Notification;
use Event;
use App\Events\Notify;

class TransferItemController extends Controller
{
    protected $_helper;
    protected $_audit;
    protected $_moduleID;

    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
        $this->_helper = new HelpersController;
        $this->_audit = new AuditTrailController;

        $this->_moduleID = $this->_helper->moduleID('T0008');
    }

    public function index()
    {
        $user_accesses = $this->_helper->UserAccess();
        return view('production.transaction.transfer-item',['user_accesses' => $user_accesses]);
    }

    public function save(Request $req)
    {
        $div = PpcDivision::find($req->div_code);

        $data = [
            'msg' => 'Saving failed.',
            'status' => 'failed',
            'transfer_item' => ''
        ];

        $this->validate($req, [ 
                'jo_no' => 'required',
                'prod_order_no' => 'required',
                'prod_code' => 'required',
                'description' => 'required',
                'qty' => 'required|integer|not_in:0',
                'div_code' => 'required',
                'process' => 'required',
                'status' => 'required'
        ],['not_in' => 'The :attribute field is not be in 0.']);

        if (isset($req->id)) {
            $items = ProdTransferItem::find($req->id);

            if ($items->item_status == 1) {
                # code...
            } else {
                $items->jo_no = strtoupper($req->jo_no);
                $items->prod_order_no = strtoupper($req->prod_order_no);
                $items->prod_code = strtoupper($req->prod_code);
                $items->description = strtoupper($req->description);
                $items->process = strtoupper($req->process);
                $items->div_code = $req->div_code;
                $items->status = strtoupper($req->status);
                $items->remarks = strtoupper($req->remarks);
                $items->qty = $req->qty;
                $items->item_status = 0;
                $items->receive_remarks = "";
                $items->receive_qty = 0;
                $items->update_user = Auth::user()->id;
                $items->updated_at = date('Y-m-d h:i:s');
                $items->update();

                $this->_audit->insert([
                    'user_type' => Auth::user()->user_type,
                    'module_id' => $this->_moduleID,
                    'module' => 'Transfer Item',
                    'action' => 'Edited Transfer Item JO: '.$req->jo_no.', Product Code: '.strtoupper($req->prod_code),
                    'user' => Auth::user()->id
                ]);

                $data = [
                    'msg' => 'Successfully saved.',
                    'status' => 'success',
                    'transfer_item' => $this->getTransferEntry()
                ];
            }
        } else {
            $items = new ProdTransferItem();
            $items->jo_no = strtoupper($req->jo_no);
            $items->prod_order_no = strtoupper($req->prod_order_no);
            $items->prod_code = strtoupper($req->prod_code);
            $items->description = strtoupper($req->description);
            $items->current_process = $req->curr_process;
            $items->process = strtoupper($req->process);
            $items->div_code = $req->div_code;
            $items->qty = $req->qty;
            $items->status = strtoupper($req->status);
            $items->remarks = strtoupper($req->remarks);
            $items->receive_remarks = "";
            $items->receive_qty = 0;
            $items->item_status = 0;

            $items->create_user = Auth::user()->id;
            $items->update_user = Auth::user()->id;
            $items->created_at = date('Y-m-d h:i:s');
            $items->updated_at = date('Y-m-d h:i:s');

            $items->save();

            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module_id' => $this->_moduleID,
                'module' => 'Transfer Item',
                'action' => 'Transfered Item JO: '.$req->jo_no.', Product Code: '.strtoupper($req->prod_code),
                'user' => Auth::user()->id
            ]);

            $to_notify = DB::table('ppc_divisions as d')
                        ->join('ppc_division_processes as p','p.division_id','=','d.id')
                        ->where('d.id',$items->div_code)
                        ->where('p.process',$items->process)
                        ->select('d.user_id')
                        ->get();

            $notis = [];

            foreach ($to_notify as $key => $notify) {
                Notification::create([
                    'title' => "Transfer Items",
                    'content' => "Items from Division Code [".$this->_helper->currentDivCodeID($req->curr_process).
                                    "] and is currently in process of ".$this->_helper->currentProcessID($req->curr_process).
                                    " for transferring to Division Code [".
                                    $this->_helper->getDivCodeByID($req->div_code).
                                    "] in process of ".$req->process.".",
                    'from' => Auth::user()->firstname." ".Auth::user()->lastname,
                    'from_id' => Auth::user()->id,
                    'to' => $notify->user_id,
                    'read' => 0,
                    'module' => 'T0007',
                    'link' => '../prod/transfer-item?receive_items=true',
                    'content_id' => $items->id,
                    'create_user' => Auth::user()->id,
                    'update_user' => Auth::user()->id
                ]);
            }

            $noti = Notification::where('read',0)->get();

            Event::fire(new Notify($noti));

            $data = [
                'msg' => 'Successfully saved.',
                'status' => 'success',
                'transfer_item' => $this->getTransferEntry()
            ];
        }

        return response()->json($data);
    }

    public function getTransferEntry()
    {
        $entry = DB::table('prod_transfer_items as t')->orderBy('t.id','desc')
                    ->join('prod_travel_sheet_processes as tsp', 'tsp.id', '=', 't.current_process')
                    ->join('ppc_divisions as d','d.div_code','=','tsp.div_code')
                    ->where('d.user_id',Auth::user()->id)
                    ->select(
                        DB::raw('t.id as id'),
                        DB::raw('t.jo_no as jo_no'),
                        DB::raw('t.prod_order_no as prod_order_no'),
                        DB::raw('t.prod_code as prod_code'),
                        DB::raw('t.description as description'),
                        DB::raw('t.current_process as current_process'),
                        DB::raw('t.div_code as div_code'),
                        DB::raw("(SELECT process FROM prod_travel_sheet_processes as p
                                    WHERE p.id = t.current_process) as current_process_name"),
                        DB::raw('(SELECT div_code FROM ppc_divisions as d
                                    where d.id = t.div_code) as div_code_code'),
                        DB::raw('t.process as process'),
                        DB::raw('t.qty as qty'),
                        DB::raw('t.status as status'),
                        DB::raw('t.remarks as remarks'),
                        DB::raw('t.item_status as item_status'),
                        DB::raw('t.create_user as create_user'),
                        DB::raw('t.created_at as created_at'),
                        DB::raw('t.update_user as update_user'),
                        DB::raw('t.updated_at as updated_at')
                    )->get();
        if (count((array)$entry) > 0) {
            return $entry;
        }
        
        return '';
    }

    // public function get_outputs()
    // {
    //     $div_code_user = Auth::user()->div_code;
    //     $data = DB::table('prod_transfer_items as i')
    //                 ->join('users as u', 'u.user_id', '=', 'i.create_user')
    //                 ->select(
    //                     'i.id as id',
    //                     'i.jo_no as jo_no',
    //                     'i.prod_order_no as prod_order_no',
    //                     'i.prod_code as prod_code',
    //                     'i.description as description',
    //                     'i.process as process',
    //                     'i.qty as qty',
    //                     'i.status as status',
    //                     'i.remarks as remarks',
    //                     'i.created_at as created_at',
    //                     'u.div_code'
    //                 )
    //                 ->where('u.div_code', Auth::user()->div_code)
    //                 ->get();

    //     return DataTables::of($data)
    //                         ->editColumn('id', function($data) {
    //                             return $data->id;
    //                         })
    //                         ->addColumn('action', function($data) {
    //                             return '
    //                             <button class="btn btn-sm bg-blue btn_edit" data-id="'.$data->id.'"><i class="fa fa-edit"></i></button>';
    //                         })->make(true); 
    // }

    public function received_items()
    {
        $receive = DB::table('prod_transfer_items as i')
                    ->join('ppc_divisions as d','d.id','=','i.div_code')
                    ->orderBy('i.id','desc')
                    ->where('i.item_status',0)
                    ->where('d.user_id',Auth::user()->id)
                    ->select(
                        'i.id as id',
                        'i.jo_no as jo_no',
                        'i.prod_order_no as prod_order_no',
                        'i.prod_code as prod_code',
                        'i.description as description',
                        'i.current_process as current_process',
                        DB::raw("(SELECT process FROM prod_travel_sheet_processes as p
                                    WHERE p.id = i.current_process) as current_process_name"),
                        DB::raw("(SELECT div_code FROM prod_travel_sheet_processes as p
                                    WHERE p.id = i.current_process) as current_div_code"),
                        'i.div_code as div_code',
                        DB::raw("(SELECT div_code FROM ppc_divisions as d
                                    WHERE d.id = i.div_code) as div_code_code"),
                        'i.process as process',
                        'i.qty as qty',
                        'i.status as status',
                        'i.remarks as remarks',
                        'i.create_user as create_user',
                        'i.created_at as created_at',
                        'i.update_user as update_user',
                        'i.updated_at as updated_at'
                    )->get();
        if (count((array)$receive) > 0) {
            return $receive;
        }
        
        return ''; 
    }

    public function DivisionCode(Request $req)
    {
        $div_code = DB::table('ppc_divisions as d')
                    ->leftjoin('ppc_division_processes as dp', 'd.id', '=', 'dp.division_id')
                    ->select('d.id as id', 'd.div_code as div_code', 'd.plant as plant')
                    ->where('dp.process', $req->process)->get();
        return response()->json($div_code);
    }

    public function getDivCodeProcess(Request $req)
    {
        $jo = ProdTravelSheet::where('jo_no',$req->jo_no)
                            ->orWhere('jo_sequence',$req->jo_no)
                            ->select('id')->first();
        $process =DB::table('prod_travel_sheet_processes as tsp')
                ->join('prod_travel_sheets as ts', 'ts.id', '=', 'tsp.travel_sheet_id')
                            ->where('tsp.travel_sheet_id',$jo->id)
                            ->select('tsp.process as process')
                            ->get();
        return response()->json($process);
    }

    public function destroy(Request $req)
    {
        if (is_array($req->id)) {
            foreach ($req->id as $key => $id) {
                $set = ProdTransferItem::find($id);
                $set->delete();
                if($set->item_status == 0){
                    Notification::where('content_id',$req->id)->delete();
                }
            }
        }else {
            $set = ProdTransferItem::find($req->id);
            $set->delete();
            if($set->item_status == 0){
                Notification::where('content_id',$req->id)->delete();
            }
        }
        $data = [
            'msg' => "Data was successfully deleted.",
            'status' => "success"
        ];
        $ids = implode(',', $req->id);
        $this->_audit->insert([
            'user_type' => Auth::user()->user_type,
            'module_id' => $this->_moduleID,
            'module' => 'Transfer Item',
            'action' => 'Deleted data ID '.$ids,
            'user' => Auth::user()->id
        ]);

        return response()->json($data);
    }

    public function getJOdetails(Request $req)
    {
        $data = [
            'jo' => '',
            'current_processes' => ''
        ];

        $jo = ProdTravelSheet::where('jo_no',$req->jo_no)
                            ->orWhere('jo_sequence',$req->jo_no)
                            ->select('id',
                                'prod_order_no',
                                'prod_code',
                                'description',
                                'status'
                            )->first();
        if(isset($jo->id)){
            if (count((array)$data) > 0) {
                $data = [
                    'jo' => $jo,
                    'current_processes' => $this->getCurrentProcesses($jo->id)
                ];

                return response()->json($data);
            }
        }

        return response()->json($data);
    }

    private function getDivCode()
    {
        $div_codes = [];
        $divs = PpcDivision::where('user_id',Auth::user()->id)
                            ->select('div_code')
                            ->get();
        if (count((array)$divs)) {
            foreach ($divs as $key => $div) {
                array_push($div_codes, $div->div_code);
            }
        }

        return $div_codes;
    }

    private function getCurrentProcesses($id)
    {
        $div_codes = $this->getDivCode();

        $current_processes = DB::table('prod_travel_sheet_processes')
                        ->whereIn('div_code',$div_codes)
                        ->where('travel_sheet_id',$id)
                        ->where('unprocessed','<>',0)
                        ->select('id','process')
                        ->groupBy('id','process')
                        ->orderBy('id','asc')
                        ->get();

        if (count((array)$current_processes) > 0) {
            return $current_processes;
        }

        return '';
    }

    public function unprocessedItem(Request $req)
    {
        $UnprocessTravel =DB::table('prod_travel_sheet_processes')
                            ->where('id',$req->current_process)
                            ->select('unprocessed', 'div_code', 'leader')
                            ->get();

        $UnprocessTransfer = DB::table('prod_transfer_items as i')
                        ->join('prod_travel_sheet_processes as tsp', 'tsp.id', '=', 'i.current_process')
                        ->where('i.current_process', $req->current_process)
                        ->where('i.item_status' , 0 )  
                        ->select(DB::raw("SUM(i.qty) as qty"))->groupBy('i.current_process')->first();
        $qty =0;            
        if(isset($UnprocessTransfer->qty)){
            $qty = $UnprocessTransfer->qty;
        }
        $data = [
            'UnprocessTravel' => $UnprocessTravel,
            'UnprocessTransfer' => $qty
        ];  
        return response()->json($data);
    }

    public function receiveProcess(Request $req)
    {
        //Getting id 
        $jo_no = ProdTravelSheet::where('jo_no',$req->jo_no)->orWhere('jo_sequence',$req->jo_no)->select('id')->first();

        //Update the qty of the process that will transfer to other div code
        $qty = $req->qty;
        $qtyprocess = $req->qty;
        if($req->status == 'SCRAP' || $req->status == 'CONVERT'){
            $qty = 0;
        }else if($req->status == 'GOOD'){
            $qtyprocess = 0;
        }

        $data = ProdTravelSheetProcess::where('travel_sheet_id' , $jo_no->id)
                ->where('div_code',$req->div_code_code)
                ->where('process',$req->process)
                ->update(['unprocessed' => DB::raw("`unprocessed` + ".$qty),
                        strtolower($req->status) => DB::raw( strtolower($req->status)."+".$qtyprocess)]);
        
        //Inserting new process if different Div Code and the process not yet existing in that div code
        if($data == 0){
            $tsp = ProdTravelSheetProcess::where('travel_sheet_id' , $jo_no->id)->where('process' , $req->process)->first();

            $data = ProdTravelSheetProcess::create([
                    'travel_sheet_id' => $jo_no->id,
                    'unprocessed' => $req->qty,
                    'process' => $req->process,
                    'previous_process' => $req->current_process_name,
                    'div_code' => $req->div_code_code,
                    'sequence' => $tsp->sequence,
                    'status' => 4,
                    strtolower($req->status) => $qtyprocess,
                    'create_user' => Auth::user()->id,
                    'update_user' => Auth::user()->id,
                ]);
        }

        //Update qty of the unprocessed in production output
        ProdTravelSheetProcess::where('id' , $req->current_process)
                                ->update(['unprocessed' => DB::raw("`unprocessed` - ".$req->qty)]);
        //Update Status 
        $item = ProdTransferItem::find($req->id);
        $item->item_status = 1;
        $item->receive_remarks = $req->remarks;
        $item->receive_qty = $req->qty;
        $item->update_user = Auth::user()->id;
        $item->update();

        //Notification
        Notification::where('content_id',$req->id)->update(['read' => 1]);
        $to_notify = DB::table('ppc_divisions')
                        ->where('div_code',$req->div_code_code)
                        ->select('user_id')
                        ->get();
        $notis = [];
        foreach ($to_notify as $key => $notify) {
            Notification::create([
                'title' => "Received Items",
                'content' => "Division Code [".$req->div_code_code."] and its process [".$req->process."] has been 
                                 received the transfer item from 
                                 Division Code [".$req->current_div_code."] and its process [".$req->current_process_name."].",
                'from' => Auth::user()->firstname." ".Auth::user()->lastname,
                'from_id' => Auth::user()->id,
                'to' => $notify->user_id,
                'read' => 0,
                'module' => 'T0007',
                'link' => '../prod/transfer-item',
                'content_id' => $item->id,
                'create_user' => Auth::user()->id,
                'update_user' => Auth::user()->id
            ]);
        }
        $noti = Notification::where('read',0)->get();
        Event::fire(new Notify($noti));

        $this->_audit->insert([
            'user_type' => Auth::user()->user_type,
            'module_id' => $this->_moduleID,
            'module' => 'Received Items',
            'action' => 'Item Received Job number '.$req->jo_no,
            'user' => Auth::user()->id
        ]);

        return response()->json($data);

    }

}
