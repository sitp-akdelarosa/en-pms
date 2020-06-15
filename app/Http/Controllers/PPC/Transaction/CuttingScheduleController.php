<?php

namespace App\Http\Controllers\PPC\Transaction;

use App\Http\Controllers\Admin\AuditTrailController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HelpersController;
use App\PpcCuttingSchedule;
use App\PpcCuttingScheduleDetail;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CuttingScheduleController extends Controller
{
    protected $_helper;
    protected $_audit;

    public function __construct()
    {
        $this->middleware('auth');
        $this->_helper = new HelpersController;
        $this->_audit = new AuditTrailController;
    }

    public function index()
    {
        $user_accesses = $this->_helper->UserAccess();
        return view('ppc.transaction.cutting-schedule', ['user_accesses' => $user_accesses]);
    }

    public function getJoDetailsCut(Request $req)
    {
        $jo_no = $req->trans_no;

        $err = [
            'status' => 'failed',
            'msg' => 'No data found.',
        ];

        $jo = DB::table('ppc_jo_details_summaries')
            ->select('id')
            ->where('jo_no', $jo_no)
            ->first();

        if (count((array) $jo) > 0) {

            $data = DB::select(" SELECT * FROM vCuttingSched where jo_summary_id = " . $jo->id);

            if (count((array) $data) > 0) {
                return response()->json($data);
            } else {
                return response()->json($err);
            }
        } else {
            return response()->json($err);
        }
    }

    public function getProdline()
    {
        $user_id = Auth::id();
        $res = DB::table('admin_assign_production_lines')
            ->select(
                DB::raw("count(*) as counter")
            )
            ->where('user_id', Auth::id())
            ->whereIn('product_line', [
                'S/S BUTT WELD FITTING',
                'S/S JIS BUTT WELD FITTING',
                'S/S SK SERIES FITTING',
                'S/S JPI 150# BUTT WELD FITTING',
                'S/S BUTT WELD FITTING LONG TANGENT',
            ])->get();

        return response()->json($res);
    }

    public function save(Request $req)
    {
        $this->validate($req, [
            'machine_no' => 'required|string|max:255',
            'prepared_by' => 'required|string|max:255',
            'leader' => 'required|string|max:255',
            'iso_control_no' => 'required|string|max:255',
        ]);

        $user = Auth::user()->user_id;

        $cut_sched = new PpcCuttingSchedule;
        $cut_sched->withdrawal_slip_no = $req->withdrawal_slip;
        $cut_sched->date_issued = $req->date_issued;
        $cut_sched->machine_no = $req->machine_no;
        $cut_sched->prepared_by = $req->prepared_by;
        $cut_sched->leader = $req->leader;
        $cut_sched->create_user = $user;
        $cut_sched->update_user = $user;
        $cut_sched->iso_control_no = $req->iso_control_no;
        $cut_sched->created_at = date('Y-m-d H:i:s');
        $cut_sched->updated_at = date('Y-m-d H:i:s');

        if ($cut_sched->save()) {
            foreach ($req->no as $key => $no) {
                PpcCuttingScheduleDetail::insert([
                    'cutt_id' => $cut_sched->id,
                    'item_no' => $req->no[$key],
                    'alloy' => $req->p_alloy[$key],
                    'size' => $req->p_size[$key],
                    'item' => $req->p_item[$key],
                    'class' => $req->p_class[$key],
                    'cut_weight' => $req->cut_weight[$key],
                    'cut_length' => $req->cut_length[$key],
                    'schedule' => $req->schedule[$key], 
                    'qty_needed_inbox' => $req->qty_needed_inbox[$key],
                    'sc_no' => $req->sc_no[$key],
                    'order_qty' => $req->order_qty[$key],
                    'qty_needed' => $req->needed_qty[$key],
                    'qty_cut' => '0',
                    'plate_qty' => $req->issued_qty[$key],
                    'material_desc_item' => $req->item[$key],
                    'material_desc_size' => $req->size[$key],
                    'material_desc_heat_no' => $req->mat_heat_no[$key],
                    'material_desc_lot_no' => $req->lot_no[$key],
                    'material_desc_supplier_heat_no' => $req->supplier_heat_no[$key],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
            $data = [
                'status' => 'success',
                'msg' => 'Schedule successfully saved.',
            ];
        }
        return response()->json($data);
    }

    public function getCutSchedDetails()
    {
        $data = DB::select("SELECT
                                cs.id AS id,
                                GROUP_CONCAT(csd.item_no SEPARATOR ',') AS item_nos,
                                cs.created_at AS created_at
                            FROM ppc_cutting_schedules as cs
                            JOIN ppc_cutting_schedule_details as csd
                            ON csd.cutt_id = cs.id
                            WHERE cs.create_user = '".Auth::user()->user_id."'
                            GROUP BY cs.id,cs.created_at
                            ORDER BY cs.created_at DESC
                            ");
       return response()->json($data);
    }
}
