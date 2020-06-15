<?php

namespace App\Http\Controllers\PPC\Masters;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use DataTables;
use App\PpcDropdownName;
use App\PpcDropdownItem;
use DB;

class DropdownMasterController extends Controller
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
        return view('ppc.masters.dropdown-master',['user_accesses' => $user_accesses]);
    }

    public function dropdown_names()
    {
        $dropdown = PpcDropdownName::all();
        return response()->json($dropdown);
    }

    public function dropdown_list()
    {
        $dropdown = PpcDropdownName::select([
                        'id',
                        'dropdown_name',
                    ]);
        return DataTables::of($dropdown)
                        ->editColumn('id', function($data) {
                            return $data->id;
                        })
                        ->addColumn('action', function($data) {
                            return "<button class='btn btn-sm bg-blue btn_edit_dropdown_name' 
                            data-id='".$data->id."'
                            data-dropdown_name='".$data->dropdown_name."'>
                                        <i class='fa fa-edit'></i>
                                    </button>";
                        })
                        ->make(true);
    }

    public function get_items(Request $req)
    {
        $items = PpcDropdownItem::where('dropdown_name_id',$req->id)->get();
        return response()->json($items);
    }

    public function save_dropdown_items(Request $req)
    {
        if (isset($req->selected_dropdown_name_id)) {
            PpcDropdownItem::where('dropdown_name_id',$req->selected_dropdown_name_id)->delete();
            if(is_array($req->dropdown_item)){
                foreach ($req->dropdown_item as $key => $dropdown_item) {
                    PpcDropdownItem::create([
                        'dropdown_name_id' => $req->selected_dropdown_name_id,
                        'dropdown_name' => strtoupper($req->selected_dropdown_name),
                        'dropdown_item' => strtoupper($dropdown_item),
                        'create_user' => Auth::user()->user_id,
                        'update_user' => Auth::user()->user_id,
                    ]);
                }
            }

            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module' => 'Dropdown Master',
                'action' => 'Edited items of : '.$req->selected_dropdown_name,
                'user' => Auth::user()->user_id
            ]);

        } else {
            foreach ($req->dropdown_item as $key => $dropdown_item) {
                PpcDropdownItem::create([
                    'dropdown_name_id' => $req->selected_dropdown_name_id,
                    'dropdown_name' => strtoupper($req->selected_dropdown_name),
                    'dropdown_item' => strtoupper($dropdown_item),
                    'create_user' => Auth::user()->user_id,
                    'update_user' => Auth::user()->user_id,
                ]);
            }

            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module' => 'Dropdown Master',
                'action' => 'Inserted items of : '.$req->selected_dropdown_name,
                'user' => Auth::user()->user_id
            ]);
        }

        $items = PpcDropdownItem::all();

        return response()->json($items);
    }

    // public function save_dropdown_name(Request $req)
    // {
    //     if (isset($req->dropdown_name_id)) {
    //         $this->validate($req, [
    //             'dropdown_name' => 'required|unique:ppc_dropdown_names|max:80',
    //         ]);
    //         $names = PpcDropdownName::find($req->dropdown_name_id);
    //         $names->dropdown_name = strtoupper($req->dropdown_name);
    //         $names->update_user = Auth::user()->user_id;
    //         $names->update();

    //         PpcDropdownItem::where('dropdown_name_id', $names->id)
    //                     ->update(['dropdown_name' => strtoupper($req->dropdown_name)]);

    //         $this->_audit->insert([
    //             'user_type' => Auth::user()->user_type,
    //             'module' => 'Dropdown Master',
    //             'action' => 'Edited data ID: '.$req->dropdown_name_id.', Dropdown Name: '.$names->dropdown_name,
    //             'user' => Auth::user()->user_id
    //         ]);
    //     } else {
    //         $this->validate($req, [
    //             'dropdown_name' => 'required|unique:ppc_dropdown_names|max:80',
    //         ]);
    //         $names = new PpcDropdownName();
    //         $names->dropdown_name = strtoupper($req->dropdown_name);
    //         $names->create_user = Auth::user()->user_id;
    //         $names->update_user = Auth::user()->user_id;
    //         $names->save();

    //         $this->_audit->insert([
    //             'user_type' => Auth::user()->user_type,
    //             'module' => 'Dropdown Master',
    //             'action' => 'Inserted data Dropdown Name: '.$req->dropdown_name,
    //             'user' => Auth::user()->user_id
    //         ]);
    //     }

    //     return response()->json($names);
    // }

    // public function destroy_dropdown_name(Request $req)
    // {
    //     $data = [
    //         'msg' => "Deleting failed",
    //         'status' => "warning"
    //     ];

    //     if (is_array($req->id)) {
    //         foreach ($req->id as $key => $id) {
    //             $name = PpcDropdownName::find($id);
    //             $items = $name->items()->count();
    //             if ($items > 0) {
    //                 $data = [
    //                     'msg' => "Data has items/options.",
    //                     'status' => "warning"
    //                 ];
    //             } else {
    //                 $name->delete();
    //                 $data = [
    //                     'msg' => "Data was successfully deleted.",
    //                     'status' => "success"
    //                 ];
    //             }
    //         }
    //     } else {
    //         $name = PpcDropdownName::find($req->id);
    //         $items = $name->items()->count();
    //         if ($items > 0) {
    //             $data = [
    //                 'msg' => "Data has items/options.",
    //                 'status' => "warning"
    //             ];
    //         } else {
    //             $name->delete();
    //             $data = [
    //                 'msg' => "Data was successfully deleted.",
    //                 'status' => "success"
    //             ];
    //         }
    //     }

    //     $ids = implode(',', $req->id);

    //     $this->_audit->insert([
    //         'user_type' => Auth::user()->user_type,
    //         'module' => 'Dropdown Master',
    //         'action' => 'Deleted data Dropdown Name ID: '.$req->id,
    //         'user' => Auth::user()->user_id
    //     ]);

    //     return response()->json($data);
    // }

    // public function destroy_dropdown_items(Request $req)
    // {
    //     $data = [
    //         'msg' => "Deleting failed",
    //         'status' => "warning"
    //     ];

    //     if (is_array($req->id)) {
    //         foreach ($req->id as $key => $id) {
    //             $item = PpcDropdownItem::find($id);
    //             $item->delete();

    //             $data = [
    //                 'msg' => "Data was successfully deleted.",
    //                 'status' => "success"
    //             ];
    //         }
    //     } else {
    //         $item = PpcDropdownItem::find($req->id);
    //         $item->delete();

    //         $data = [
    //             'msg' => "Data was successfully deleted.",
    //             'status' => "success"
    //         ];
    //     }

    //     $ids = implode(',', $req->id);

    //     $this->_audit->insert([
    //         'user_type' => Auth::user()->user_type,
    //         'module' => 'Dropdown Master',
    //         'action' => 'Deleted data Dropdown Item ID: '.$ids,
    //         'user' => Auth::user()->user_id
    //     ]);

    //     return response()->json($data);
    // }

    // public function check_item_if_exist(Request $req)
    // {
    //     $data = [
    //         'msg' => '',
    //         'status' => '',
    //         'value' => 0
    //     ];

    //     $check = PpcDropdownItem::where('dropdown_name_id',$req->selected_dropdown_name_id)
    //                             ->where('dropdown_item',strtoupper($req->item))
    //                             ->count();
    //     if ($check > 0) {
    //         $data = [
    //             'msg' => 'Item already exists.',
    //             'status' => 'failed',
    //             'value' => 1
    //         ];
    //     }

    //     return $data;
    // }
}

