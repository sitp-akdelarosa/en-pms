<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use Illuminate\Support\Facades\Auth;
use App\AdminUserType;
use DataTables;

class UserTypeController extends Controller
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
        return view('admin.user-type',['user_accesses' => $user_accesses]);
    }

    public function list()
    {
        $type = AdminUserType::select([
                        'id',
                        'description',
                        'category'
                    ])->get();
        
        return response()->json($type);
        // return DataTables::of($type)
        //                 ->editColumn('id', function($data) {
        //                     return $data->id;
        //                 })
        //                 ->addColumn('action', function($data) {
        //                     return '<a class="btn btn-sm bg-blue btn_edit" data-id="'.$data->id.'" 
        //                                 data-description="'.$data->description.'"
        //                                 data-category="'.$data->category.'">
        //                                 <i class="fa fa-edit"></i>
        //                             </a>';
        //                 })
        //                 ->make(true);
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
                $type->update();

                $this->_audit->insert([
                    'user_type' => Auth::user()->user_type,
                    'module' => 'User Type',
                    'action' => 'Edited data ID: '.$req->id.', User Type: '.$type->description.' and Category: '.$type->category,
                    'user' => Auth::user()->user_id
                ]);
            }
        } else {

            $this->validate($req, [
                'description' => 'unique:admin_user_types'
            ]);

            $type = new AdminUserType();
            $type->description = strtoupper($req->description);
            $type->category = strtoupper($req->category);
            $type->save();

            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module' => 'User Type',
                'action' => 'Inserted data User Type: '.$req->description.' and Category: '.$type->category,
                'user' => Auth::user()->user_id
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
            'module' => 'User Type',
            'action' => 'Deleted data ID: '.$ids,
            'user' => Auth::user()->user_id
        ]);

        return response()->json($data);
    }
}
