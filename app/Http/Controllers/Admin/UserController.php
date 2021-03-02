<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use Illuminate\Support\Facades\Hash;
use DataTables;
use App\User;
use App\PpcDivision;
use App\AdminModule;
use App\AdminModuleAccess;
use App\AdminUserType;
use Carbon\Carbon;
use DB;

class UserController extends Controller
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

		$this->_moduleID = $this->_helper->moduleID('A0001');
	}

	public function index()
	{
		$user_accesses = $this->_helper->UserAccess();
		$permission_access = $this->_helper->check_permission('A0001');

		return view('admin.users', [
			'user_accesses' => $user_accesses,
			'permission_access' => $permission_access
		]);
	}

	public function user_list()
	{
		$users = DB::select("SELECT u.id as id,
								   u.user_id as user_id,
								   u.firstname as firstname,
								   u.nickname as nickname,
								   u.lastname as lastname,
								   ifnull(u.email,'') as email,
								   ut.description as user_type,
								   ifnull(CONCAT(d.div_code,' - ',d.div_name),'') as div_code,
								   u.actual_password as actual_password,
								   u.deleted as del_flag,
								   DATE_FORMAT(u.created_at, '%Y-%m-%d %H:%i %p') as created_at
							FROM enpms.users as u
							INNER JOIN  enpms.admin_user_types as ut 
							ON u.user_type =  ut.id
							LEFT JOIN enpms.ppc_divisions as d
							on u.div_code = d.id
							where u.deleted <> 1
							ORDER BY u.id DESC");

		return $users;
	}

	public function save(Request $req)
	{
		if (isset($req->id)) {
			return $this->update($req);
		} else {
			return $this->store($req);
		}
	}

	private function store($req)
	{
		$this->validate($req, [
			'user_id' => 'required|string|max:50|min:1|unique:users,user_id,NULL,id,deleted_at,NULL',
			'firstname' => 'required|string|max:50|min:1',
			'lastname' => 'required|string|max:50|min:1',
			'password' => 'required|string|min:5|confirmed',
			'user_type' => 'required',
			// 'email' => 'unique:users',
			// 'div_code' => 'required',
			'photo' => 'image|mimes:jpeg,png,jpg,gif,svg',
		]);

		$exists = User::where('user_id',$req->user_id)->where('deleted',0)->first();
		if (is_null($exists)) {

			$user = new User();

			$user->user_id = $req->user_id;
			$user->firstname = $req->firstname;
			$user->lastname = $req->lastname;
			$user->email = $req->email;
			$user->password = Hash::make($req->password);
			$user->actual_password = $req->password;
			$user->user_type = $req->user_type;
			$user->user_category = $this->user_category($req->user_type);
			$user->div_code = $req->div_code;
			$user->nickname = $req->nickname;
			$user->create_user = Auth::user()->id;
			$user->update_user = Auth::user()->id;

			if (isset($req->is_admin)) {
				$user->is_admin = $req->is_admin;
			}

			if ($user->save()) {
				if (isset($req->is_admin)) {
					$this->give_admin_access($user->id);
				}

				if (isset($req->rw) || isset($req->ro)) {
					$this->give_user_access($user->id,$req);
				}

				$id = $user->id;
				$photo = $req->get('photo');

				$this->_helper->uploadProfilePhoto($id,$req->photo);

				$this->_audit->insert([
					'user_type' => Auth::user()->user_type,
					'module_id' => $this->_moduleID,
					'module' => 'User Master',
					'action' => 'Inserted data User ID: '.$req->user_id,
					'user' => Auth::user()->id,
					'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
				]);

				return response()->json($user);
			} else {
				return response()->json(['msg'=>"Somehing went wrong while saving.",'status' => 'failed']);
			}
			
		} else {
			return response()->json(['msg'=>"User ID already taken",'status' => 'failed']);
		}
	}

	public function show($id)
	{
		$users = User::find($id);

		return response()->json($users);
	}

	private function update($req)
	{
		$this->validate($req, [
			'user_id' => 'required|string|max:255',
			'firstname' => 'required|string|max:255',
			'lastname' => 'required|string|max:255',
			'user_type' => 'required',
			// 'div_code' => 'required',
			'photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5244',
			'password' => 'confirmed',
		]);

		$exists = User::where('user_id',$req->user_id)->where('id','!=', $req->id) ->first();

		if (is_null($exists)) {

			$user = User::find($req->id);

			$user->user_id = $req->user_id;
			$user->firstname = $req->firstname;
			$user->lastname = $req->lastname;
			$user->email = $req->email;
			$user->user_type = $req->user_type;
			$user->nickname = $req->nickname;
			$user->user_category = $this->user_category($req->user_type);
			$user->update_user = Auth::user()->id;
			$user->div_code = $req->div_code;

			if (isset($req->is_admin)) {
				$user->is_admin = $req->is_admin;
				$this->give_admin_access($req->id);
			}  else {
				$user->is_admin = 0;
				AdminModuleAccess::where('user_id',$req->id)
								->where('user_category','ALL')
								->delete();
			}

			if (isset($req->password)) {
				$user->password = Hash::make($req->password);
				$user->actual_password = $req->password;
			}

			$user->update();

			if (isset($req->rw) || isset($req->ro)) {
				$this->give_user_access($user->id,$req);
			}

			$this->_helper->uploadProfilePhoto($req->id,$req->photo);

			$this->_audit->insert([
				'user_type' => Auth::user()->user_type,
				'module_id' => $this->_moduleID,
				'module' => 'User Master',
				'action' => 'Edited data ID '.$req->id.', User ID: '.$req->user_id,
				'user' => Auth::user()->id,
				'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
			]);

			return response()->json($user);
		}else{
			return response()->json(['msg'=>"User ID already taken",'status' => 'failed']);
		}
	}

	public function destroy(Request $req)
	{
		$data = [
			'msg' => 'Deleting process was unsuccessful.',
			'status' => "warning"
		];

		foreach ($req->ids as $key => $id) {
			if (Auth::user()->id == $id) {
				$data = [
					'msg' => "You cannot delete yourself.",
					'status' => "warning"
				];
				return response()->json($data);
			}
		}

		$ExistingDivision = DB::table('ppc_divisions as d')
								->join('users as u','u.id','d.user_id')
								->whereIn('d.user_id',$req->ids)
								->select(
									DB::raw('d.user_id as user_id'),
									DB::raw('u.lastname as lastname')
								)
								->groupBy('d.user_id','u.lastname')
								->get();

		if (count((array)$ExistingDivision) > 0) {
			$extID = [];
			$extLastName = [];
			foreach ($ExistingDivision as $key => $extDiv) {
				array_push($extID, $extDiv->user_id);
				array_push($extLastName, $extDiv->lastname);
			}

			$msg = "You cannot delete ";
			
			if (count($extLastName) > 1 ) {
				$msg .= nl2br("users that still existing on Division Master. \r\n");

				foreach ($extLastName as $key => $lastname) {
					$msg .= "- ". $lastname."\r\n";
				}
			} 

			if (count($extLastName) == 1 ) {
				$msg .= nl2br("user that still existing on Division Master. \r\n");
				$msg .= "- ". $extLastName[0];
			}

			if (count($extLastName) > 0) {
				$data = [
					'msg' => $msg,
					'status' => "warning"
				];

				return response()->json($data);
			}
		}

		$ExistingProductLine = DB::table('admin_assign_production_lines as a')
									->join('users as u','u.id','a.user_id')
									->whereIn('a.user_id',$req->ids)
									->select(
										DB::raw('a.user_id as user_id'),
										DB::raw('u.lastname as lastname')
									)
									->groupBy('a.user_id','u.lastname')
									->get();

		if (count((array)$ExistingProductLine) > 0) {
			$extID = [];
			$extLastName = [];
			foreach ($ExistingProductLine as $key => $extProd) {
				array_push($extID, $extProd->user_id);
				array_push($extLastName, $extProd->lastname);
			}

			$msg = "You cannot delete ";
			
			if (count($extLastName) > 1 ) {
				$msg .= nl2br("users that still existing on Assign Production Line. \r\n");

				foreach ($extLastName as $key => $lastname) {
					$msg .= nl2br("- ". $lastname."\r\n");
				}
			}

			if (count($extLastName) == 1) {
				$msg .= nl2br("user that still existing on Assign Production Line. \r\n");
				$msg .= "- ". $extLastName[0];
			}

			if (count($extLastName) > 0) {
				$data = [
					'msg' => $msg,
					'status' => "warning"
				];
				return response()->json($data);
			}
		}

		$query = User::whereIn('id', $req->ids)
					->update([
						'deleted' => 1,
						'delete_user' => Auth::user()->id,
						'update_user' => Auth::user()->id,
						'deleted_at' => Carbon::now(),
						'updated_at' => Carbon::now()
					]);

		// $user = User::find($req->id);
		// $user->del_flag = 1;
		// $user->update_user = Auth::user()->id;
		// $user->deleted_at = Carbon::now();
		// $user->update();

		if ($query) {
			$msg = "User was successfully deleted.";

			if (count($req->ids) > 0) {
				$msg = "Users were successfully deleted.";
			}

			$data = [
				'msg' => $msg,
				'status' => "success"
			];

			$this->_audit->insert([
				'user_type' => Auth::user()->user_type,
				'module_id' => $this->_moduleID,
				'module' => 'User Master',
				'action' => 'Deleted data ID '.$req->id,
				'user' => Auth::user()->id,
				'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
			]);
		}

		return response()->json($data);
	}

	public function getDivCode(Request $req)
	{
	    $div = DB::table('ppc_divisions')
	    			->select(
    					'id as id', 
    					DB::raw("CONCAT(div_code,' - ',div_name) as text")
    				)
    				->where('is_disable',0)
    				->get();
	    return response()->json($div);
	}

	public function getUsersType(Request $req)
	{
	    $ut = DB::table('admin_user_types')
	    		->select('id as id', 'description as text')
	    		->get();

	    return response()->json($ut);
	}

	public function user_modules(Request $req)
	{
		$modules = [];
		$user_id_cond = "";
		$user_type_cond = "";

		if (!empty($req->id)) {
			$user_id_cond = "AND acc.user_id = '".$req->id."'";
		}

		if (!empty($req->user_type)) {
			$user_type_cond = "AND utm.user_type_id = '".$req->user_type."'";
		}

		if (!empty($req->id)) {
			$modules = DB::select("select `mod`.id as id,
											`mod`.`code` as `code`,
											`mod`.title as title,
											IFNULL(acc.access,0) as access
									FROM admin_modules as `mod`
									LEFT JOIN admin_module_accesses as acc
									ON `mod`.`code` = acc.`code`
									 ".$user_id_cond."
									LEFT JOIN admin_user_type_modules as utm
									ON `mod`.`code` = utm.`code`
									WHERE `mod`.user_category <> 'ALL'
									 ".$user_type_cond." 
									GROUP BY `mod`.id,
											`mod`.`code`,
									        `mod`.title,
											IFNULL(acc.access,0)");
		} else {
			$modules = DB::select("select `mod`.id as id,
											`mod`.`code` as `code`,
											`mod`.title as title,
											0 as access
									FROM admin_modules as `mod`
									LEFT JOIN admin_user_type_modules as utm
									ON `mod`.`code` = utm.`code`
									WHERE `mod`.user_category <> 'ALL'
									 ".$user_type_cond." 
									GROUP BY `mod`.id,
											`mod`.`code`,
									        `mod`.title");
		}

		

		// if ($req->user_type == '') {
		// 	$modules = AdminModule::where('user_category','<>','Administrator')->get();
		// } else {
			// if ($req->id == '') {
			// 	$userTypeCategory = $this->user_category($req->user_type);

			// 	if ($userTypeCategory == 'ALL') {
			// 		// $modules = AdminModule::where('user_category','<>','Administrator')->get();
			// 		$modules = DB::table('admin_modules as mod')
			// 					->leftJoin('admin_module_accesses as acc', function($join) use($req) {
			// 						$join->on('mod.code','=','acc.code')->where('acc.user_id',$req->id);
			// 					})
			// 					->where('mod.user_category','<>','Administrator')
			// 					// ->where('acc.user_id',$req->id)
			// 					->select(
			// 						DB::raw("mod.id as id"),
			// 						DB::raw("mod.code as code"),
			// 						DB::raw("mod.title as title"),
			// 						DB::raw("IFNULL(acc.access,0) as access")
			// 					)->get();
			// 	} else {
			// 		//$modules = AdminModule::where('user_category','<>','Administrator')->get();
			// 		//$modules = AdminModule::where('user_category', $userTypeCategory)->get();
			// 		$modules = DB::table('admin_user_type_modules as utm')
			// 					->join('admin_modules as m','utm.module_id','m.id')
			// 					->where('utm.user_type_id',$req->user_type)
			// 					->select(
			// 						DB::raw('utm.module_id as id'),
			// 						DB::raw('utm.code as code'),
			// 						DB::raw('m.title')
			// 					)
			// 					->get();
			// 	}
				
			// } else {
			// 	$modules = DB::table('admin_modules as mod')
			// 				->leftJoin('admin_module_accesses as acc', function($join) use($req) {
			// 					$join->on('mod.code','=','acc.code')->where('acc.user_id',$req->id);
			// 				})
			// 				->leftJoin('admin_user_type_modules as utm','mod.code','utm.code')
			// 				->where('utm.user_type_id',$req->user_type)
			// 				// ->where('acc.user_id',$req->id)
			// 				->select(
			// 					DB::raw("mod.id as id"),
			// 					DB::raw("mod.code as code"),
			// 					DB::raw("mod.title as title"),
			// 					DB::raw("IFNULL(acc.access,0) as access")
			// 				)->get();
				// if ($this->user_category($req->user_type) == 'ALL') {
				// 	$modules = DB::table('admin_modules as mod')
				// 				->leftJoin('admin_module_accesses as acc', function($join) use($req) {
				// 					$join->on('mod.code','=','acc.code')->where('acc.user_id',$req->id);
				// 				})
				// 				->where('mod.user_category','<>','Administrator')
				// 				// ->where('acc.user_id',$req->id)
				// 				->select(
				// 					DB::raw("mod.id as id"),
				// 					DB::raw("mod.code as code"),
				// 					DB::raw("mod.title as title"),
				// 					DB::raw("IFNULL(acc.access,0) as access")
				// 				)->get();
				// } else {
				// 	$modules = DB::table('admin_modules as mod')
				// 				->leftJoin('admin_module_accesses as acc', function($join) use($req) {
				// 					$join->on('mod.code','=','acc.code')->where('acc.user_id',$req->id);
				// 				})
				// 				->leftJoin('admin_user_type_modules as utm','mod.code','utm.code')
				// 				->where('utm.user_type_id',$req->user_type)
				// 				// ->where('acc.user_id',$req->id)
				// 				->select(
				// 					DB::raw("mod.id as id"),
				// 					DB::raw("mod.code as code"),
				// 					DB::raw("mod.title as title"),
				// 					DB::raw("IFNULL(acc.access,0) as access")
				// 				)->get();
				// }
				// if (count($modules) < 1) {
				//     $modules = AdminModule::where('user_type',$req->user_type)->get();
				// }
			// }
		// }
			
		return response()->json($modules);
	}

	private function give_user_access($id,$req)
	{
		AdminModuleAccess::where('user_id',$id)
						->where('user_category','<>','ALL')
						// ->where('user_category',$this->user_category($req->user_type))
						->delete();

		$user_access = [];

		if (isset($req->rw)) {
			$modules_rw = AdminModule::whereIn('id',$req->rw)->get();
			$user_access = [];
			foreach ($modules_rw as $key => $module_rw) {
				array_push($user_access,[
					'id' => $module_rw->id,
					'code' => $module_rw->code,
					'title' => $module_rw->title,
					'category' => $module_rw->category,
					'user_category' => ($module_rw->user_category == '' || $module_rw->user_category == null)? $this->user_category($req->user_type) : $module_rw->user_category,
					'user_id' => $id,
					'access' => 1,
					'create_user' => Auth::user()->id,
					'update_user' => Auth::user()->id,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
				]);
			}
		}

		if (isset($req->ro)) {
			$modules_ro = AdminModule::whereIn('id',$req->ro)->get();
			foreach ($modules_ro as $key => $module_ro) {
				if (!isset($module_ro->id) || !isset($req->rw)) {
					array_push($user_access,[
						'id' => $module_ro->id,
						'code' => $module_ro->code,
						'title' => $module_ro->title,
						'category' => $module_ro->category,
						'user_category' => ($module_ro->user_category == '' || $module_ro->user_category == null)? $this->user_category($req->user_type) : $module_ro->user_category,
						'user_id' => $id,
						'access' => 2,
						'create_user' => Auth::user()->id,
						'update_user' => Auth::user()->id,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s'),
					]);
				} else {
					if (in_array($module_ro->id, $req->rw)) {
						
					} else {
						array_push($user_access,[
							'id' => $module_ro->id,
							'code' => $module_ro->code,
							'title' => $module_ro->title,
							'category' => $module_ro->category,
							'user_category' => ($module_ro->user_category == '' || $module_ro->user_category == null)? $this->user_category($req->user_type) : $module_ro->user_category,
							'user_id' => $id,
							'access' => 2,
							'create_user' => Auth::user()->id,
							'update_user' => Auth::user()->id,
							'created_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s'),
						]);
					}
				}
			}
		}

		$sort = [];
		$user_accesses = [];
		foreach ($user_access as $key => $row) {
			$sort[$key] = $row['id'];
		}

		array_multisort($sort, SORT_ASC, $user_access);

		foreach ($user_access as $key => $row) {
			array_push($user_accesses,[
				'code' => $row['code'],
				'title' => $row['title'],
				'category' => $row['category'],
				'user_category' => $row['user_category'],
				'user_id' => $row['user_id'],
				'access' => $row['access'],
				'create_user' => Auth::user()->id,
				'update_user' => Auth::user()->id,
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			]);
		}

		array_unique($user_accesses,SORT_REGULAR);

		$allowAccess = array_chunk($user_accesses, 1000);

		foreach ($allowAccess as $access) {
			AdminModuleAccess::insert($access);
		}
	}

	private function give_admin_access($id)
	{
		$modules = AdminModule::where('user_category','ALL')->get(); // ALL
		$user_access = [];
		foreach ($modules as $key => $module) {
			array_push($user_access,[
				'code' => $module->code,
				'title' => $module->title,
				'category' => $module->category,
				'user_category' => $module->user_category,
				'user_id' => $id,
				'access' => 1,
				'create_user' => Auth::user()->id,
				'update_user' => Auth::user()->id,
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			]);
		}

		$allowAccess = array_chunk($user_access, 1000);
		AdminModuleAccess::where('user_id',$id)
						->where('user_category','ALL')
						->delete();

		foreach ($allowAccess as $access) {
			AdminModuleAccess::insert($access);
		}
	}

	private function user_category($user_type)
	{
		$category = AdminUserType::where('id',$user_type)->select('category') //description
								->first();
		if (count((array)$category) > 0) {
			return $category->category; // ex. PRODUCTION / OFFICE / ALL
		}

		return 'OFFICE';
	}
}
