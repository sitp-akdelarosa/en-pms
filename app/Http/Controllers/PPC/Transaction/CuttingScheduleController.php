<?php

namespace App\Http\Controllers\PPC\Transaction;

use App\Http\Controllers\Admin\AuditTrailController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HelpersController;
use App\PpcCuttingSchedule;
use App\PpcCuttingScheduleDetail;
use App\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;

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
		$permission_access = $this->_helper->check_permission('T0005');

		return view('ppc.transaction.cutting-schedule', [
			'user_accesses' => $user_accesses,
			'permission_access' => $permission_access
		]);
	}

	/**
     * Get List of J.O.
     */
    public function JOList(Request $req)
    {
        $data = DB::table('v_jo_list_for_cutting_sched')
					->where('user_id', Auth::user()->id)
					->where('rmw_no', $req->withdrawal_slip)
					->where('status','<>','3');

        // return response()->json($data);

        return DataTables::of($data)
						->addColumn('action', function($data) {
							return "<input type='checkbox' class='table-checkbox chk_jo' value='".$data->jo_no."' data-jo='".$data->jo_no."' />";
                        })
                        ->editColumn('status', function($data) {
                            switch ($data->status) {
                                case 0:
                                    return 'No quantity issued';
                                    break;
                                case 1:
                                    return 'Ready to Issue';
                                    break;
                                case 2:
                                    return 'In Production';
                                    break;
                                case 3:
                                    return 'Cancelled';
                                    break;
                                case 5:
                                    return 'CLOSED';
                                    break;
                            }
                        })
						->make(true);
	}
	
	/**
     * Get List of Leaders
     */
	public function getLeader()
	{
		$leader = User::select(
						'id as id',
						DB::raw("CONCAT(user_id,' | ',nickname, ' - ',firstname,' ',lastname) as text"),
						DB::raw("CONCAT(firstname,' ',lastname) as leader_name")
					)->get();

        return response()->json($leader);
	}

	/**
     * Save Cutting Sched
     */
	public function saveCuttingSched(Request $req)
	{
		$jo_no = '';
		$jo_arr = $req->jo_no;

		if (is_array($jo_arr)) {
			sort($jo_arr);
			$jo_no = implode(',',$jo_arr);
		}
		$exist = DB::table('v_cutting_sched_list')
					->where('create_user', Auth::user()->id)
					->where('jo_no', $jo_no)
					->where('withdrawal_slip_no', $req->withdrawal_slip)
					->count();

		if ($exist > 0) {
			$data = [
				'status' => 'failed',
				'msg' => 'Withdrawal Slip Number with corresponding J.O. number was already saved.',
			];
		} else {
			$data = [
				'status' => 'failed',
				'msg' => 'Saving Cutting Schedule details has failed.',
			];

			$user = Auth::user()->id;

			$cut_sched = new PpcCuttingSchedule;
			$cut_sched->withdrawal_slip_no = $req->withdrawal_slip;
			$cut_sched->date_issued = $req->date_issued;
			$cut_sched->machine_no = 'N/A';
			$cut_sched->prepared_by = $req->prepared_by;
			$cut_sched->leader = $this->LeaderName($req->leader);
			$cut_sched->leader_id = $req->leader;
			$cut_sched->create_user = $user;
			$cut_sched->update_user = $user;
			$cut_sched->iso_control_no = $req->iso_control_no;
			$cut_sched->created_at = date('Y-m-d H:i:s');
			$cut_sched->updated_at = date('Y-m-d H:i:s');

			if ($cut_sched->save()) {

				$data = DB::table('v_jo_list_for_cutting_sched')
							->where('user_id', Auth::user()->id)
							->where('rmw_no', $req->withdrawal_slip)
							->whereIn('jo_no',$req->jo_no)
							->where('status','<>','3')
							->get();

				foreach ($data as $key => $dt) {
					PpcCuttingScheduleDetail::insert([
						'cutt_id' => $cut_sched->id,
						'jo_no' => $dt->jo_no,
						'alloy' => $dt->alloy,
						'size' => $dt->size,
						'item' => $dt->item,
						'class' => $dt->class,
						'cut_weight' => $dt->cut_weight,
						'cut_length' => $dt->cut_length,
						'cut_width' => $dt->cut_width,
						'sc_no' => $dt->sc_no,
						'jo_qty' => $dt->sched_qty,
						'material_used' => $dt->material_used,
						'qty_needed' => $dt->assign_qty,
						'material_heat_no' => $dt->material_heat_no,
						'lot_no' => $dt->lot_no,
						'supplier_heat_no' => $dt->supplier_heat_no,
						'create_user' => Auth::user()->id,
						'update_user' => Auth::user()->id,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s'),
					]);
				}
				$data = [
					'status' => 'success',
					'msg' => 'Cutting Schedule details was successfully saved.',
				];

				$this->_audit->insert([
					'user_type' => Auth::user()->user_type,
					'module_id' => $this->_moduleID,
					'module' => 'Cutting Schedule',
					'action' => 'Added a new Cutting Schedule ID:'.$cut_sched->id,
					'user' => Auth::user()->id,
					'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
				]);
			}
		}

		return response()->json($data);
	}

	/**
     * Get Cutting Sched details
     */
	public function getCutSchedList()
	{
		$data = DB::table('v_cutting_sched_list')->where('create_user', Auth::user()->id);

	   return DataTables::of($data)
	   					->addColumn('action', function($data) {
							return "<button type='button' class='btn btn-sm bg-blue btn_reprint' value='".$data->id."'>
										<i class='fa fa-print'></i>		
									</button>";
                        })->make(true);
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

			if ($jo->cancelled == 1) {
				$data = [
					'msg' => 'J.O. # is Cancelled.',
					'status' => 'failed'
				];
				return response()->json($data);
			}

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
			'prepared_by' => 'required|string|max:255',
			'leader' => 'required|string|max:255',
			'iso_control_no' => 'required|string|max:255',
		]);

		$user = Auth::user()->id;

		$cut_sched = new PpcCuttingSchedule;
		$cut_sched->withdrawal_slip_no = $req->withdrawal_slip;
		$cut_sched->date_issued = $req->date_issued;
		$cut_sched->machine_no = 'N/A';
		$cut_sched->prepared_by = $req->prepared_by;
		$cut_sched->leader = $this->LeaderName($req->leader);
		$cut_sched->leader_id = $req->leader;
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
					'cut_width' => (isset($req->cut_width[$key]))? $req->cut_width[$key] : '',
					'schedule' => (isset($req->schedule[$key]))? $req->schedule[$key] : '', 
					'qty_needed_inbox' => (isset($req->qty_needed_inbox[$key]))? $req->qty_needed_inbox[$key] : '',
					'sc_no' => (isset($req->sc_no[$key]))? $req->sc_no[$key] : '',
					'jo_qty' => $req->jo_qty[$key],
					'material_used' => $req->material_used[$key],
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
				'user' => Auth::user()->id,
				'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
			]);
		}


		return response()->json($data);
	}

	

	private function LeaderName($id)
	{
		$leader = User::select(
						DB::raw("CONCAT(firstname,' ',lastname) as leader_name")
					)
					->where('id',$id)
					->first();
		if (count((array)$leader) > 0) {
			return $leader->leader_name;
		}

        return '';
	}
}
