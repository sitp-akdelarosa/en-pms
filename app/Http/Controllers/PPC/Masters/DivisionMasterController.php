<?php

namespace App\Http\Controllers\PPC\Masters;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use DataTables;
use App\PpcDivision;
use App\PpcDivisionProcess;
use App\PpcDivisionProductline;
use DB;
use App\User;

class DivisionMasterController extends Controller
{
	protected $_helper;
	protected $_audit;
	protected $_moduleID;

	public function __construct()
	{
		// $this->middleware('ajax-session-expired');
		// $this->middleware('auth');
		$this->_helper = new HelpersController;
		$this->_audit = new AuditTrailController;

		$this->_moduleID = $this->_helper->moduleID('M0001');
	}

	public function index()
	{
		$user_accesses = $this->_helper->UserAccess();
		$permission_access = $this->_helper->check_permission('M0001');

		return view('ppc.masters.division-master', [
			'user_accesses' => $user_accesses,
			'permission_access' => $permission_access
		]);
	}

	public function division_list()
	{
		$division = PpcDivision::select([
						'id',
						'div_code',
						'div_name',
						'plant',
						'leader',
						'leader_id',
						'is_disable',
						'user_id',
						'updated_at'
					]); //->orderBy('updated_at','desc');
		return DataTables::of($division)
						->editColumn('id', function($data) {
							return $data->id;
						})
						->addColumn('action', function($data) {
							return '<button class="btn btn-sm bg-blue btn_edit_div" data-id="'.$data->id.'" 
										data-div_code="'.$data->div_code.'" data-div_name="'.$data->div_name.'" 
										data-plant="'.$data->plant.'" data-leader="'.$data->leader_id.'" 
										data-leader_id="'.$data->leader_id.'" data-user_id="'.$data->leader_id.'">
										<i class="fa fa-edit"></i>
									</button>';
						})
						->make(true);
	}

	public function save(Request $req)
	{
		$this->validate($req, [
			'div_code' => 'required|string|max:75',
			'div_name' => 'required|string|max:75',
			'plant' => 'required|max:75',
			'leader' => 'required|max:75'
		]);

		if ($req->id !== '' && !empty($req->id)) {
			$exists = PpcDivision::where('div_code',$req->div_code)
								->where('id','!=', $req->id) 
								->first();
		} else {
			$exists = PpcDivision::where('div_code',$req->div_code)->first();
		}

		if (is_null($exists)) {

			if ($req->id !== '' && !empty($req->id)) {
				$div = PpcDivision::find($req->id);

				$div->div_code = strtoupper($req->div_code);
				$div->div_name = strtoupper($req->div_name);
				$div->plant = strtoupper($req->plant);
				$div->leader = $this->getuserID($req->leader);
				$div->leader_id = $req->leader;
				$div->user_id = $req->leader;
				$div->update_user = Auth::user()->id;

				$div->update();

				PpcDivisionProcess::where('division_id',$req->id)->delete();

				if (count((array)$req->processes) > 0) {
					foreach ($req->processes as $key => $process) {
						PpcDivisionProcess::create([
							'process' => $process,
							'division_id' => $div->id
						]);
					}
				}
				PpcDivisionProductline::where('division_id',$req->id)->delete();

				if (count((array)$req->productlines) > 0) {
					foreach ($req->productlines as $key => $productline) {
						PpcDivisionProductline::create([
							'productline' => $productline,
							'division_id' => $div->id
						]);
					}
				}

				$this->_audit->insert([
					'user_type' => Auth::user()->user_type,
					'module_id' => $this->_moduleID,
					'module' => 'Division Master',
					'action' => 'Edited data ID '.$req->id.', Division Code: '.$div->div_code,
					'user' => Auth::user()->id,
					'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
				]);

			} else {
				$div = new PpcDivision();

				$div->div_code = strtoupper($req->div_code);
				$div->div_name = strtoupper($req->div_name);
				$div->plant = strtoupper($req->plant);
				$div->leader = $this->getuserID($req->leader);
				$div->leader_id = $req->leader;
				$div->user_id = $req->leader;
				$div->create_user = Auth::user()->id;
				$div->update_user = Auth::user()->id;

				$div->save();

				if (count((array)$req->processes) > 0) {
					foreach ($req->processes as $key => $process) {
						PpcDivisionProcess::create([
							'process' => strtoupper($process),
							'division_id' => $div->id
						]);
					}
				}
				if (count((array)$req->productlines) > 0) {
					foreach ($req->productlines as $key => $productline) {
						PpcDivisionProductline::create([
							'productline' => strtoupper($productline),
							'division_id' => $div->id
						]);
					}
				}

				$this->_audit->insert([
					'user_type' => Auth::user()->user_type,
					'module_id' => $this->_moduleID,
					'module' => 'Division Master',
					'action' => 'Inserted data Division Code: '.$div->div_code,
					'user' => Auth::user()->id,
					'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
				]);
			}

			return response()->json($div);

		}else{
			return response()->json(['msg'=>"Division Code already taken",'status' => 'failed']);
		}
	}

