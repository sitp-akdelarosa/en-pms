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
	protected $_moduleID;


	public function __construct()
	{
		$this->middleware('auth');
		$this->_helper = new HelpersController;
		$this->_audit = new AuditTrailController;

		$this->_moduleID = $this->_helper->moduleID('T0005');
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

			$data = DB::select(" SELECT * FROM vcuttingsched where jo_summary_id = " . $jo->id);

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
			->where('user_id', Auth::user()->id)
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

		$user = Auth::user()->id;

		$cut_sched = new PpcCuttingSchedule;
		$cut_sched->withdrawal_slip_no = $req->withdrawal_slip;
		$cut_sched->date_issued = $req->date_issued;
		$cut_sched->machine_no = $req->machine_no;
		$cut_sched->prepared_by = $req->prepared_by;
		$cut_sched->leader = $req->leader;
		//$cut_sched->leader_id = $req->leader;
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
					'alloy' => (isset($req->p_alloy[$key]))? $req->p_alloy[$key] : '',
					'size' => (isset($req->p_size[$key]))? $req->p_size[$key] : '',
					'item' => (isset($req->p_item[$key]))? $req->p_item[$key] : '',
					'class' => (isset($req->p_class[$key]))? $req->p_class[$key] : '',
					'cut_weight' => (isset($req->cut_weight[$key]))? $req->cut_weight[$key] : '',
					'cut_length' => (isset($req->cut_length[$key]))? $req->cut_length[$key] : '',
					'schedule' => (isset($req->schedule[$key]))? $req->schedule[$key] : '', 
					'qty_needed_inbox' => (isset($req->qty_needed_inbox[$key]))? $req->qty_needed_inbox[$key] : '',
					'sc_no' => (isset($req->sc_no[$key]))? $req->sc_no[$key] : '',
					'order_qty' => $req->order_qty[$key],
					'qty_needed' => $req->needed_qty[$key],
					'qty_cut' => '0',
					'plate_qty' => (isset($req->issued_qty[$key]))? $req->issued_qty[$key] : '',
					'material_desc_item' => (isset($req->item[$key]))? $req->item[$key] : '',
					'material_desc_size' => (isset($req->size[$key]))? $req->size[$key] : '',
					'material_desc_heat_no' => (isset($req->mat_heat_no[$key]))? $req->mat_heat_no[$key] : '',
					'material_desc_lot_no' => (isset($req->lot_no[$key]))? $req->lot_no[$key] : '',
					'material_desc_supplier_heat_no' => (isset($req->supplier_heat_no[$key]))? $req->supplier_heat_no[$key] : '',
					'create_user' => Auth::user()->id,
					'update_user' => Auth::user()->id,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
				]);
			}
			$data = [
				'status' => 'success',
				'msg' => 'Schedule successfully saved.',
			];

			$this->_audit->insert([
				'user_type' => Auth::user()->user_type,
				'module_id' => $this->_moduleID,
				'module' => 'Cutting Schedule',
				'action' => 'Added a new Cutting Schedule ID:'.$cut_sched->id,
				'user' => Auth::user()->id
			]);
		}


		return response()->json($data);
	}

	public function getCutSchedDetails()
	{
		$data = DB::select("SELECT
								cs.id AS id,
								GROUP_CONCAT(csd.item_no SEPARATOR ',') AS item_nos,
								cs.withdrawal_slip_no,
								cs.iso_control_no,
								cs.date_issued,
								cs.machine_no,
								cs.leader,
								cs.prepared_by,
								cs.created_at AS created_at
							FROM ppc_cutting_schedules as cs
							JOIN ppc_cutting_schedule_details as csd
							ON csd.cutt_id = cs.id
							WHERE cs.create_user = '".Auth::user()->id."'
							GROUP BY cs.id,
									cs.withdrawal_slip_no,
									cs.iso_control_no,
									cs.date_issued,
									cs.machine_no,
									cs.leader,
									cs.prepared_by,
									cs.created_at
							ORDER BY cs.created_at DESC
							");
	   return response()->json($data);
	}
}
