<?php

namespace App\Http\Controllers\Production\Transaction;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use Illuminate\Support\Facades\Auth;
use App\ProdProductionOutput;
use App\ProdTravelSheet;
use App\ProdTravelSheetProcess;
use App\PpcDivision;
use App\PpcPreTravelSheet;
use App\PpcOperator;
use App\RptFgSummary;
use App\PpcJoDetailsSummary;
use Event;
use App\Events\TravelSheet;
use DB;

class ProductionOutputController extends Controller
{
    protected $_helper;
    protected $_audit;
    protected $_moduleID;

    public function __construct()
    {
        // $this->middleware('auth');
        $this->_helper = new HelpersController;
        $this->_audit = new AuditTrailController;

        $this->_moduleID = $this->_helper->moduleID('T0007');
    }

    public function index()
    {
        $user_accesses = $this->_helper->UserAccess();
        $permission_access = $this->_helper->check_permission('T0007');
        return view('production.transaction.production-output',[
                    'user_accesses' => $user_accesses,
                    'permission_access' => $permission_access
                ]);
    }

    public function store(Request $req)
    {
        $error = ['errors' => ['operator' => 'Operator ID is not yet registered.']];
        $check = PpcOperator::where('operator_id',$req->operator)->count();
        if ($check == 0) { 
            return response()->json($error, 422);
        }
        $data = [
            'msg' => 'Saving failed.',
            'status' => 'failed',
            'outputs' => '',
            'travel_sheet' => ''
        ];

        $this->validate($req, [
            'unprocessed' => 'required|numeric',
            'good' => 'required|numeric',
            'rework' => 'required|numeric',
            'scrap' => 'required|numeric',
            'convert' => 'required|numeric',
            'alloy_mix' => 'required|numeric',
            'nc' => 'required|numeric',
            'operator' => 'required',
            // 'machine_no' => 'required'
        ],
        [
            'operator.required' => 'The Operator ID is required.'
        ]);
        //Update status on production 
        $pre_id = ProdTravelSheet::where('id',$req->travel_sheet_id)
                            ->select('pre_travel_sheet_id')->first();
        PpcPreTravelSheet::where('id',$pre_id->pre_travel_sheet_id)->update(['status' => 2]);
        PpcJoDetailsSummary::where('jo_no' , $req->jo_no)->update(['status' => 2 ]);
        ProdTravelSheet::where('id',$req->travel_sheet_id)->update(['status' => 2 ]);

        $date = '';

        if (isset($req->process_date) && isset($req->process_time)) {
            $date = $req->process_date.' '.$req->process_time;
        } elseif (!isset($req->process_date) && isset($req->process_time)) {
            $date = date('Y-m-d').' '.$req->process_time;
        } elseif (isset($req->process_date) && !isset($req->process_time)) {
            $date = $req->process_date.' '.date('H:i:s');
        } else {
            $date = date('Y-m-d H:i:s');
        }
        
        //$date->getTimestamp();

        $prod_output = new ProdProductionOutput();

        $prod_output->travel_sheet_id = $req->travel_sheet_id;
        $prod_output->travel_sheet_process_id = $req->travel_sheet_process_id;
        $prod_output->jo_no = $req->jo_no;

        $prod_output->unprocessed = $this->deductUnprocessed($req);
        $prod_output->good = $req->good;
        $prod_output->rework = $req->rework;
        $prod_output->scrap = $req->scrap;
        $prod_output->convert = $req->convert;
        $prod_output->alloy_mix = $req->alloy_mix;
        $prod_output->nc = $req->nc;
        $prod_output->previous_process = strtoupper($req->prev_process);
        $prod_output->current_process = strtoupper($req->current_process);
        $prod_output->machine_no = strtoupper($req->machine_no);
        $prod_output->operator = strtoupper($req->operator);
        $prod_output->process_date = $date;
        $prod_output->create_user = Auth::user()->id;
        $prod_output->update_user = Auth::user()->id;

        if ($prod_output->save()) {
            $status = 0;
            $end_date = null;
            // check if the inputed quantity covers the unprocess quantity 
            if ($this->deductUnprocessed($req) == 0) {
                // assign end date of the process
                $end_date = date('Y-m-d H:i:s');
            }

            ProdTravelSheetProcess::where('id',$req->travel_sheet_process_id)
                                    ->update([
                                        'unprocessed' => $this->deductUnprocessed($req),
                                        'good' => DB::raw("`good` + ".$prod_output->good),
                                        'rework' => DB::raw("`rework` + ".$prod_output->rework),
                                        'scrap' => DB::raw("`scrap` + ".$prod_output->scrap),
                                        'convert' => DB::raw("`convert` + ".$prod_output->convert),
                                        'alloy_mix' => DB::raw("`alloy_mix` + ".$prod_output->alloy_mix),
                                        'nc' => DB::raw("`nc` + ".$prod_output->nc),
                                        'machine_no' => $prod_output->machine_no,
                                        'operator' => $prod_output->operator,
                                        'leader' => Auth::user()->id,
                                        'end_date' => $end_date,
                                        'update_user' => Auth::user()->id
                                    ]);

            // Get process data
            $processes = DB::table('prod_travel_sheet_processes')
                            ->where('id',$req->travel_sheet_process_id)
                            ->first();

            // check if unprocess is 0
            if ($processes->unprocessed < 1) {
                // assign status to 0 as DONE PROCESS
                DB::table('prod_travel_sheet_processes')
                            ->where('id',$req->travel_sheet_process_id)
                            ->update([
                                'status' => 1, // DONE PROCESS
                                'is_current' => 1
                            ]);
            } elseif ($processes->unprocessed > 0) {
                // assign status to 2 as ON-GOING
                DB::table('prod_travel_sheet_processes')
                            ->where('id',$req->travel_sheet_process_id)
                            ->update([
                                'status' => 2, // ON-GOING
                            ]);
            }

            // accumulate process sequence to get next process
            $next_sequence = $req->sequence + 1;

            // check if has next process
            $lastsequnce = ProdTravelSheetProcess::where([
                                        ['travel_sheet_id',$req->travel_sheet_id],
                                        ['sequence',$next_sequence]
                                    ])->where('status' , 0)->count();
            
            // if travel sheet has no next process
            if($lastsequnce == 0){
                // register travel sheet to FG
                $this->saveFGSummary($req->travel_sheet_process_id,$req->travel_sheet_id,$req->jo_no,
                                $req->prod_order,$req->prod_code,$req->description,$prod_output->good);
            }

            //get output data
            $output = ProdProductionOutput::select(
                                        'travel_sheet_id',
                                        'travel_sheet_process_id',
                                        'id',
                                        'unprocessed',
                                        'good',
                                        'rework',
                                        'scrap',
                                        'convert',
                                        'alloy_mix',
                                        'nc',
                                        DB::raw("(`good`+`rework`+`scrap`+`convert`+`alloy_mix`+`nc`) as total"),
                                        DB::raw("ifnull(process_date,updated_at) as process_date")
                                    )->where('deleted',0)
                                    ->where('travel_sheet_process_id',$req->travel_sheet_process_id)->get();

            // get unprocess qty
            $unprocessed = $this->deductUnprocessed($req);

            // get travel sheet data
            $travel_sheet = $this->getTravelSheetData($req->jo_sequence);

            $data = [
                'msg' => 'Successfully saved.',
                'status' => 'success',
                'outputs' => $output,
                'unprocessed' => ($unprocessed > 0)? $unprocessed : 0,
                'travel_sheet' => $travel_sheet
            ];

            // fire event
            Event::fire(new TravelSheet($travel_sheet));
        }


        $this->_audit->insert([
            'user_type' => Auth::user()->user_type,
            'module_id' => $this->_moduleID,
            'module' => 'Production Output',
            'action' => 'Inputted data for Travel Sheet JO # '.$req->jo_no.', Product Code: '.strtoupper($req->prod_code).', for Process: '.strtoupper($req->current_process),
            'user' => Auth::user()->id,
            'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
        ]);

        
        return response()->json($data);
    }

