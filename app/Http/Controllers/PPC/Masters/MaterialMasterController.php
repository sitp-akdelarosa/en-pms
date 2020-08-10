<?php

namespace App\Http\Controllers\PPC\Masters;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use App\PpcMaterialAssembly;
use App\PpcMaterialCode;
use App\PpcDropdownItem;
use App\PpcUpdateInventory;
use App\NotRegisteredMaterial;
use DataTables;
use DB;


class MaterialMasterController extends Controller
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
        return view('ppc.masters.material-master',['user_accesses' => $user_accesses]);
    }

    public function material_assembly_list()
    {   
        $assembly = DB::table('ppc_material_assemblies as pma')
                        ->leftjoin('admin_assign_production_lines as apl', 'apl.product_line', '=', 'pma.mat_type')
                        ->select([
                            'pma.id as id',
                            'pma.mat_type as mat_type',
                            'pma.character_num as character_num',
                            'pma.character_code as character_code',
                            'pma.description as description',
                            'pma.created_at as created_at'
                        ])
                        ->where('apl.user_id' ,Auth::user()->id)
                        ->orderBy('pma.id','desc');

        return DataTables::of($assembly)
                        ->editColumn('id', function($data) {
                            return $data->id;
                        })
                        ->addColumn('action', function($data) {
                            return '<button class="btn btn-sm bg-blue btn_edit_assembly" data-id="'.$data->id.'" data-mat_type="'.$data->mat_type.'" data-character_num="'.$data->character_num.'" data-character_code="'.$data->character_code.'" data-description="'.$data->description.'">
                                        <i class="fa fa-edit"></i>
                                    </button>';
                        })
                        ->make(true);
    }

    public function save_material_assembly(Request $req)
    {

        $this->validate($req, [
            'mat_type' => 'required',
            'character_num' => 'required',
            'character_code' => 'required|max:20',
            'description' => 'required|max:50',
        ]);

        $error = ['errors' => [
                'mat_type' => 'Material Type is already registered with same Character # and Code.',
                'character_num' => 'Character #  is already registered with same Material Type and Character code.',
                'character_code' => 'Character Code Line is already registered with same Character # and Material Type.']
        ];

        if (isset($req->assembly_id)) {
            $check = PpcMaterialAssembly::where('mat_type',$req->mat_type)
                                        ->where('character_num',$req->character_num)
                                        ->where('character_code',$req->character_code)
                                        ->where('id','!=',$req->assembly_id)
                                        ->count();
            if ($check > 0) {
                    return response()->json($error, 422);
            }

            $code_assembly = PpcMaterialAssembly::find($req->assembly_id);

            $code_assembly->mat_type = $req->mat_type;
            $code_assembly->character_num = $req->character_num;
            $code_assembly->character_code = strtoupper($req->character_code);
            $code_assembly->description = strtoupper($req->description);
            $code_assembly->update_user = Auth::user()->user_id;

            $code_assembly->update();

            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module' => 'Material Master',
                'action' => 'Edited data ID: '.$req->assembly_id.', 
                            Material Type: '.$req->mat_type.', 
                            Character Code: '.$req->character_code,
                'user' => Auth::user()->user_id
            ]);
        } else {
            $check = PpcMaterialAssembly::where('mat_type',$req->mat_type)
                                        ->where('character_num',$req->character_num)
                                        ->where('character_code',$req->character_code)
                                        ->count();
            if ($check > 0) {
                    return response()->json($error, 422);
            }
            $code_assembly = new PpcMaterialAssembly();

            $code_assembly->mat_type = $req->mat_type;
            $code_assembly->character_num = $req->character_num;
            $code_assembly->character_code = strtoupper($req->character_code);
            $code_assembly->description = strtoupper($req->description);
            $code_assembly->create_user = Auth::user()->user_id;
            $code_assembly->update_user = Auth::user()->user_id;

            $code_assembly->save();

            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module' => 'Material Master',
                'action' => 'Inserted data Material Type: '.$req->mat_type.', 
                            Character Code: '.$req->character_code,
                'user' => Auth::user()->user_id
            ]);
        }

        return response()->json($code_assembly);
    }

    public function destroy_material_assembly(Request $req)
    {
        $data = [
            'msg' => "Deleting failed",
            'status' => "warning"
        ];

        if (is_array($req->id)) {
            foreach ($req->id as $key => $id) {
                $code_assembly = PpcMaterialAssembly::find($id);
                $code_assembly->delete();

                $data = [
                    'msg' => "Data was successfully deleted.",
                    'status' => "success"
                ];
            }
        } else {
            $code_assembly = PpcMaterialAssembly::find($id);
            $code_assembly->delete();

            $data = [
                'msg' => "Data was successfully deleted.",
                'status' => "success"
            ];
        }

        $ids = implode(',', $req->id);

        $this->_audit->insert([
            'user_type' => Auth::user()->user_type,
            'module' => 'Material Master',
            'action' => 'Deleted data Material Assembly ID: '.$ids,
            'user' => Auth::user()->user_id
        ]);

        return response()->json($data);
    }

    public function show_dropdowns(Request $req)
    {
        $first = PpcMaterialAssembly::select('character_num','character_code','description')
                                    ->where('mat_type',$req->mat_type)
                                    ->where('character_num',1)
                                    ->get();

        $second = PpcMaterialAssembly::select('character_num','character_code','description')
                                    ->where('mat_type',$req->mat_type)
                                    ->where('character_num',2)
                                    ->get();

        $third = PpcMaterialAssembly::select('character_num','character_code','description')
                                    ->where('mat_type',$req->mat_type)
                                    ->where('character_num',3)
                                    ->get();

        $forth = PpcMaterialAssembly::select('character_num','character_code','description')
                                    ->where('mat_type',$req->mat_type)
                                    ->where('character_num',4)
                                    ->get();

        $fifth = PpcMaterialAssembly::select('character_num','character_code','description')
                                    ->where('mat_type',$req->mat_type)
                                    ->where('character_num',5)
                                    ->get();

        $seventh = PpcMaterialAssembly::select('character_num','character_code','description')
                                    ->where('mat_type',$req->mat_type)
                                    ->where('character_num',7)
                                    ->get();

        $eighth = PpcMaterialAssembly::select('character_num','character_code','description')
                                    ->where('mat_type',$req->mat_type)
                                    ->where('character_num',8)
                                    ->get();

        $ninth = PpcMaterialAssembly::select('character_num','character_code','description')
                                    ->where('mat_type',$req->mat_type)
                                    ->where('character_num',9)
                                    ->get();

        $eleventh = PpcMaterialAssembly::select('character_num','character_code','description')
                                    ->where('mat_type',$req->mat_type)
                                    ->where('character_num',11)
                                    ->get();

        $forteenth = PpcMaterialAssembly::select('character_num','character_code','description')
                                    ->where('mat_type',$req->mat_type)
                                    ->where('character_num',14)
                                    ->get();
        $data = [
            'first' => $first,
            'second' => $second,
            'third' => $third,
            'forth' => $forth,
            'fifth' => $fifth,
            'seventh' => $seventh,
            'eighth' => $eighth,
            'ninth' => $ninth,
            'eleventh' => $eleventh,
            'forteenth' => $forteenth,
        ];
        return response()->json($data);
    }

    public function mat_code_list()
    {        
        $mat = DB::table('ppc_material_codes as pmc')
                        ->leftjoin('admin_assign_production_lines as apl', 'apl.product_line', '=', 'pmc.material_type')
                        ->select([
                            'pmc.id as id',
                            'pmc.material_type as material_type',
                            'pmc.material_code as material_code',
                            'pmc.code_description as code_description',
                            'pmc.create_user as create_user',
                            'pmc.item as item',
                            'pmc.alloy as alloy',
                            'pmc.schedule as schedule',
                            'pmc.size as size',
                            'pmc.created_at as created_at'
                        ])
                        ->where('apl.user_id' ,Auth::user()->id)
                        ->orderBy('pmc.id','desc');

        return DataTables::of($mat)
                        ->editColumn('id', function($data) {
                            return $data->id;
                        })
                        ->editColumn('code_description', function($data) {
                            return $data->code_description;
                        })
                        ->addColumn('action', function($data) {
                            return "<button class='btn btn-sm bg-blue btn_edit_material' 
                                        data-id='".$data->id."' 
                                        data-material_type='".$data->material_type."' 
                                        data-material_code='".$data->material_code."' 
                                        data-code_description='".$data->code_description."'
                                        data-item='".$data->item."'
                                        data-alloy='".$data->alloy."'
                                        data-schedule='".$data->schedule."'
                                        data-size='".$data->size."'
                                        data-create_user='".$data->create_user."'
                                        data-created_at='".$data->created_at."'>
                                        <i class='fa fa-edit'></i>
                                    </button>";
                        })
                        ->make(true);
    }

    public function save_material_code(Request $req)
    {
        if (isset($req->material_id)) {
            $this->validate($req, [
                'material_code' => 'required|min:16|max:16',
                'material-type' => 'required',
                'code_description' => 'required',
                'item' => 'required',
                'alloy' => 'required',
                'size' => 'required'
            ]);

            $exists = PpcMaterialCode::where('material_code',$req->material_code)->where('id','!=', $req->material_id)->first();
            if (is_null($exists)) {

                $mat = PpcMaterialCode::find($req->material_id);
                $mat->material_type = strtoupper($req->material_type);
                $mat->material_code = strtoupper($req->material_code);
                $mat->code_description = strtoupper($req->code_description);
                $mat->item = strtoupper($req->item);
                $mat->alloy = strtoupper($req->alloy);
                $mat->schedule = strtoupper($req->schedule);
                $mat->size = strtoupper($req->size);
                $mat->update_user = Auth::user()->user_id;

                $mat->update();

                $check = NotRegisteredMaterial::where('materials_code',$req->material_code)
                                                ->count();

                if ($check > 0) {
                    NotRegisteredMaterial::where('materials_code',$req->material_code)
                                                ->delete();

                    PpcUpdateInventory::where('materials_code',$req->material_code)
                                        ->update([
                                            'description' => strtoupper($req->code_description),
                                            'item' => strtoupper($req->item),
                                            'alloy' => strtoupper($req->alloy),
                                            'schedule' => strtoupper($req->schedule),
                                            'size' => strtoupper($req->size),
                                            'update_user' => strtoupper($req->update_user),
                                            'updated_at' => date('Y-m-d H:i:S')
                                        ]);
                }

                $this->_audit->insert([
                    'user_type' => Auth::user()->user_type,
                    'module' => 'Material Master',
                    'action' => 'Edited data ID: '.$req->material_id.', 
                                Material Code: '.$mat->material_code,
                    'user' => Auth::user()->user_id
                ]);
            }else{
                return response()->json(['msg'=>"Material Code already taken",'status' => 'failed']);
            }

        } else {
            $this->validate($req, [
                'material_code' => 'required|min:16|max:16|unique:ppc_material_codes',
                'material-type' => 'required',
                'code_description' => 'required',
                'item' => 'required',
                'alloy' => 'required',
                'size' => 'required'
            ]);
            $mat = new PpcMaterialCode();

            $mat->material_type =  strtoupper($req->material_type);
            $mat->material_code = strtoupper($req->material_code);
            $mat->code_description = strtoupper($req->code_description);
            $mat->item = strtoupper($req->item);
            $mat->alloy = strtoupper($req->alloy);
            $mat->schedule = strtoupper($req->schedule);
            $mat->size = strtoupper($req->size);
            $mat->create_user = Auth::user()->user_id;
            $mat->update_user = Auth::user()->user_id;

            $mat->save();

            $check = NotRegisteredMaterial::where('materials_code',$req->material_code)
                                                ->count();

            if ($check > 0) {
                NotRegisteredMaterial::where('materials_code',$req->material_code)
                                            ->delete();

                PpcUpdateInventory::where('materials_code',$req->material_code)
                                    ->update([
                                        'description' => strtoupper($req->code_description),
                                        'item' => strtoupper($req->item),
                                        'alloy' => strtoupper($req->alloy),
                                        'schedule' => strtoupper($req->schedule),
                                        'size' => strtoupper($req->size),
                                        'update_user' => strtoupper($req->update_user),
                                        'updated_at' => date('Y-m-d H:i:S')
                                    ]);
            }

            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module' => 'Material Master',
                'action' => 'Inserted data Material Code: '.$req->material_code,
                'user' => Auth::user()->user_id
            ]);
        }
        return response()->json(['msg'=>"Data was successfully saved.",'status' => 'success']);
    }

    public function destroy_code(Request $req)
    {
        $data = [
            'msg' => "Deleting failed",
            'status' => "warning"
        ];

        if (is_array($req->id)) {
            foreach ($req->id as $key => $id) {
                $material = PpcMaterialCode::find($id);
                $material->delete();

                $data = [
                    'msg' => "Data was successfully deleted.",
                    'status' => "success"
                ];
            }
        } else {
            $material = PpcMaterialCode::find($id);
            $material->delete();

            $data = [
                'msg' => "Data was successfully deleted.",
                'status' => "success"
            ];
        }

        $ids = implode(',', $req->id);
        $this->_audit->insert([
            'user_type' => Auth::user()->user_type,
            'module' => 'Material Master',
            'action' => 'Deleted data Material Code ID: '.$ids,
            'user' => Auth::user()->user_id
        ]);

        return response()->json($data);
    }

    public function get_dropdown_material_type()
    {
         $process = DB::table('ppc_dropdown_items as pdt')
                    ->leftjoin('admin_assign_production_lines as apl', 'apl.product_line', '=', 'pdt.dropdown_item')
                    ->select([
                        'apl.product_line as product_line',
                    ])
                    ->where('pdt.dropdown_name_id', 9)
                    ->where('apl.user_id' , Auth::user()->id)
                    ->groupBy('apl.product_line')
                    ->get();
        return response()->json($process);
    }
}
