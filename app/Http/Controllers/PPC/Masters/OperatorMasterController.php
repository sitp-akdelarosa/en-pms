<?php

namespace App\Http\Controllers\PPC\Masters;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use App\PpcOperator;
use DataTables;
use DB;

class OperatorMasterController extends Controller
{
    protected $_helper;
    protected $_audit;
    protected $_moduleID;

    public function __construct()
    {
        $this->middleware('auth');
        $this->_helper = new HelpersController;
        $this->_audit = new AuditTrailController;

        $this->_moduleID = $this->_helper->moduleID('M0006');
    }

    public function index()
    {
        $user_accesses = $this->_helper->UserAccess();
        return view('ppc.masters.operator-master',['user_accesses' => $user_accesses]);
    }

    public function save(Request $request)
    {   
        $this->validate($request, [ 
            'operator_id' => 'required|max:50',
            'firstname' => 'required|max:50',
            'lastname' => 'required|max:50'
        ]);

        // $count = PpcOperator::all()->count(); 
        // if ($count > 44) {
        //     return response()->json(['data' =>  $count,'msg'=>'The total number of the operator already exceeded','status' => 'failed']);
        // }
        
        if (isset($request->id)) {
            $exists = DB::table('ppc_operators')->where('operator_id',$request->operator_id)->where('id','!=', $request->id)->first();
        }else{
             $exists = DB::table('ppc_operators')->where('operator_id',$request->operator_id)->first();
        }

        if (is_null($exists)) {
            if (isset($request->id)) {               
                $PpcOperator = PpcOperator::find($request->id);
                $PpcOperator->operator_id =$request->operator_id;
                $PpcOperator->firstname = $request->firstname;
                $PpcOperator->lastname =$request->lastname;
                $PpcOperator->update_user = Auth::user()->id;
                $PpcOperator->update();

                $this->_audit->insert([
                    'user_type' => Auth::user()->user_type,
                    'module_id' => $this->_moduleID,
                    'module' => 'Operator Master',
                    'action' => 'Editing ID '.$request->operator_id,
                    'user' => Auth::user()->id,
					'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
                ]);
            }else{
                $PpcOperator = new PpcOperator();
                $PpcOperator->operator_id =$request->operator_id;
                $PpcOperator->firstname = $request->firstname;
                $PpcOperator->lastname =$request->lastname;
                $PpcOperator->create_user = Auth::user()->id;
                $PpcOperator->update_user = Auth::user()->id;
                $PpcOperator->save();

                $this->_audit->insert([
                    'user_type' => Auth::user()->user_type,
                    'module_id' => $this->_moduleID,
                    'module' => 'Operator Master',
                    'action' => 'Inserting ID '.$request->operator_id,
                    'user' => Auth::user()->id,
					'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
                ]);
            }

                return response()->json([
                    'data' =>  $PpcOperator,
                    'msg' => 'Operator Master was successfully saved.',
                    'status' => 'success'
                ]);
        }else{
                $PpcOperator = PpcOperator::all();
                return response()->json(['data' =>  $PpcOperator,'msg'=>"Operation ID already taken",'status' => 'failed']);
            }

        return response()->json($PpcOperator);
    }

    public function Operators()
    {   
        $data = PpcOperator::all(); 
        return DataTables::of($data)
                        ->editColumn('id', function($data) {
                            return $data->id;
                        })
                        ->addColumn('action', function($data) {
                            return '<button class="btn btn-sm bg-blue btn_edit permission-button" 
                            data-id="'.$data->id.'"
                            data-operator_id="'.$data->operator_id.'"
                            data-firstname="'.$data->firstname.'"
                            data-lastname="'.$data->lastname.'"
                            data-disabled="'.$data->disabled.'"
                            ><i class="fa fa-edit"></i></button>';
                        })
        ->make(true);
    }

    public function destroy(Request $req)
    {
        if (is_array($req->id)) {
            foreach ($req->id as $key => $id) {
                $set = PpcOperator::find($id);
                $set->delete();

                $data = [
                    'msg' => "Data was successfully deleted.",
                    'status' => "success"
                ];
            }
            PpcOperator::whereIn('id',$req->id)->delete();
        }else {
            $set = PpcOperator::find($req->id);
            $set->delete();

            PpcOperator::where('id',$req->id)->delete();

            $data = [
                'msg' => "Data was successfully deleted.",
                'status' => "success"
            ];
        }

        $ids = implode(',', $req->id);

        $this->_audit->insert([
            'user_type' => Auth::user()->user_type,
            'module_id' => $this->_moduleID,
            'module' => 'Operator Master',
            'action' => 'Deleted data ID '.$ids,
            'user' => Auth::user()->id,
            'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
        ]);

        return response()->json($data);
    }
}