    public function saveFGSummary($travel_sheet_process_id,$travel_sheet_id,$jo_no,$prod_order,$prod_code,$description,$prod_good)
    {
        //Update Status on process 
        ProdTravelSheetProcess::where('id',$travel_sheet_process_id)->update([ 'status' => 1 ]);

        $ptsp = ProdTravelSheetProcess::select(DB::raw("SUM(unprocessed) as unprocessed"))
                                ->where('travel_sheet_id',$travel_sheet_id)
                                ->where('status' , 1)
                                ->first();
        if($ptsp->unprocessed == 0){
            // $prod_ts = ProdTravelSheet::select('pre_travel_sheet_id')->where('id',$travel_sheet_id)->first();

            // PpcPreTravelSheet::where('id',$prod_ts->pre_travel_sheet_id)
            //                 ->update([ 'status' => 5 ]);

            ProdTravelSheetProcess::where('travel_sheet_id',$travel_sheet_id)
                                ->update([ 'status' => 5 ]);
        }

        $scno = explode( ',', $prod_order );
        $qty = 0;
        $qtynextsc = 0;
        $good = $prod_good;
        foreach ($scno as $key => $sc_no) {
            $jo_travel_sheet =  DB::table('ppc_pre_travel_sheets as pts')
                                    ->leftJoin('ppc_jo_details_summaries as js', 'js.jo_no','=','pts.jo_no')
                                    ->leftJoin('ppc_jo_details as jd', 'jd.jo_summary_id','=','js.id')
                                    ->where('pts.jo_no',$jo_no)
                                    ->where('jd.sc_no' , $sc_no)
                                    ->select('jd.sc_no as sc_no',
                                            'jd.sched_qty as sched_qty',
                                            'jd.back_order_qty as back_order_qty',
                                            'pts.id as id'
                                        )
                                    ->first();
            $RptFgSummary = RptFgSummary::where('sc_no' , $sc_no)
                                        ->where('prod_code',$prod_code)
                                        ->select('sc_no','qty')
                                        ->first();
            if(isset($RptFgSummary->sc_no)){
                if($RptFgSummary->qty == $jo_travel_sheet->sched_qty){
                    
                }else if($RptFgSummary->qty < $jo_travel_sheet->sched_qty){
                    $qty = $RptFgSummary->qty + $good;

                    if($qty > $jo_travel_sheet->sched_qty){
                        $qtynextsc = $qty - $jo_travel_sheet->sched_qty;
                        $good -= $jo_travel_sheet->sched_qty - $RptFgSummary->qty;         
                        RptFgSummary::where('sc_no' , $sc_no)->where('prod_code',$prod_code)
                                    ->update(['qty' => $jo_travel_sheet->sched_qty]);
                    }else{
                        RptFgSummary::where('sc_no' , $sc_no)->where('prod_code',$prod_code)
                                    ->increment('qty',(int)$good);
                        break;
                    }

                }
            }else{

                if($qtynextsc != 0){
                    $qty =  $qtynextsc;

                }else{
                    $qty = $good;
                }

                if($good <= $jo_travel_sheet->sched_qty){
                    $qty = $good;
                }else if($good > $jo_travel_sheet->sched_qty){
                    $qty = $jo_travel_sheet->sched_qty;
                }
                $good -= $jo_travel_sheet->sched_qty;


                $Rpt_Fg_Summary = new RptFgSummary();
                $Rpt_Fg_Summary->sc_no = $sc_no;
                $Rpt_Fg_Summary->prod_code = $prod_code;
                $Rpt_Fg_Summary->description = $description;
                $Rpt_Fg_Summary->order_qty = $jo_travel_sheet->back_order_qty;
                $Rpt_Fg_Summary->qty = $qty;
                $Rpt_Fg_Summary->status = 0;
                $Rpt_Fg_Summary->create_user = Auth::user()->id;
                $Rpt_Fg_Summary->update_user = Auth::user()->id;
                $Rpt_Fg_Summary->save();

                if($good <= 0){
                    break;
                }
            }

            //Update status Closed
            $totalqty = RptFgSummary::where('sc_no' , $sc_no)
                            ->where('prod_code',$prod_code)
                            ->select('qty')
                            ->first();
            if($jo_travel_sheet->sched_qty == $totalqty->qty && $sc_no == end($scno)){
                //PpcPreTravelSheet::where('id' ,$jo_travel_sheet->id)->update(['status' => 5 ]);
                ProdTravelSheet::where('jo_no' ,$jo_no)->update(['status' => 5 ]);
                PpcPreTravelSheet::where('jo_no' ,$jo_no)->update(['status' => 5]);
            }
        }
    }

