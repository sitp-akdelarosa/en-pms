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
use App\AdminAssignProductionLine;
use DB;

class UserController extends Controller
{
    protected $_helper;
    protected $_audit;

    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
        $this->_helper = new HelpersController;
        $this->_audit = new AuditTrailController;
    }

    public function index()
    {
        $user_accesses = $this->_helper->UserAccess();
        return view('admin.users',['user_accesses' => $user_accesses]);
    }

    public function user_list()
    {
        $users = DB::select("SELECT u.id as id,
                                   u.user_id as user_id,
                                   u.firstname as firstname,
                                   u.lastname as lastname,
                                   ifnull(u.email,'') as email,
                                   ut.description as user_type,
                                   u.div_code as div_code,
                                   DATE_FORMAT(u.created_at, '%Y/%m/%d %h:%i %p') as created_at
                            FROM users as u
                            INNER JOIN  admin_user_types as ut 
                            ON u.user_type =  ut.id
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
            'user_id' => 'required|string|max:50|min:1|unique:users',
            'firstname' => 'required|string|max:50|min:1',
            'lastname' => 'required|string|max:50|min:1',
            'password' => 'required|string|min:5|confirmed',
            'user_type' => 'required',
            // 'div_code' => 'required',
            'photo' => 'image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        $exists = User::where('user_id',$req->user_id)->first();
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
            $user->div_code = "";

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
                    'module' => 'User Master',
                    'action' => 'Inserted data User ID: '.$req->user_id,
                    'user' => Auth::user()->user_id
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
            $user->user_category = $this->user_category($req->user_type);
            // $user->div_code = $req->div_code;

            if (isset($req->is_admin)) {
                $user->is_admin = $req->is_admin;
                $this->give_admin_access($req->id);
            }  else {
                $user->is_admin = 0;
                AdminModuleAccess::where('user_id',$req->id)
                                ->where('user_category','Administrator')
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
                'module' => 'User Master',
                'action' => 'Edited data ID '.$req->id.', User ID: '.$req->user_id,
                'user' => Auth::user()->user_id
            ]);

            return response()->json($user);
        }else{
            return response()->json(['msg'=>"User ID already taken",'status' => 'failed']);
        }
    }

    public function destroy(Request $req)
    {
        if (Auth::user()->id == $req->id) {
            $data = [
                'msg' => "You cannot delete yourself.",
                'status' => "warning"
            ];
            return response()->json($data);
        } 
        $ExistingDivision = PpcDivision::where('user_id',$req->id)->first();
        if (isset($ExistingDivision)) {
            $data = [
                'msg' => "You cannot delete user that still existing on Division Master.",
                'status' => "warning"
            ];
            return response()->json($data);
        }
        $ExistingProductLine = AdminAssignProductionLine::where('user_id',$req->id)->first();
        if (isset($ExistingProductLine)) {
             $data = [
                'msg' => "You cannot delete user that still existing on Assign Production Line.",
                'status' => "warning"
            ];
            return response()->json($data);
        }
            $data = [
                'msg' => "User data was successfully deleted.",
                'status' => "success"
            ];

            $user = User::find($req->id);
            $user->delete();

        $this->_audit->insert([
            'user_type' => Auth::user()->user_type,
            'module' => 'User Master',
            'action' => 'Deleted data ID '.$req->id,
            'user' => Auth::user()->user_id
        ]);

        return response()->json($data);
    }

    // public function div_code(Request $req)
    // {
    //     $div = PpcDivision::select('div_code')
    //                         ->where('div_code', 'like', '%'.$req->data.'%')
    //                         ->groupBy('div_code')
    //                         ->get();
    //     return response()->json($div);
    // }

    public function user_modules(Request $req)
    {
        $modules;
        if ($req->id == '') {
            if ($this->user_category($req->user_type) == 'ALL') {
                $modules = AdminModule::where('user_category','<>','Administrator')->get();
            } else {
                $modules = AdminModule::where('user_category','<>','Administrator')->get();
                //$modules = AdminModule::where('user_category',$this->user_category($req->user_type))->get();
            }
            
        } else {
            if ($this->user_category($req->user_type) == 'ALL') {
                $modules = DB::table('admin_modules as mod')
                            ->leftJoin('admin_module_accesses as acc', function($join) use($req) {
                                $join->on('mod.code','=','acc.code')->where('acc.user_id',$req->id);
                            })
                            ->where('mod.user_category','<>','Administrator')
                            // ->where('acc.user_id',$req->id)
                            ->select(
                                DB::raw("mod.id as id"),
                                DB::raw("mod.code as code"),
                                DB::raw("mod.title as title"),
                                DB::raw("IFNULL(acc.access,0) as access")
                            )->get();
            } else {
                $modules = DB::table('admin_modules as mod')
                            ->leftJoin('admin_module_accesses as acc', function($join) use($req) {
                                $join->on('mod.code','=','acc.code')->where('acc.user_id',$req->id);
                            })
                            ->where('mod.user_category',$this->user_category($req->user_type))
                            // ->where('acc.user_id',$req->id)
                            ->select(
                                DB::raw("mod.id as id"),
                                DB::raw("mod.code as code"),
                                DB::raw("mod.title as title"),
                                DB::raw("IFNULL(acc.access,0) as access")
                            )->get();
            }
            // if (count($modules) < 1) {
            //     $modules = AdminModule::where('user_type',$req->user_type)->get();
            // }
        }
        return response()->json($modules);
    }

    private function give_user_access($id,$req)
    {
        AdminModuleAccess::where('user_id',$id)
                        ->where('user_category','<>','Administrator')
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
                    'create_user' => Auth::user()->user_id,
                    'update_user' => Auth::user()->user_id,
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
                        'create_user' => Auth::user()->user_id,
                        'update_user' => Auth::user()->user_id,
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
                            'create_user' => Auth::user()->user_id,
                            'update_user' => Auth::user()->user_id,
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
                'create_user' => Auth::user()->user_id,
                'update_user' => Auth::user()->user_id,
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
        $modules = AdminModule::where('user_category','Administrator')->get();
        $user_access = [];
        foreach ($modules as $key => $module) {
            array_push($user_access,[
                'code' => $module->code,
                'title' => $module->title,
                'category' => $module->category,
                'user_category' => $module->user_category,
                'user_id' => $id,
                'access' => 1,
                'create_user' => Auth::user()->user_id,
                'update_user' => Auth::user()->user_id,
            ]);
        }

        $allowAccess = array_chunk($user_access, 1000);
        AdminModuleAccess::where('user_id',$id)
                        ->where('user_category','Administrator')
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
            return $category->category;
        }
    }

    public function changeStringToIntUserType()
    {
        $ok = 0;
        $users = DB::table('users')->orderBy('id','desc')
                    ->select([
                        'id',
                        'user_type'
                    ])->get();

        foreach ($users as $key => $user) {
            $userUpd = User::find($user->id);

            switch ($user->user_type) {
                case 'SYSTEM ADMINISTRATOR':
                    $userUpd->user_type = 5;
                    break;
                
                case 'OPERATOR':
                    $userUpd->user_type = 4;
                    break;

                case 'MANAGER':
                    $userUpd->user_type = 3;
                    break;

                case 'LINE LEADER':
                    $userUpd->user_type = 2;
                    break;

                case 'PPC':
                    $userUpd->user_type = 1;
                    break;
                default:
                    # code...
                    break;
            }

            if ($userUpd->update()) {
                $ok++;
            }
            
        }

        if ($ok > 0) {
            return response()->json(['msg'=> "ok",'status' => 'success']);
        }

        return response()->json(['msg'=>"not ok",'status' => 'failed']);
    }
}
