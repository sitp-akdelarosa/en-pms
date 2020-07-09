<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use DataTables;
use App\AdminAssignProductionLine;
use App\User;
use App\PpcDropdownItem;
use DB;

class AssignProductionLineController extends Controller
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
        return view('admin.assign-production-line',['user_accesses' => $user_accesses]);
    }

    public function productline_list(Request $req)
    {
        $prodline = DB::table('admin_assign_production_lines as a')
                        ->join('users as u','a.user_id','=','u.id')
                        ->whereIn('a.user_id',$req->user_id)
                        ->select(
                            'a.id',
                            'a.user_id',
                            'a.product_line',
                            DB::raw("DATE_FORMAT(a.updated_at, '%m/%d/%Y %h:%i %p') as updated_at"),
                            'u.firstname',
                            'u.lastname'
                        )
                        ->groupBy(
                            'a.id',
                            'a.user_id',
                            'a.product_line',
                            DB::raw("DATE_FORMAT(a.updated_at, '%m/%d/%Y %h:%i %p')"),
                            'u.firstname',
                            'u.lastname')
                        ->orderby('id','desc')->get();

        return response()->json($prodline);
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
            AdminAssignProductionLine::where('user_id',$user_id)->delete();

            foreach ($req->product_line as $key => $product_line) {
                
                if ($product_line !== '' || !empty($product_line)) {
                    array_push($params, [
                        'user_id' => $user_id,
                        'product_line' => $product_line,
                        'create_user' => Auth::user()->user_id,
                        'update_user' => Auth::user()->user_id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        $insert = array_chunk($params, 1000);

        foreach ($insert as $batch) {
            AdminAssignProductionLine::insert($batch);

            $data = [
                'msg' => "Successfully saved.",
                'status' => "success"
            ];
        }

        $this->_audit->insert([
            'user_type' => Auth::user()->user_type,
            'module' => 'Assign Production Line',
            'action' => 'Edited data user ID '.$user_id.', Production Line: '.implode(',', $req->product_line),
            'user' => Auth::user()->user_id
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
                $prod = AdminAssignProductionLine::find($id);
                $prod->delete();

                $data = [
                    'msg' => "Data was successfully deleted.",
                    'status' => "success"
                ];
            }
        } else {
            $prod = AdminAssignProductionLine::find($id);
            $prod->delete();

            $data = [
                'msg' => "Data was successfully deleted.",
                'status' => "success"
            ];
        }

        $ids = implode(',', $req->id);

        $this->_audit->insert([
            'user_type' => Auth::user()->user_type,
            'module' => 'Assign Production Line',
            'action' => 'Deleted data ID '.$ids,
            'user' => Auth::user()->user_id
        ]);

        return response()->json($data);
    }

    public function users()
    {
        $users = User::whereIn('user_category',['OFFICE','ALL'])->get();
        return response()->json($users);
    }

    public function getDropdownItemByID()
    {
        $items = PpcDropdownItem::whereIn('dropdown_name_id',[8,9])
                                ->select('dropdown_item')
                                ->groupBy('dropdown_item')
                                ->orderby('dropdown_item','ASC')->get();
        return response()->json($items);
    }

}