    public function destroyFGSummary($travel_sheet_id ,$prod_good)
    {
        $pts = ProdTravelSheet::select('prod_order_no','prod_code')
                            ->where('id',$travel_sheet_id)->first();
        $scno = explode( ',', $pts->prod_order_no );
        $qty = 0;
        $good = $prod_good;
        foreach (array_reverse($scno) as $key => $sc_no) {
            $RptFgSummary = RptFgSummary::where('sc_no' , $sc_no)
                                        ->where('prod_code',$pts->prod_code)
                                        ->select('sc_no','qty')
                                        ->first();
            if(isset($RptFgSummary->qty)){
                if($RptFgSummary->qty <= $good){
                    $good -= $RptFgSummary->qty;
                    RptFgSummary::where('sc_no' , $sc_no)->where('prod_code',$pts->prod_code)->delete();
                }else{
                    RptFgSummary::where('sc_no' , $sc_no)->where('prod_code',$pts->prod_code)
                                    ->decrement('qty',(int)$good);
                    break;
                }

                if($good <= 0 ){
                    break;
                }
            }
        }
    }


    public function get_outputs(Request $req)
    {
        $data = ProdProductionOutput::select(
                                        'travel_sheet_id',
                                        'travel_sheet_process_id',
                                        'id',
                                        'unprocessed',
                                        'good',
                                        'rework',
                                        'scrap',
                                        'convert',
                                        'alloy_mix',
                                        'nc',
                                        DB::raw("(`good`+`rework`+`scrap`+`convert`+`alloy_mix`+`nc`) as total"),
                                        DB::raw("ifnull(process_date,updated_at) as process_date")
                                    )->where('travel_sheet_process_id',$req->id)
                                    ->where('deleted',0)->get();
        return response()->json($data);
    }

