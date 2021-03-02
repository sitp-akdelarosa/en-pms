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
    protected $_moduleID;

    public function __construct()
    {
        // $this->middleware('ajax-session-expired');
        // $this->middleware('auth');
        $this->_helper = new HelpersController;
        $this->_audit = new AuditTrailController;

        $this->_moduleID = $this->_helper->moduleID('M0002');
    }

    public function index()
    {
        $user_accesses = $this->_helper->UserAccess();
        $permission_access = $this->_helper->check_permission('M0002');

        return view('ppc.masters.dropdown-master', [
            'user_accesses' => $user_accesses,
            'permission_access' => $permission_access
        ]);
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
                        'create_user' => Auth::user()->id,
                        'update_user' => Auth::user()->id,
                    ]);
                }
            }

            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module_id' => $this->_moduleID,
                'module' => 'Dropdown Master',
                'action' => 'Edited items of : '.$req->selected_dropdown_name,
                'user' => Auth::user()->id,
                'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
            ]);

        } else {
            foreach ($req->dropdown_item as $key => $dropdown_item) {
                PpcDropdownItem::create([
                    'dropdown_name_id' => $req->selected_dropdown_name_id,
                    'dropdown_name' => strtoupper($req->selected_dropdown_name),
                    'dropdown_item' => strtoupper($dropdown_item),
                    'create_user' => Auth::user()->id,
                    'update_user' => Auth::user()->id,
                ]);
            }

            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module_id' => $this->_moduleID,
                'module' => 'Dropdown Master',
                'action' => 'Inserted items of : '.$req->selected_dropdown_name,
                'user' => Auth::user()->id,
                'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
            ]);
        }

        $items = PpcDropdownItem::all();

        return response()->json($items);
    }
}

