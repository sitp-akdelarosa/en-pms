<?php

namespace App\Http\Controllers\PPC\Transaction;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use App\PpcProcess;
use App\PpcProductProcess;
use App\PpcDivision;
use App\PpcDivisionProcess;
use App\PpcPreTravelSheet;
use App\PpcPreTravelSheetProducts;
use App\PpcPreTravelSheetProcess;
use App\ProdTravelSheet;
use App\PpcJoTravelSheet;
use App\PpcJoDetailsSummary;
use App\PpcJoDetails;
use App\ProdTravelSheetProcess;
use App\AdminSettingIso;
use App\User;
use App\PpcUpdateInventory;
use DataTables;
use DB;

class TravelSheetController extends Controller
{
    protected $_helper = '';
    protected $_audit;
    protected $_moduleID;

    public function __construct()
    {
        // $this->middleware('auth');
        $this->_helper = new HelpersController;
        $this->_audit = new AuditTrailController;

        $this->_moduleID = $this->_helper->moduleID('T0006');
    }

    public function index()
    {
        $user_accesses = $this->_helper->UserAccess();
        return view('ppc.transaction.travel-sheet',['user_accesses' => $user_accesses]);
    }

    public function getJoDetails(Request $req)
    {
        $TO = 0;
        $FROM = 0;
        $data = [];

        if (isset($req->fromvalue)) {
            $from = PpcJoDetailsSummary::select('id')
                        ->where('jo_no', 'like', '%'.$req->fromvalue.'%')
                        ->first();
            $to =  PpcJoDetailsSummary::select('id')
                        ->where('jo_no', 'like', '%'.$req->tovalue.'%')
                        ->first();
            if (isset($from) && isset($to))
            {
                $FROM = $from->id;
                $TO = $to->id;

                if($req->tovalue == ''){
                    $TO = $FROM;
                }
            }
            
            $data = DB::select(
                        DB::raw("CALL GET_prod_sched_jo_list(".Auth::user()->id.",".$FROM.",".$TO.",".(int)$req->status.")")
                    );
        } else {

            if (is_null($req->status)) {
                $data = DB::select(
                            DB::raw("CALL GET_prod_sched_jo_list(".Auth::user()->id.",NULL,NULL,NULL)")
                        );
            } else {
                $data = DB::select(
                            DB::raw("CALL GET_prod_sched_jo_list(".Auth::user()->id.",NULL,NULL,".(int)$req->status.")")
                        );
            }
           
        }

        return DataTables::of($data)
						->addColumn('action', function($data) {
                            return "<button type='button' class='btn btn-sm bg-green open_travel_sheet_modal' 
                                        data-jo_no='".$data->jo_no."' data-prod_code='".$data->product_code."' 
                                        data-issued_qty='".$data->issued_qty."' data-id='".$data->travel_sheet_id."' 
                                        data-status='".$data->status."'  data-sched_qty='".$data->sched_qty."' 
                                        data-qty_per_sheet='".$data->qty_per_sheet."'  data-iso_code='".$data->iso_code."' 
                                        data-ship_date='".$data->ship_date."'  data-remarks='".$data->remarks."' 
                                        data-sc_no='".$data->sc_no."' data-idJO='".$data->jo_summary_id."' 
                                        title='Prepare Travel Sheet'><i class='fa fa-file-text-o'></i> </button>";
                        })
						->make(true);
    }