    public function checkSequence(Request $req)
    {
        $data = [ 'status' => 'success' ]; 
        $prod_travel = ProdTravelSheetProcess::where('id',$req->id)->first();
        $next_sequence = $prod_travel->sequence + 1;
        $prod_travelnext = ProdTravelSheetProcess::where('travel_sheet_id',$prod_travel->travel_sheet_id)
                                        ->where('sequence',$next_sequence)->first();
        if(isset($prod_travelnext->id)){
            $prod_output = ProdProductionOutput::where('travel_sheet_process_id',$prod_travelnext->id)->where('deleted',0)->count();            
            if($prod_output == 0){
                $data = [ 'status' => 'success' ]; 
            }else{
                $data = [ 'status' => 'failed' ];
            }
        }
       
        return response()->json($data);
    }

    public function destroy(Request $req)
    {
        $unprocessed = 0;
        foreach ($req->chkArray as $key => $value) {

            $prod_output = ProdProductionOutput::where('id',$value["id"])->where('deleted',0)->first();

            // return outputs to unprocess
            ProdTravelSheetProcess::where('id',$value["travel_sheet_process_id"])
                                    ->update([
                                        'unprocessed' => DB::raw("`unprocessed` + ".$prod_output->good." + ".$prod_output->rework." + ".$prod_output->scrap),
                                        'good' => DB::raw("`good` - ".$prod_output->good),
                                        'rework' => DB::raw("`rework` - ".$prod_output->rework),
                                        'scrap' => DB::raw("`scrap` - ".$prod_output->scrap),
                                        'convert' => DB::raw("`convert` - ".$prod_output->convert),
                                        'alloy_mix' => DB::raw("`alloy_mix` - ".$prod_output->alloy_mix),
                                        'nc' => DB::raw("`nc` - ".$prod_output->nc),
                                        'status' => 2,
                                        'update_user' => Auth::user()->id
                                    ]);

            $currenunprocessed  = $prod_output->good;
            $total = $prod_output->good + $prod_output->rework +  $prod_output->scrap + $prod_output->unprocessed;
            if($total > $unprocessed){
                $unprocessed = $total;
            }

            // $prod_travel = ProdTravelSheetProcess::where('id',$value["travel_sheet_process_id"])->first();
            // $next_sequence = $prod_travel->sequence + 1;
            // $lastsequnce = ProdTravelSheetProcess::where('travel_sheet_id',$prod_travel->travel_sheet_id)
            //                         ->where('sequence',$next_sequence)
            //                         ->update([
            //                             'unprocessed' => DB::raw("`unprocessed` - ".$currenunprocessed),
            //                         ]);

            // if($lastsequnce == 0 ){
            //     $this->destroyFGSummary($prod_travel->travel_sheet_id,$prod_output->good);
            // }

            ProdProductionOutput::where('id',$value["id"])->delete();
                                    // ->update([
                                    //     'deleted' => 1,
                                    //     'delete_user' => Auth::user()->id,
                                    //     'deleted_at' => date('Y-m-d H:i:s')
                                    // ]);
        }

        $ProdTravelSheet = ProdTravelSheet::where('id',$req->chkArray[0]['travel_sheet_id'])->first();

        $travel_sheet = $this->getTravelSheetData($ProdTravelSheet->jo_sequence);

        $data = [ 
            'unprocessed' => $unprocessed, 
            'travel_sheet' => $travel_sheet 
        ];

        $this->_audit->insert([
            'user_type' => Auth::user()->user_type,
            'module_id' => $this->_moduleID,
            'module' => 'Product Output',
            'action' => 'Deleted data ID ',
            'user' => Auth::user()->id,
            'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
        ]);

        return response()->json($data);
    }

