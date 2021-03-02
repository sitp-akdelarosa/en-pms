<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use DataTables;
use App\AdminAssignMaterialType;
use App\User;
use App\PpcDropdownItem;
use DB;

class AssignMaterialTypeController extends Controller
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

		$this->_moduleID = $this->_helper->moduleID('A0006');
	}

	public function index()
	{
		$user_accesses = $this->_helper->UserAccess();
		$permission_access = $this->_helper->check_permission('A0006');
		
		return view('admin.assign-material-type', [
			'user_accesses' => $user_accesses,
			'permission_access' => $permission_access
		]);
	}

	public function materialtype_list(Request $req)
	{
		$mat_type = DB::table('admin_assign_material_types as a')
						->join('users as u','a.user_id','=','u.id')
						->whereIn('a.user_id',$req->user_id)
						->select(
							'a.id',
							'a.user_id',
							'a.material_type',
							DB::raw("DATE_FORMAT(a.updated_at, '%Y/%m/%d %h:%i %p') as updated_at"),
							DB::raw("CONCAT(u.firstname,' ',u.lastname) as fullname")
						)
						->groupBy(
							'a.id',
							'a.user_id',
							'a.material_type',
							DB::raw("DATE_FORMAT(a.updated_at, '%Y/%m/%d %h:%i %p')"),
							DB::raw("CONCAT(u.firstname,' ',u.lastname)")
						)
						->orderby('id','desc')->get();

		return response()->json($mat_type);
	}

	public function save(Request $req)
	{
		$data = [
			'msg' => "Saving failed",
			'status' => "warning",
			'user_id' => []
		];

		$params = [];
		$last_user = 0;

		foreach ($req->user_id as $key => $user_id) {
			//AdminAssignMaterialType::where('user_id',$user_id)->delete();

			foreach ($req->material_type as $key => $material_type) {
				
				if ($material_type !== '' || !empty($material_type)) {
					AdminAssignMaterialType::where('user_id',$user_id)
											->where('material_type',$material_type)->delete();
					array_push($params, [
						'user_id' => $user_id,
						'material_type' => $material_type,
						'create_user' => Auth::user()->id,
						'update_user' => Auth::user()->id,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s'),
					]);
				}
			}
		}

		$insert = array_chunk($params, 1000);

		foreach ($insert as $batch) {
			AdminAssignMaterialType::insert($batch);

			$data = [
				'msg' => "Successfully saved.",
				'status' => "success",
				'user_id' => $req->user_id
			];
		}

		$this->_audit->insert([
			'user_type' => Auth::user()->user_type,
			'module_id' => $this->_moduleID,
			'module' => 'Assign Material Type',
			'action' => 'Edited data user ID '.$user_id.', Material Type: '.implode(',', $req->material_type),
			'user' => Auth::user()->id
		]);

		

		return response()->json($data);
	}

	public function destroy(Request $req)
	{
		$data = [
			'msg' => "Deleting failed",
			'status' => "warning"
		];

		if (is_array($req->id)) {
			foreach ($req->id as $key => $id) {
				$prod = AdminAssignMaterialType::find($id);
				$prod->delete();

				$data = [
					'msg' => "Data was successfully deleted.",
					'status' => "success"
				];
			}
		} else {
			$prod = AdminAssignMaterialType::find($id);
			$prod->delete();

			$data = [
				'msg' => "Data was successfully deleted.",
				'status' => "success"
			];
		}

		$ids = implode(',', $req->id);

		$this->_audit->insert([
			'user_type' => Auth::user()->user_type,
			'module_id' => $this->_moduleID,
			'module' => 'Assign Material Type',
			'action' => 'Deleted data ID '.$ids,
			'user' => Auth::user()->id
		]);

		return response()->json($data);
	}

	public function users()
	{
		$users = User::select('id','user_id',DB::raw("CONCAT(firstname,' ',lastname) as fullname"))
					->whereIn('user_category',['OFFICE','ALL'])
					->get();
		return response()->json($users);
	}

	public function material_type_selection()
	{
		$items = PpcDropdownItem::whereIn('dropdown_name_id',[8]) // product Line && Material Master
								->select('dropdown_item','dropdown_name')
								->groupBy('dropdown_item','dropdown_name')
								->orderby('dropdown_item','ASC')->get();
		return response()->json($items);
	}

}
