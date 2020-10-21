<?php

namespace App\Http\Controllers\PPC\Masters;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use App\PpcProcess;
use App\PpcProcessSet;
use App\PpcDropdownItem;
use DB;

class ProcessMasterController extends Controller
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

        $this->_moduleID = $this->_helper->moduleID('M0005');
    }

    public function index()
    {
        $user_accesses = $this->_helper->UserAccess();
        return view('ppc.masters.process-master',['user_accesses' => $user_accesses]);
    }

    public function process_list()
    {
        $process = PpcDropdownItem::where('dropdown_name_id',1)
                        ->select( DB::raw('dropdown_item as process'))
                        ->orderby('dropdown_item','ASC')
                        ->groupBy('dropdown_item')
                        ->get();
        return response()->json($process);
    }

    public function selected_process_list(Request $req)
    {
        $process = PpcProcess::where('set_id',$req->set_id)
                            ->get();
        return response()->json($process);
    }

    public function save(Request $req)
    {
        $data = [
                'msg' => 'Saving failed.',
                'status' => 'failed'
            ];

        if (isset($req->processes)) {
            // $req->set_id is a array
            PpcProcess::whereIn('set_id',$req->set_id)->delete();

            $saved = false;

            foreach ($req->processes as $key => $proc) {
                foreach ($req->set_id as $key => $set_id) {
                    $setname = PpcProcessSet::select('set')->where('id',$set_id)->first();
                    PpcProcess::create([
                        'set_id' => $set_id,
                        'set' => strtoupper($setname->set),
                        'sequence' => $proc['sequence'],
                        'process' => strtoupper($proc['process']),
                        'create_user' => Auth::user()->id,
                        'update_user' => Auth::user()->id
                    ]);
                }
                $saved = true;
            }

            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module_id' => $this->_moduleID,
                'module' => 'Process Master',
                'action' => 'Saved data Process set: '.$req->sets,
                'user' => Auth::user()->id
            ]);


            if ($saved == true) {
                $data = [
                    'msg' => 'Successfully saved.',
                    'status' => 'success'
                ];
            }

            return $data;
        } else {
            $data = [
                'msg' => 'Please add your processes to your desired set.',
                'status' => 'failed'
            ];
        }

        return $data;
    }

    public function save_set(Request $req)
    {
        $data = [
            'msg' => 'Saving failed',
            'status' => 'failed'
        ];

        $saved = false;

        if (isset($req->set_id)) {
            $this->validate($req, [
                'set' => 'required',
            ]);

            $set = PpcProcessSet::find($req->set_id);

            $set->set = strtoupper($req->set);
            $set->update_user = Auth::user()->id;
            $set->update();

            $saved = true;
        } else {
            $this->validate($req, [
                'set' => 'required|unique:ppc_process_sets|max:50',
            ]);

            $set = new PpcProcessSet();

            $set->set = strtoupper($req->set);
            $set->create_user = Auth::user()->id;
            $set->update_user = Auth::user()->id;
            $set->save();

            $saved = true;
        }

        if ($saved == true) {
            $data = [
                'msg' => 'Successfully saved',
                'status' => 'success'
            ];
        }

        return $data;
    }

    public function get_set()
    {
        $set = PpcProcessSet::select(
                                DB::raw('id as id'),
                                DB::raw('`set` as `text`')
                            )
                            ->where('create_user',Auth::user()->id)
                            ->get();

        return response()->json($set);
    }

    public function destroy_set(Request $req)
    {
        $data = [
            'msg' => "Deleting failed",
            'status' => "warning"
        ];

        if (is_array($req->id)) {
            foreach ($req->id as $key => $id) {
                $set = PpcProcessSet::find($id);
                $set->delete();

                $data = [
                    'msg' => "Data was successfully deleted.",
                    'status' => "success"
                ];
            }
            PpcProcess::whereIn('set_id',$req->id)->delete();
        } else {
            $set = PpcProcessSet::find($req->id);
            $set->delete();

            PpcProcess::where('set_id',$req->id)->delete();

            $data = [
                'msg' => "Data was successfully deleted.",
                'status' => "success"
            ];
        }

        $ids = implode(',', $req->id);

        $this->_audit->insert([
            'user_type' => Auth::user()->user_type,
            'module_id' => $this->_moduleID,
            'module' => 'Process Master',
            'action' => 'Deleted data Process Set ID: '.$ids,
            'user' => Auth::user()->id
        ]);

        return response()->json($data);
    }
}