	public function destroy(Request $req)
	{
		$UserCheckTrue=0;
		foreach ($req->id as $key => $id) {
			$UserCheck = User::where('div_code',$id)->first();;
			if (is_null($UserCheck)) {
					
			}else{
				$UserCheckTrue++;
			}
		}

		if($UserCheckTrue == 0){


		$data = [
			'msg' => "Deleting failed",
			'status' => "warning"
		];

		if (is_array($req->id)) {
			foreach ($req->id as $key => $id) {
				$div = PpcDivision::find($id);
				$div->delete();

				PpcDivisionProcess::where('division_id',$id)->delete();
				PpcDivisionProductline::where('division_id',$id)->delete();

				$data = [
					'msg' => "Data was successfully deleted.",
					'status' => "success"
				];
			}
		} else {
			$div = PpcDivision::find($id);
			$div->delete();

			PpcDivisionProcess::where('division_id',$id)->delete();
			PpcDivisionProductline::where('division_id',$id)->delete();

			$data = [
				'msg' => "Data was successfully deleted.",
				'status' => "success"
			];
		}
		
		$ids = implode(',', $req->id);

		$this->_audit->insert([
			'user_type' => Auth::user()->user_type,
			'module_id' => $this->_moduleID,
			'module' => 'Division Master',
			'action' => 'Deleted data ID: '.$ids,
			'user' => Auth::user()->id,
			'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
		]);

		}else{
			$data = [
				'msg' => "Some user still using the division code",
				'status' => "failed"
			];
		}
		return response()->json($data);
	}

	public function getuserID($user_id)
	{
		$user = User::select(DB::raw("CONCAT(firstname,' ',lastname)  as fullname"))
					->where('id', $user_id)
					->first();
		return $user->fullname;
	}

	public function getProcess(Request $req)
	{
		$pro = PpcDivisionProcess::where('division_id',$req->division_id)->get();
		return response()->json($pro);
	}

	public function getProductline(Request $req)
	{
		$pro = DB::table('ppc_division_productlines')
					->select('productline','division_id')
					->where('division_id',$req->division_id)
					->groupBy('productline','division_id')
					->get();
		return response()->json($pro);
	} 

	public function disableEnableDivision(Request $req)
	{
		$_is_disable;
		$res = PpcDivision::find($req->id);

		if ($res->is_disable == 0) {
			$res->is_disable = 1;
			$_is_disable = 1;
		}else {
			$res->is_disable = 0;
			$_is_disable = 0;
		}

		$res->update();
		return response()->json($_is_disable);
	}

	public function getLeader()
	{
		$leader = User::select(
						'id as id',
						DB::raw("CONCAT(user_id,' | ',nickname, ' - ',firstname,' ',lastname) as text")
					)->get();

        return response()->json($leader);
	}
}
