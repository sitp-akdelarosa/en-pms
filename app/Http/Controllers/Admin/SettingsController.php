<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use App\AdminSettingIso;
use DataTables;
use File;

class SettingsController extends Controller
{
    protected $_helper;
    protected $_audit;
    protected $_moduleID;

    public function __construct()
    {
        // $this->middleware('auth');
        $this->_helper = new HelpersController;
        $this->_audit = new AuditTrailController;

        $this->_moduleID = $this->_helper->moduleID('A0005');
    }

    public function index()
    {
        $user_accesses = $this->_helper->UserAccess();
        $permission_access = $this->_helper->check_permission('A0005');

        return view('admin.settings', [
            'user_accesses' => $user_accesses,
            'permission_access' => $permission_access
        ]);
    }

    public function getISOTable()
    {   
        $data = AdminSettingIso::all();
        return response()->json($data);
    }

    public function save(Request $req)
    {   
        

        if (isset($req->id)) {
            $this->validate($req, [ 
                'iso_name' => 'required|max:20',
                'iso_code' => 'required|max:50',
                'photo' => 'image|mimes:jpeg,png,jpg,gif,svg',
            ]);

            $exists = AdminSettingIso::where('iso_name',$req->iso_name)->where('id','<>', $req->id) ->first();
            if (is_null($exists)) {            
                $AdminSettingIso = AdminSettingIso::find($req->id);

                $AdminSettingIso->iso_name = strtoupper($req->iso_name);
                $AdminSettingIso->iso_code = strtoupper($req->iso_code);
                $AdminSettingIso->update_user = Auth::user()->id;

                $AdminSettingIso->update();

                if (isset($req->photo)) {
                    $this->uploadPhoto($req->id,$req->photo);
                }

                $this->_audit->insert([
                    'user_type' => Auth::user()->user_type,
                    'module_id' => $this->_moduleID,
                    'module' => 'Admin ISO Settings',
                    'action' => 'Editing ISO '.$req->iso_name,
                    'user' => Auth::user()->id,
					'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
                ]);
            } else {
                 $error = ['errors' => ['iso_name' => 'The ISO Name has already been taken.']];
                 return response()->json($error, 422);
            }
        } else {
            $this->validate($req, [ 
                'iso_name' => 'required|max:20|unique:admin_setting_isos',
                'iso_code' => 'required|max:50|unique:admin_setting_isos',
                'photo' => 'image|mimes:jpeg,png,jpg,gif,svg',
            ]);

            $AdminSettingIso = new AdminSettingIso();
            $AdminSettingIso->iso_name = strtoupper($req->iso_name);
            $AdminSettingIso->iso_code = strtoupper($req->iso_code);
            $AdminSettingIso->create_user = Auth::user()->id;
            $AdminSettingIso->update_user = Auth::user()->id;
            $AdminSettingIso->save();

            $this->uploadPhoto($AdminSettingIso->id,$req->photo);

            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module_id' => $this->_moduleID,
                'module' => 'Admin Setting',
                'action' => 'Inserting ID '.$req->iso_name,
                'user' => Auth::user()->id,
				'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
            ]);
        }

        return response()->json($AdminSettingIso);
    }

    public function uploadPhoto($id,$photo)
    {
        if (isset($photo)) {
            $whs_name = str_replace(' ', '_', $id);
            $dbPath = 'images/whs_logo/';
            $destinationPath = public_path($dbPath);
            $fileName = 'img_'.$whs_name.'.'.$photo->getClientOriginalExtension();

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }

            if (File::exists($destinationPath.'/'.$fileName)) {
                File::delete($destinationPath.'/'.$fileName);
            }

            $photo->move($destinationPath, $fileName);

            $AdminSettingIso = AdminSettingIso::find($id);
            $AdminSettingIso->photo = $dbPath.$fileName;
            $AdminSettingIso->update();
        }else{
            $AdminSettingIso = AdminSettingIso::find($id);
            $AdminSettingIso->photo = 'images/default_upload_photo.jpg';
            $AdminSettingIso->update();
        }
    }

    public function destroy(Request $req)
    {
        if (is_array($req->id)) {
            foreach ($req->id as $key => $id) {
                $set = AdminSettingIso::find($id);
                $set->delete();
            }
            AdminSettingIso::whereIn('id',$req->id)->delete();
        }else {
            $set = AdminSettingIso::find($req->id);
            $set->delete();
            AdminSettingIso::where('id',$req->id)->delete();
        }

        $ids = implode(',', $req->id);
        $this->_audit->insert([
            'user_type' => Auth::user()->user_type,
            'module_id' => $this->_moduleID,
            'module' => 'Admin Settings',
            'action' => 'Deleted data ID '.$ids,
            'user' => Auth::user()->id,
			'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
        ]);

        $data = [
                'msg' => "Data was successfully deleted.",
                'status' => "success"
        ];
        return response()->json($data);
    }

}