    public function SearchJo(Request $req)
    {
        $data = [
            'msg' => 'No Travel Sheet available.',
            'status' => 'failed',
            'jo' => ''
        ];

        $travel_sheet = $this->getTravelSheetData($req->search_jo);

        if (count($travel_sheet) > 0) {
            $data = [
                'msg' => '',
                'status' => 'success',
                'jo' => $travel_sheet
            ];
        }

        return $data;
    }

    private function deductUnprocessed($req)
    {
        $unprocessed = ((int)$req->unprocessed == 0)? 0:(int)$req->unprocessed;
        $good = ((int)$req->good == 0)? 0:(int)$req->good;
        $rework = ((int)$req->rework == 0)? 0:(int)$req->rework;
        $scrap = ((int)$req->scrap == 0)? 0:(int)$req->scrap;
        $convert = ((int)$req->convert == 0)? 0:(int)$req->convert;
        $alloy_mix = ((int)$req->alloy_mix == 0)? 0:(int)$req->alloy_mix;
        $nc = ((int)$req->nc == 0)? 0:(int)$req->nc;
        
        $sum = $good + $rework + $scrap + $convert + $alloy_mix + $nc;
        $diff = $unprocessed - $sum;
        return $diff;
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

    public function getOperator(Request $req)
    {
        $error = ['errors' => ['operator' => 'Operator ID is not yet registered.']];
        $check = PpcOperator::where('operator_id',$req->operator)->count();
        if ($check == 0) { 
            return response()->json($error, 422);
        }else{
             return response()->json(['status  ' => 'success']);
        }
    }

    public function getTransferQty(Request $req)
    {
        $data =  DB::table('prod_transfer_items')->select(DB::raw("SUM(qty) as qty"))
                    ->where('current_process',$req->id)
                    ->where('item_status',0)
                    ->where('deleted','<>',1)
                    ->first();
        if(isset($data->qty)){
            return response()->json($data->qty);
        }else{
            $data = 0;
            return response()->json($data);
        }
    }

    private function getTravelSheetData($jo_sequence)
    {
        $data = [];

        $travel_sheet_count = DB::select(DB::raw("CALL GET_travel_sheet_production_output(3,'". $jo_sequence."',". Auth::user()->id.")"));

        if (count((array)$travel_sheet_count) > 0) {
            $data = DB::select(DB::raw("CALL GET_travel_sheet_production_output(NULL,'" . $jo_sequence . "'," . Auth::user()->id . ")"));

            if (count((array) $data) > 0) {
                //$jo = DB::select(DB::raw("CALL GET_jo_list_by_ts_id(" . $data[0]->pre_travel_sheet_id . ")"));

                // DB::table('v_jo_list')->where('travel_sheet_id', $data[0]->pre_travel_sheet_id)->first();

                if ($data[0]->travel_sheet_status == 4) {
                    // get PRODUCTION SIDE travel sheet data
                    // $travel_sheet = DB::table('prod_travel_sheets')
                    //     ->where('pre_travel_sheet_id', $data[0]->pre_travel_sheet_id)
                    //     ->select('id')
                    //     ->first();

                    $updated = DB::select(
                                    DB::raw("CALL UPDATE_travel_sheet_status(
                                        ". $data[0]->travel_sheet_id .",
                                        ". $data[0]->pre_travel_sheet_id .",
                                        '". $data[0]->jo_no ."'
                                    )")
                                );

                    // DB::table('prod_travel_sheet_processes')
                    //     ->where('travel_sheet_id', $travel_sheet->id)
                    //     ->where('sequence', 1)
                    //     ->update([
                    //         'is_current' => 1
                    //     ]);

                    // PpcPreTravelSheet::where('id', $data[0]->pre_travel_sheet_id)
                    //     ->update([
                    //         'status' => 2,
                    //         'updated_at' => date('Y-m-d H:i:s')
                    //     ]);

                    // PpcJoDetailsSummary::where('id', $jo[0]->jo_summary_id)
                    //     ->update([
                    //         'status' => 2,
                    //         'updated_at' => date('Y-m-d H:i:s')
                    //     ]);

                }
            }
            
        }        

        return $data;
    }
}
