<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use Illuminate\Support\Facades\Auth;
use App\AdminUserType;
use App\AdminUserTypeModule;
use App\AdminModule;
use DataTables;
use DB;

class UserTypeController extends Controller
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

		$this->_moduleID = $this->_helper->moduleID('A0003');
	}

	public function index()
	{
		$user_accesses = $this->_helper->UserAccess();
		$permission_access = $this->_helper->check_permission('A0003');

		return view('admin.user-type', [
			'user_accesses' => $user_accesses,
			'permission_access' => $permission_access
		]);
	}

	public function list()
	{
		$type = AdminUserType::select(
						'id',
						'description',
						'category',
						'created_at'
					)->get();
		
		return response()->json($type);
	}

	public function save(Request $req)
	{
		$this->validate($req, [
				'description' => 'required|max:50',
				'category' => 'required',
		]);
		$error = ['errors' => ['description' => 'The description has already been taken.']];
		 
		if (isset($req->id)) {
			$check = AdminUserType::where('description',$req->description)->where('id','!=',$req->id)->count();
			if ($check > 0){
				return response()->json($error, 422);
			}else{

				$type = AdminUserType::find($req->id);
				$type->description = strtoupper($req->description);
				$type->category = strtoupper($req->category);
				$type->update_user = Auth::user()->id;
				if ($type->update()) {

					if (count((array)$req->modules) > 0) {
						AdminUserTypeModule::where('user_type_id',$req->id)->delete();

						$modules = AdminModule::whereIn('id',$req->modules)
												->select([
													'id',
													'code',
													'title',
													'user_category'
												])->get();

						foreach ($modules as $key => $mod) {
							AdminUserTypeModule::insert([
													'user_type_id' => $req->id,
													'module_id' => $mod->id,
													'code' => $mod->code,
													'user_category' => $mod->user_category,
													'create_user' => Auth::user()->id,
													'update_user' => Auth::user()->id,
													'created_at' => date('Y-m-d H:i:s'),
													'updated_at' => date('Y-m-d H:i:s'),
												]);
						}
					}
						
				}

				$this->_audit->insert([
					'user_type' => Auth::user()->user_type,
					'module_id' => $this->_moduleID,
					'module' => 'User Type',
					'action' => 'Edited data ID: '.$req->id.', User Type: '.$type->description.' and Category: '.$type->category,
					'user' => Auth::user()->id,
					'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
				]);
			}
		} else {

			$this->validate($req, [
				'description' => 'unique:admin_user_types'
			]);

			$type = new AdminUserType();
			$type->description = strtoupper($req->description);
			$type->category = strtoupper($req->category);
			$type->create_user = Auth::user()->id;
			$type->update_user = Auth::user()->id;

			if ($type->save()) {
				if (count((array)$req->modules) > 0) {
					AdminUserTypeModule::where('user_type_id',$type->id)->delete();

					$modules = AdminModule::whereIn('id',$req->modules)
											->select([
												'id',
												'code',
												'title',
												'user_category'
											])->get();

					foreach ($modules as $key => $mod) {
						AdminUserTypeModule::insert([
												'user_type_id' => $type->id,
												'module_id' => $mod->id,
												'code' => $mod->code,
												'user_category' => $mod->user_category,
												'create_user' => Auth::user()->id,
												'update_user' => Auth::user()->id,
												'created_at' => date('Y-m-d H:i:s'),
												'updated_at' => date('Y-m-d H:i:s'),
											]);
					}
				}
			}

			$this->_audit->insert([
				'user_type' => Auth::user()->user_type,
				'module_id' => $this->_moduleID,
				'module' => 'User Type',
				'action' => 'Inserted data User Type: '.$req->description.' and Category: '.$type->category,
				'user' => Auth::user()->id,
				'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
			]);
		}

		return response()->json($type);
	}

	public function destroy(Request $req)
	{
		$data = [
			'msg' => "Deleting failed",
			'status' => "warning"
		];

		if (is_array($req->id)) {
			foreach ($req->id as $key => $id) {
				$type = AdminUserType::find($id);
				$type->delete();

				$data = [
					'msg' => "Data was successfully deleted.",
					'status' => "success"
				];
			}
		} else {
			$type = AdminUserType::find($id);
			$type->delete();

			$data = [
				'msg' => "Data was successfully deleted.",
				'status' => "success"
			];
		}

		$ids = implode(',', $req->id);

		$this->_audit->insert([
			'user_type' => Auth::user()->user_type,
			'module_id' => $this->_moduleID,
			'module' => 'User Type',
			'action' => 'Deleted data ID: '.$ids,
			'user' => Auth::user()->id,
			'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
		]);

		return response()->json($data);
	}

	public function module_list(Request $req)
	{
		$modules = [];
		if (empty($req->id)) {
			$modules = AdminModule::where('user_category','<>','ALL')
									->select(
										'id',
										'code',
										'title',
										DB::raw("'' as module_id")
									)->get();
		} else {
			$modules = DB::table('admin_modules as mod')
							->leftJoin('admin_user_type_modules as utm', function($join) use($req) {
								$join->on('mod.id','=','utm.module_id')->where('utm.user_type_id',$req->id);
							})
							->where('mod.user_category','<>','ALL')
							//->where('utm.user_category',$req->category)
							->select(
								DB::raw("mod.id as id"),
								DB::raw("mod.code as code"),
								DB::raw("mod.title as title"),
								DB::raw("mod.title as title"),
								DB::raw("utm.module_id as module_id")
							)->get();
		}

		return response()->json($modules);
	}
}
