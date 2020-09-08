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
use App\PpcJoTravelSheet;
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
        $this->middleware('auth');
        $this->_helper = new HelpersController;
        $this->_audit = new AuditTrailController;

        $this->_moduleID = $this->_helper->moduleID('T0007');
    }

    public function index()
    {
        $user_accesses = $this->_helper->UserAccess();
        return view('production.transaction.production-output',['user_accesses' => $user_accesses]);
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
            'machine_no' => 'required'
        ],
        [
            'operator.required' => 'The Operator ID is required.'
        ]);
        //Update status on production 
        $pre_id = ProdTravelSheet::where('id',$req->travel_sheet_id)
                            ->select('pre_travel_sheet_id')->first();
        PpcPreTravelSheet::where('id',$pre_id->pre_travel_sheet_id)->update(['status' => 2]);
        PpcJoTravelSheet::where('jo_no' , $req->jo_no)->update(['status' => 2 ]);
        ProdTravelSheet::where('id',$req->travel_sheet_id)->update(['status' => 2 ]);

        $prod_output = new ProdProductionOutput();

        $prod_output->travel_sheet_id = $req->travel_sheet_id;
        $prod_output->travel_sheet_process_id = $req->travel_sheet_process_id;
        $prod_output->jo_no = $req->jo_no;

        $prod_output->unprocessed = $this->deductUnprocessed($req->unprocessed,$req->good,$req->rework,$req->scrap);
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
        $prod_output->create_user = $req->create_user;
        $prod_output->update_user = $req->update_user;

        if ($prod_output->save()) {
            ProdTravelSheetProcess::where('id',$req->travel_sheet_process_id)
                                    ->update([
                                        'unprocessed' => $this->deductUnprocessed($req->unprocessed,$req->good,$req->rework,$req->scrap),
                                        'good' => DB::raw("`good` + ".$prod_output->good),
                                        'rework' => DB::raw("`rework` + ".$prod_output->rework),
                                        'scrap' => DB::raw("`scrap` + ".$prod_output->scrap),
                                        'convert' => DB::raw("`convert` + ".$prod_output->convert),
                                        'alloy_mix' => DB::raw("`alloy_mix` + ".$prod_output->alloy_mix),
                                        'nc' => DB::raw("`nc` + ".$prod_output->nc),
                                        'machine_no' => $prod_output->machine_no,
                                        'operator' => $prod_output->operator,
                                        'leader' => Auth::user()->id,
                                        'update_user' => Auth::user()->id
                                    ]);

            $next_sequence = $req->sequence + 1;

            $lastsequnce = ProdTravelSheetProcess::where([
                                        ['travel_sheet_id',$req->travel_sheet_id],
                                        ['sequence',$next_sequence]
                                    ])->where('status' , 0)
                                    ->update([
                                        'unprocessed' => DB::raw("`unprocessed` + ".$prod_output->good)
                                    ]);
            if($lastsequnce == 0){
                $this->saveFGSummary($req->travel_sheet_process_id,$req->travel_sheet_id,$req->jo_no,
                                $req->prod_order,$req->prod_code,$req->description,$prod_output->good);
            }

            $output = ProdProductionOutput::where('travel_sheet_process_id',$req->travel_sheet_process_id)->get();

            $unprocessed = $this->deductUnprocessed($req->unprocessed,$req->good,$req->rework,$req->scrap);

            $travel_sheet = $this->getTravelSheetData($req->jo_sequence);

            $data = [
                'msg' => 'Successfully saved.',
                'status' => 'success',
                'outputs' => $output,
                'unprocessed' => ($unprocessed > 0)? $unprocessed : 0,
                'travel_sheet' => $travel_sheet
            ];

            Event::fire(new TravelSheet($travel_sheet));
        }


        $this->_audit->insert([
            'user_type' => Auth::user()->user_type,
            'module_id' => $this->_moduleID,
            'module' => 'Production Output',
            'action' => 'Inputted data for Travel Sheet JO # '.$req->jo_no.', Product Code: '.strtoupper($req->prod_code).', for Process: '.strtoupper($req->current_process),
            'user' => Auth::user()->id
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
            ProdTravelSheetProcess::where('travel_sheet_id',$travel_sheet_id)
                                ->update([ 'status' => 5 ]);
        }

        $scno = explode( ',', $prod_order );
        $qty = 0;
        $qtynextsc = 0;
        $good = $prod_good;
        foreach ($scno as $key => $sc_no) {
            $jo_travel_sheet =  DB::table('ppc_jo_travel_sheets as jts')
                                    ->leftJoin('ppc_jo_details as jd', 'jd.jo_summary_id','=','jts.id')
                                    ->where('jts.jo_no',$jo_no)
                                    ->where('jd.sc_no' , $sc_no)
                                    ->select('jd.sc_no as sc_no',
                                            'jd.sched_qty as sched_qty',
                                            'jd.back_order_qty as back_order_qty',
                                            'jts.id as id'
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
                PpcJoTravelSheet::where('id' ,$jo_travel_sheet->id)->update(['status' => 5 ]);
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
        $data = ProdProductionOutput::where('travel_sheet_process_id',$req->id)->get();
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
            $prod_output = ProdProductionOutput::where('travel_sheet_process_id',$prod_travelnext->id)->count();            
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

            $prod_output = ProdProductionOutput::where('id',$value["id"])->first();
            ProdTravelSheetProcess::where('id',$value["travel_sheet_process_id"])
                                    ->update([
                                        'unprocessed' => DB::raw("`unprocessed` + ".$prod_output->good." + ".$prod_output->rework." + ".$prod_output->scrap),
                                        'good' => DB::raw("`good` - ".$prod_output->good),
                                        'rework' => DB::raw("`rework` - ".$prod_output->rework),
                                        'scrap' => DB::raw("`scrap` - ".$prod_output->scrap),
                                        'convert' => DB::raw("`convert` - ".$prod_output->convert),
                                        'alloy_mix' => DB::raw("`alloy_mix` - ".$prod_output->alloy_mix),
                                        'nc' => DB::raw("`nc` - ".$prod_output->nc),
                                        'update_user' => Auth::user()->id
                                    ]);

            $currenunprocessed  = $prod_output->good;
            $total = $prod_output->good + $prod_output->rework +  $prod_output->scrap + $prod_output->unprocessed;
            if($total > $unprocessed){
                $unprocessed = $total;
            }

            $prod_travel = ProdTravelSheetProcess::where('id',$value["travel_sheet_process_id"])->first();
            $next_sequence = $prod_travel->sequence + 1;
            $lastsequnce = ProdTravelSheetProcess::where('travel_sheet_id',$prod_travel->travel_sheet_id)
                                    ->where('sequence',$next_sequence)
                                    ->update([
                                        'unprocessed' => DB::raw("`unprocessed` - ".$currenunprocessed)
                                    ]);

            if($lastsequnce == 0 ){
                $this->destroyFGSummary($prod_travel->travel_sheet_id,$prod_output->good);
            }

            ProdProductionOutput::where('id',$value["id"])->delete();
        }

        $ProdTravelSheet = ProdTravelSheet::where('id',$req->chkArray[0]['travel_sheet_id'])->first();
        $travel_sheet = $this->getTravelSheetData($ProdTravelSheet->jo_sequence);
        $data = [ 'unprocessed' => $unprocessed , 'travel_sheet' => $travel_sheet ];

        $this->_audit->insert([
            'user_type' => Auth::user()->user_type,
            'module_id' => $this->_moduleID,
            'module' => 'Product Output',
            'action' => 'Deleted data ID ',
            'user' => Auth::user()->id
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

    private function deductUnprocessed($unprocessed,$good,$rework,$scrap)
    {
        $sum = $good + $rework + $scrap;
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
        $div_codes = $this->getDivCode();

        $travel_sheet = DB::table('prod_travel_sheets as ts')
                            ->join('prod_travel_sheet_processes as p','ts.id','=','p.travel_sheet_id')
                            ->leftJoin('ppc_product_codes as pc','pc.product_code','=','ts.prod_code')
                            ->where('ts.jo_sequence','like',$jo_sequence.'%')
                            ->where('ts.status','!=', 3)
                            // ->whereIn('p.div_code',$div_codes)
                            ->select(
                                DB::raw("p.travel_sheet_id as travel_sheet_id"),
                                DB::raw("ts.jo_no as jo_no"),
                                DB::raw("ts.status as pts_status"),
                                DB::raw("ts.prod_code as prod_code"),
                                DB::raw("ts.prod_order_no as prod_order_no"),
                                DB::raw("ifnull(pc.code_description,ts.description) as description"),
                                DB::raw("ts.material_used as material_used"),
                                DB::raw("ts.material_heat_no as material_heat_no"),
                                DB::raw("ts.lot_no as lot_no"),
                                DB::raw("ts.type as type"),
                                DB::raw("ts.order_qty as order_qty"),
                                DB::raw("ts.issued_qty as issued_qty"),
                                DB::raw("ts.total_issued_qty as total_issued_qty"),
                                DB::raw("ts.jo_sequence as jo_sequence"),
                                DB::raw("p.id as id"),
                                DB::raw("p.process as process"),
                                DB::raw("p.previous_process as previous_process"),
                                DB::raw("p.unprocessed as unprocessed"),
                                DB::raw("p.good as good"),
                                DB::raw("p.rework as rework"),
                                DB::raw("p.scrap as scrap"), 
                                DB::raw("p.sequence as sequence"),
                                DB::raw("p.div_code as div_code"),
                                DB::raw("p.status as status"),
                                DB::raw("ts.pre_travel_sheet_id as pre_travel_sheet_id")             
                            )
                            ->orderBy('sequence' , 'ASC')
                            ->get();

        return $travel_sheet;
    }
}