    public function getProcess(Request $req)
    {
        $data = [];
        $processes = [];
        $div_ids = [];
        if($req->sets == 'None'){
            $exist = DB::table('ppc_pre_travel_sheet_processes')
                        ->where('jo_no',strtoupper($req->jo_no))
                        ->count();
            if ($exist > 0) {
                $prod_processes = DB::select("select tsp.id,
                                                    tsp.`set`,
                                                    tsp.process_name as process,
                                                    tsp.sequence,
                                                    ifnull(tsp.remarks,pp.remarks) as remarks
                                            from ppc_pre_travel_sheet_processes as tsp
                                            join ppc_product_processes as pp
                                            on pp.process = tsp.process_name
                                            where tsp.jo_no = '".strtoupper($req->jo_no)."' 
                                            and prod_code = '".strtoupper($req->prod_code)."'
                                            group by tsp.id,
                                                    tsp.`set`,
                                                    tsp.process_name,
                                                    tsp.sequence
                                            order by sequence asc");
            } else {
                $product = DB::table('ppc_product_codes')->select('id')->where('product_code',$req->prod_code)->first();
                $prod_processes = DB::select(
                                        DB::raw("CALL GET_product_processes(".$product->id.",'".$req->prod_code."')")
                                    );
            }
            
        }else{
            $prod_processes = PpcProcess::where('set_id',$req->sets)
                                    ->select('id','process','sequence','remarks')
                                    ->groupBy('id','process','sequence','remarks')
                                    ->orderBy('sequence','asc')
                                    ->get();
        }

        foreach ($prod_processes as $key => $process) {
            $div_codes = DB::select(
                                        DB::raw("CALL GET_division_codes_for_processes('".$process->process."',".Auth::user()->id.")")
                                    );
            array_push($data,[
                'id' => $process->id,
                'process' => $process->process,
                'div_code' => $div_codes,
                'sequence' => $process->sequence,
                'remarks' => $process->remarks
            ]);
        }

        return response()->json($data);
    }

    public function getProcessDiv(Request $req)
    {
        $div_code = DB::select("SELECT d.div_code as div_code
                                from ppc_division_processes as dp
                                inner join ppc_divisions as d
                                on d.id = dp.division_id
                                inner join ppc_division_productlines as dpl
                                on d.id = dpl.division_id
                                inner join admin_assign_production_lines as pl
                                on dpl.productline = pl.product_line
                                where dp.process = '".$req->process."'
                                AND pl.user_id = ".Auth::user()->id."
                                AND d.is_disable = 0
                                group by d.div_code");

        return $div_code;
    }

    public function save_travel_sheet_setup(Request $req)
    {
       $this->validate($req, [
			'iso_no' => 'required',
            'issued_qty_per_sheet' => 'required',
            'issued_qty' => 'required',
            'ship_date' => 'required'
		]);

        $sc_no = [];

        // fixing sc number array
        foreach ($req->scno as $key => $scno) {
            array_push($sc_no,$scno);
        }

        if(!isset($req->process)){
            $data = [ 'msg' => "Please Input some Procces.", 'status' => "warning" ];
            return $data;
        }

        foreach ($req->process as $key => $process) {
            if ($req->div_code[$key] == ''){
                $data = [ 'msg' => "Please Fill up all the Division Code", 'status' => "warning" ];
                return $data;
            }
        }
        
        foreach ($req->issued_qty_per_sheet as $key => $issued_qty_per_sheet) {
            $scno = (isset($req->scno[$key]))? $req->scno[$key] : $sc_no[$key];
            if ($issued_qty_per_sheet == '' || $scno == ''){
                $data = [ 'msg' => "Please Fill up all the fields on Product Table", 'status' => "warning" ];
                return $data;
            }
        }

        $total_issued_qty = 0;
        $msgIssuedQty = 0;
        if (isset($req->travel_sheet_id) && !empty($req->travel_sheet_id)) {
            $pre_ts = PpcPreTravelSheet::find($req->travel_sheet_id);
            $pre_ts->jo_no = strtoupper($req->jo_no);
            $pre_ts->prod_code = strtoupper($req->prod_code);
            $pre_ts->issued_qty = $req->issued_qty;
            $pre_ts->iso_code = $req->iso_no;
            $pre_ts->iso_name = $this->getISObyCode($req->iso_no)->iso_name;
            $pre_ts->iso_photo = $this->getISObyCode($req->iso_no)->photo;
            $pre_ts->qty_per_sheet = $req->qty_per_sheet;
            $pre_ts->ship_date = $req->ship_date;
            $pre_ts->remarks = $req->ts_remarks;
            $pre_ts->update_user = Auth::user()->id;


            if ($pre_ts->update()) {
                PpcPreTravelSheetProducts::where('jo_no',$req->jo_no)->delete(); // pre_travel_sheet_id = $req->travel_sheet_id

                $jo_sequence = 0;
                $page_count = count($req->issued_qty_per_sheet);
                foreach ($req->issued_qty_per_sheet as $key => $issued_qty_per_sheet) {
                     $total_issued_qty += $issued_qty_per_sheet;
                     if($total_issued_qty > $req->issued_qty){
                        $msgIssuedQty = 1;
                     }
                    if ($page_count > 1) {
                        $seq = $key + 1;
                        $jo_sequence = sprintf("%02d", $seq);
                    } else {
                        $jo_sequence = '';
                    }

                    $scno = (isset($req->scno[$key]))? $req->scno[$key] : $sc_no[$key];
                    $stringed_scno = "";

                    if (!is_string($scno)) {
                        $stringed_scno = strtoupper(implode(',', $scno));
                    } else {
                        $stringed_scno = strtoupper($scno);
                    }

                    PpcPreTravelSheetProducts::create([
                        'pre_travel_sheet_id' => $pre_ts->id,
                        'jo_no' => strtoupper($req->jo_no),
                        'prod_code' => strtoupper($req->prod_code),
                        'issued_qty_per_sheet' => $issued_qty_per_sheet,
                        'jo_sequence' => ($jo_sequence == '')? $req->jo_no : $req->jo_no.'-'.$jo_sequence,
                        'sc_no' => $stringed_scno,
                        'create_user' => Auth::user()->id,
                        'update_user' => Auth::user()->id,
                    ]);
                }

                PpcPreTravelSheetProcess::where('pre_travel_sheet_id',$req->travel_sheet_id)->delete();
                foreach ($req->process as $key => $process) {
                    $update = DB::table('ppc_pre_travel_sheet_processes')->insert([
                        'pre_travel_sheet_id' => $pre_ts->id,
                        'jo_no' => strtoupper($req->jo_no),
                        'set' => $req->set,
                        'process_name' => strtoupper($process),
                        'div_code' => strtoupper($req->div_code[$key]),
                        'sequence' => $req->sequence[$key],
                        'remarks' => $req->remarks[$key],
                        'create_user' => Auth::user()->id,
                        'update_user' => Auth::user()->id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }

            }
            $this->saveProdTravelSheet($req->jo_no,$req->iso_no);

            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module_id' => $this->_moduleID,
                'module' => 'Travel Sheet',
                'action' => 'Edited Travel Sheet Jo_No: '.strtoupper($req->jo_no),
                'user' => Auth::user()->id,
                'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
            ]);
        } else {
            $pre_ts = new PpcPreTravelSheet();
            $pre_ts->jo_no = strtoupper($req->jo_no);
            $pre_ts->prod_code = strtoupper($req->prod_code);
            $pre_ts->issued_qty = $req->issued_qty;
            $pre_ts->qty_per_sheet = $req->qty_per_sheet;
            $pre_ts->status = 1;
            $pre_ts->iso_code = $req->iso_no;
            $pre_ts->iso_name = $this->getISObyCode($req->iso_no)->iso_name;
            $pre_ts->iso_photo = $this->getISObyCode($req->iso_no)->photo;
            $pre_ts->ship_date = $req->ship_date;
            $pre_ts->remarks = $req->ts_remarks;
            $pre_ts->create_user = Auth::user()->id;
            $pre_ts->update_user = Auth::user()->id;

            if ($pre_ts->save()) {

                $jo_sequence = 0;
                $page_count = count($req->issued_qty_per_sheet);
                foreach ($req->issued_qty_per_sheet as $key => $issued_qty_per_sheet) {
                    if ($page_count > 1) {
                        $seq = $key + 1;
                        $jo_sequence = sprintf("%02d", $seq);
                    } else {
                        $jo_sequence = '';
                    }

                    $scno = (isset($req->scno[$key]))? $req->scno[$key] : $sc_no[$key];
                    $stringed_scno = "";

                    if (!is_string($scno)) {
                        $stringed_scno = strtoupper(implode(',', $scno));
                    } else {
                        $stringed_scno = strtoupper($scno);
                    }

                    PpcPreTravelSheetProducts::create([
                        'pre_travel_sheet_id' => $pre_ts->id,
                        'jo_no' => strtoupper($req->jo_no),
                        'prod_code' => strtoupper($req->prod_code),
                        'issued_qty_per_sheet' => $issued_qty_per_sheet,
                        'jo_sequence' => ($jo_sequence == '')? $req->jo_no : $req->jo_no.'-'.$jo_sequence,
                        'sc_no' => $stringed_scno,
                        'create_user' => Auth::user()->id,
                        'update_user' => Auth::user()->id,
                    ]);
                }

                foreach ($req->process as $key => $process) {
                    PpcPreTravelSheetProcess::create([
                        'pre_travel_sheet_id' => $pre_ts->id,
                        'jo_no' => strtoupper($req->jo_no),
                        'set' => $req->set,
                        'process_name' => strtoupper($process),
                        'sequence' => $req->sequence[$key],
                        'remarks' => $req->remarks[$key],
                        'div_code' => strtoupper($req->div_code[$key]),
                        'create_user' => Auth::user()->id,
                        'update_user' => Auth::user()->id,
                    ]);
                }

                PpcJoDetailsSummary::where('jo_no' , $req->jo_no)->update(['status' => 1 ]);
                
                $this->saveProdTravelSheet($req->jo_no,$req->iso_no);
            }
            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module_id' => $this->_moduleID,
                'module' => 'Travel Sheet',
                'action' => 'Preparation for Travel Sheet Jo_No: '.strtoupper($req->jo_no),
                'user' => Auth::user()->id,
                'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
            ]);
        }
            $data = [ 
            'msg' => "Preparation for Travel Sheet was successfully saved.",
            'status' => "success",
            'travel_sheet_id' => $pre_ts->id,
            'issuedQty' => $msgIssuedQty,
            ];
        return $data;
    }

    private function saveProdTravelSheet($jo_no,$iso_code)
    {
        $ProdTrav = ProdTravelSheet::select('id')->where('jo_no',$jo_no)->get();
        foreach ($ProdTrav as $key => $pd) {
            ProdTravelSheetProcess::where('travel_sheet_id', $pd->id)->delete();
        }    
        ProdTravelSheet::where('jo_no',$jo_no)->delete();

        $travel_sheet = DB::select("select tsp.pre_travel_sheet_id as id,
                                            tsp.prod_code as prod_code,
                                            tsp.issued_qty_per_sheet as issued_qty,
                                            tsp.jo_sequence as jo_sequence,
                                            ts.jo_no as jo_no,
                                            tsp.sc_no as sc_no,
                                            ts.description as description,
                                            SUM(ts.back_order_qty) as order_qty,
                                            SUM(ts.sched_qty) as sched_qty,
                                            ts.material_used as material_used,
                                            ts.material_heat_no as material_heat_no,
                                            ts.lot_no as prod_heat_no,
                                            IF(LEFT(ts.product_code,1) = 'Z','Finish','Semi-Finish') as type 
                                    from ppc_pre_travel_sheet_products as tsp
                                    join v_jo_list as ts
                                    on tsp.jo_no = ts.jo_no
                                    where tsp.jo_no = '".$jo_no."'
                                    GROUP BY tsp.pre_travel_sheet_id,
                                            tsp.prod_code,
                                            tsp.issued_qty_per_sheet,
                                            tsp.jo_sequence,
                                            ts.jo_no,
                                            tsp.sc_no,
                                            ts.description,
                                            ts.material_used,
                                            ts.material_heat_no,
                                            ts.lot_no,
                                            ts.product_code");

        foreach ($travel_sheet as $key => $ts) {
            $jo = ProdTravelSheet::create([
                    'jo_no' => $jo_no,
                    'jo_sequence' => $ts->jo_sequence,
                    'prod_order_no' => $ts->sc_no,
                    'prod_code' => $ts->prod_code,
                    'description' => $ts->description,
                    'material_used' => $ts->material_used,
                    'material_heat_no' => $ts->material_heat_no,
                    'lot_no' => $ts->prod_heat_no,
                    'type' => $ts->type,
                    'order_qty' => $ts->order_qty,
                    'issued_qty' => $ts->issued_qty,
                    'total_issued_qty' => $this->getTotalIssuedQty($ts->id),
                    'status' => 0,
                    'iso_code' => $iso_code,
                    'iso_name' => $this->getISObyCode($iso_code)->iso_name,
                    'iso_photo' => $this->getISObyCode($iso_code)->photo,
                    'pre_travel_sheet_id' => $ts->id,
                    'create_user' => Auth::user()->id,
                    'update_user' => Auth::user()->id,
                ]);

            $processes = DB::table('ppc_pre_travel_sheet_processes')
                            ->where('pre_travel_sheet_id',$ts->id)
                            ->select('div_code','process_name','sequence')
                            ->get();

            foreach ($processes as $key => $proc) {
                ProdTravelSheetProcess::create([
                    'travel_sheet_id' => $jo->id,
                    'unprocessed' => ($proc->sequence == 1)? $ts->issued_qty : 0,
                    'process' => $proc->process_name,
                    'previous_process' => $this->getPrevProcess($ts->id,$proc->sequence),
                    'div_code' => $proc->div_code,
                    'sequence' => $proc->sequence,
                    'status' => 0,
                    'create_user' => Auth::user()->id,
                    'update_user' => Auth::user()->id,
                ]);
            }
        }
    }

    private function getISObyCode($code)
    {
        $iso = AdminSettingIso::where('iso_code',$code)->first();
        return $iso;
    }

    public function getPreparedby()
    {
        $users = User::select(
                        DB::raw("CONCAT(firstname,' ',lastname) as name")
                    )->where('user_type','PPC')
                    ->where('user_id','<>',Auth::user()->id)->get();

        return response()->json($users);
    }

    public function getLeader()
    {
        $leaders = PpcDivision::select('leader')->groupBy('leader')->get();
                    
        return response()->json($leaders);
    }

    public function getPreTravelSheetData(Request $req)
    {
        $data = [
            'prod' => '',
            'process' => '',
            'prod_code' => '',
            'sets' => ''
        ];

        DB::statement(DB::raw('set @row_num:=0'));

        $prod = DB::select("SELECT prod_code,
                                    issued_qty_per_sheet as issued_qty,
                                    @row_num := ifnull(@row_num,0) + 1 as id,
                                    sc_no
                            FROM ppc_pre_travel_sheet_products
                            where pre_travel_sheet_id = ".$req->id);

        $trav_sheet = PpcPreTravelSheetProducts::where('pre_travel_sheet_id',$req->id)
                                        ->select('prod_code')
                                        ->first();

        $process = PpcPreTravelSheetProcess::where('pre_travel_sheet_id',$req->id)->get();
        $process_set = PpcPreTravelSheetProcess::where('pre_travel_sheet_id',$req->id)
                            ->select('set')->first();

        $data = [
            'prod' => $prod,
            'process' => $process,
            'prod_code' => (isset($trav_sheet->prod_code))? $trav_sheet->prod_code: '',
            'sets' => (isset($process_set->set))? $process_set->set: ''
        ];

        return response()->json($data);
    }

    private function getPrevProcess($id,$sequence)
    {
        $sequence--;
        $prev = DB::table('ppc_pre_travel_sheet_processes')
                    ->where('pre_travel_sheet_id',$id)
                    ->where('sequence',$sequence)
                    ->select('process_name')
                    ->first();
        if (count((array)$prev) > 0) {
            return $prev->process_name;
        }

        return '';
    }

    private function getTotalIssuedQty($id)
    {
        $data = DB::table('ppc_pre_travel_sheet_products')
                    ->where('pre_travel_sheet_id',$id)
                    ->select(
                        DB::raw('SUM(issued_qty_per_sheet) as total_issued_qty')
                    )->first();

        if (count((array)$data) > 0) {
            return $data->total_issued_qty;
        }
    }

    public function getSc_no(Request $req)
    {
        $scno = [];
        $back_order_qty = [];

        $sc_no = DB::table('ppc_jo_details')
                    ->where('jo_summary_id',$req->id)
                    ->select('sc_no',DB::raw('SUM(back_order_qty) as back_order_qty'))
                    ->groupBy('sc_no')
                    ->get();

        foreach ($sc_no as $key => $sc) {
            array_push($scno, [$sc->sc_no]);
            array_push($back_order_qty, [$sc->back_order_qty]);
        }

        $data = [
            'sc_no' => $scno,
            'back_order_qty' => $back_order_qty
        ];
        
        return response()->json($data);
    }

    public function proceedToProduction(Request $req)
    {
        $data = [
            'msg' => 'Confirmation has failed.',
            'status' => 'failed'
        ];

        $updated_id = 0;
        $jo_no = '';

        if (count($req->id) > 0) {
            $com = '';
            foreach ($req->id as $key => $id) {
                $update = PpcPreTravelSheet::where('id',$id)
                                            ->update([
                                                'status' => 4,
                                                'update_user' => Auth::user()->id,
                                                'updated_at' => date('Y-m-d H:i:s')
                                            ]);

                if ($update) {
                    $jo = DB::table('v_jo_list')->where('travel_sheet_id', (int)$id)->first();

                    PpcJoDetailsSummary::where('id', $jo->jo_summary_id)
                                        ->update([
                                            'status' => 4,
                                            'update_user' => Auth::user()->id,
                                            'updated_at' => date('Y-m-d H:i:s')
                                        ]);

                    if ($jo_no !== '') {
                        $com = ',';
                    }
                    $jo_no .= $com.$jo_no;
                    $updated_id++;
                }
            }

            if ($updated_id > 0) {
                $data = [
                    'msg' => 'J.o. # '.$jo_no.' has successfully confirmed to proceed to Production.',
                    'status' => 'success'
                ];
            }
        }

        return response()->json($data);
    }
}
