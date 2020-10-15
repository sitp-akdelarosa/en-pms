<?php

namespace App\Http\Controllers\PPC\Masters;

use App\AdminAssignProductionLine;
use App\Http\Controllers\Admin\AuditTrailController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HelpersController;
use App\NotRegisteredProduct;
use App\PpcDivision;
use App\PpcMaterialCode;
use App\PpcProcess;
use App\PpcProcessSet;
use App\PpcProductCode;
use App\PpcProductCodeAssembly;
use App\PpcProductProcess;
use App\PpcUploadOrder;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductMasterController extends Controller
{
    protected $_helper;
    protected $_audit;
    protected $_moduleID;

    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
        $this->_helper = new HelpersController;
        $this->_audit = new AuditTrailController;

        $this->_moduleID = $this->_helper->moduleID('M0003');

    }

    public function index()
    {
        $user_accesses = $this->_helper->UserAccess();
        return view('ppc.masters.product-master', ['user_accesses' => $user_accesses]);
    }

    public function product_code_assembly_list()
    {
        $assembly = DB::table('ppc_product_code_assemblies as pca')
            ->leftjoin('admin_assign_production_lines as apl', 'apl.product_line', '=', 'pca.prod_type')
            ->select(
                'pca.id as id',
                'pca.prod_type as prod_type',
                'pca.character_num as character_num',
                'pca.character_code as character_code',
                'pca.description as description',
                'pca.created_at as created_at'
            )
            ->where('apl.user_id', Auth::user()->id)
            ->groupBy(
                'pca.id',
                'pca.prod_type',
                'pca.character_num',
                'pca.character_code',
                'pca.description',
                'pca.created_at'
            )
            ->orderBy('pca.id', 'desc')->get();
            


        return response()->json($assembly);
    }

    public function save_code_assembly(Request $req)
    {
        $this->validate($req, [
            'prod_type' => 'required',
            'character_num' => 'required|min:1|max:2',
            'character_code' => 'required|max:20',
            'description' => 'max:50',
        ]);

        $error = ['errors' => [
            'prod_type' => 'Product Line is already registered with same Character # and Code.',
            'character_num' => 'Character #  is already registered with same Product Line and Character code.',
            'character_code' => 'Character Code Line is already registered with same Character # and Product Line.'],
        ];

        if (isset($req->assembly_id)) {
            $check = PpcProductCodeAssembly::where('prod_type', $req->prod_type)
                ->where('character_num', $req->character_num)
                ->where('character_code', $req->character_code)
                ->where('id', '!=', $req->assembly_id)
                ->count();
            if ($check > 0) {
                return response()->json($error, 422);
            }

            $code_assembly = PpcProductCodeAssembly::find($req->assembly_id);

            $code_assembly->prod_type = $req->prod_type;
            $code_assembly->character_num = $req->character_num;
            $code_assembly->character_code = strtoupper($req->character_code);
            $code_assembly->description = strtoupper($req->description);
            $code_assembly->update_user = Auth::user()->id;

            $code_assembly->update();

            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module_id' => $this->_moduleID,
                'module' => 'Product Master',
                'action' => 'Edited data ID: ' . $req->assembly_id . ',
                            Product Type: ' . $req->prod_type . ',
                            Character Code: ' . $req->character_code,
                'user' => Auth::user()->id,
            ]);
        } else {
            $check = PpcProductCodeAssembly::where('prod_type', $req->prod_type)
                ->where('character_num', $req->character_num)
                ->where('character_code', $req->character_code)
                ->count();
            if ($check > 0) {
                return response()->json($error, 422);
            }

            $code_assembly = new PpcProductCodeAssembly();

            $code_assembly->prod_type = $req->prod_type;
            $code_assembly->character_num = $req->character_num;
            $code_assembly->character_code = strtoupper($req->character_code);
            $code_assembly->description = strtoupper($req->description);
            $code_assembly->create_user = Auth::user()->id;
            $code_assembly->update_user = Auth::user()->id;

            $code_assembly->save();

            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module_id' => $this->_moduleID,
                'module' => 'Product Master',
                'action' => 'Inserted data Product Type: ' . $req->prod_type . ',
                            Character Code: ' . $req->character_code,
                'user' => Auth::user()->id,
            ]);
        }

        return response()->json($code_assembly);
    }

    public function destroy_code_assembly(Request $req)
    {
        $data = [
            'msg' => "Deleting failed",
            'status' => "warning",
        ];

        if (is_array($req->id)) {
            foreach ($req->id as $key => $id) {
                $code_assembly = PpcProductCodeAssembly::find($id);
                $code_assembly->delete();

                $data = [
                    'msg' => "Data was successfully deleted.",
                    'status' => "success",
                ];
            }
        } else {
            $code_assembly = PpcProductCodeAssembly::find($req->id);
            $code_assembly->delete();

            $data = [
                'msg' => "Data was successfully deleted.",
                'status' => "success",
            ];
        }

        $ids = implode(',', $req->id);

        $this->_audit->insert([
            'user_type' => Auth::user()->user_type,
            'module_id' => $this->_moduleID,
            'module' => 'Product Master',
            'action' => 'Deleted data Product Code Assembly ID: ' . $ids,
            'user' => Auth::user()->id,
        ]);

        return response()->json($data);
    }

    public function product_type(Request $req)
    {
        $pt = PpcProductCodeAssembly::select('prod_type')
            ->where('prod_type', 'like', '%' . $req->data . '%')
            ->distinct()
            ->get();
        return response()->json($pt);
    }

    public function show_dropdowns(Request $req)
    {
        $first = PpcProductCodeAssembly::select('character_num', 'character_code', 'description')
            ->where('prod_type', $req->prod_type)
            ->where('character_num', 1)
            ->get();

        $second = PpcProductCodeAssembly::select('character_num', 'character_code', 'description')
            ->where('prod_type', $req->prod_type)
            ->where('character_num', 2)
            ->get();

        $third = PpcProductCodeAssembly::select('character_num', 'character_code', 'description')
            ->where('prod_type', $req->prod_type)
            ->where('character_num', 3)
            ->get();

        $forth = PpcProductCodeAssembly::select('character_num', 'character_code', 'description')
            ->where('prod_type', $req->prod_type)
            ->where('character_num', 4)
            ->get();

        $fifth = PpcProductCodeAssembly::select('character_num', 'character_code', 'description')
            ->where('prod_type', $req->prod_type)
            ->where('character_num', 5)
            ->get();

        $seventh = PpcProductCodeAssembly::select('character_num', 'character_code', 'description')
            ->where('prod_type', $req->prod_type)
            ->where('character_num', 7)
            ->get();

        $eighth = PpcProductCodeAssembly::select('character_num', 'character_code', 'description')
            ->where('prod_type', $req->prod_type)
            ->where('character_num', 8)
            ->get();

        $ninth = PpcProductCodeAssembly::select('character_num', 'character_code', 'description')
            ->where('prod_type', $req->prod_type)
            ->where('character_num', 9)
            ->get();

        $eleventh = PpcProductCodeAssembly::select('character_num', 'character_code', 'description')
            ->where('prod_type', $req->prod_type)
            ->where('character_num', 11)
            ->get();

        $forteenth = PpcProductCodeAssembly::select('character_num', 'character_code', 'description')
            ->where('prod_type', $req->prod_type)
            ->where('character_num', 14)
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

    public function process_div(Request $req)
    {
        $div = PpcDivision::where('process', $req->process)->get();
        return response()->json($div);
    }

    public function prod_process_list(Request $req)
    {
        $process = PpcProductProcess::where('prod_code', $req->prod_code)->get();
        return response()->json($process);
    }

    public function save_processes(Request $req)
    {
        $processes = [];
        $result = 0;

        $data = [
            'msg' => "Saving Failed.",
            'status' => 'warning',
        ];

        PpcProductProcess::where('prod_id', $req->prod_id)
                        // ->where('set',$req->sets[0])
                        ->delete();

        

        if (is_array($req->sequence)) {
            

            foreach ($req->sequence as $key => $seq) {
                

                array_push($processes, [
                    'prod_id' => $req->prod_id,
                    'prod_code' => strtoupper($req->prod_code),
                    'process' => strtoupper($req->process[$key]),
                    'set' => strtoupper($req->sets[$key]),
                    'sequence' => $seq,
                    'create_user' => Auth::user()->id,
                    'update_user' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $insert = array_chunk($processes, 1000);
            foreach ($insert as $params) {
                PpcProductProcess::insert($params);
                $result = 1;
            }

        }

        if ($result) {
            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module_id' => $this->_moduleID,
                'module' => 'Product Master',
                'action' => 'Assigned Process in Product Code: ' . $req->prod_code, //[0],
                'user' => Auth::user()->id,
            ]);
            $data = PpcProductProcess::where('prod_id', $req->prod_id)->get();
        }

        return response()->json($data);
    }

    public function prod_code_list()
    {
        $userid = Auth::user()->id;
        $product_codes = DB::table('ppc_product_codes as pc')
            ->leftjoin('admin_assign_production_lines as apl', 'apl.product_line', '=', 'pc.product_type')
            ->where('apl.user_id', $userid)->orderby('pc.id', 'desc')
            ->select(
                'pc.id as id',
                'pc.product_type as product_type',
                'pc.product_code as product_code',
                'pc.code_description as code_description',
                'pc.cut_weight as cut_weight',
                'pc.cut_weight_uom as cut_weight_uom',
                'pc.cut_length as cut_length',
                'pc.cut_length_uom as cut_length_uom',
                'pc.cut_width as cut_width',
                'pc.cut_width_uom as cut_width_uom',
                'pc.item as item',
                'pc.alloy as alloy',
                'pc.class as class',
                'pc.size as size',
                DB::raw('IFNULL(pc.standard_material_used, "") as standard_material_used'),
                DB::raw('IFNULL(pc.formula_classification, "") as formula_classification'),
                'pc.create_user as create_user',
                'pc.created_at as created_at'
            )->groupBy(
                'pc.id',
                'pc.product_type',
                'pc.product_code',
                'pc.code_description',
                'pc.cut_weight',
                'pc.cut_weight_uom',
                'pc.cut_length',
                'pc.cut_length_uom',
                'pc.cut_width',
                'pc.cut_width_uom',
                'pc.item',
                'pc.alloy',
                'pc.class',
                'pc.size',
                'pc.standard_material_used',
                'pc.formula_classification',
                'pc.create_user',
                'pc.created_at'
            )->get();
        return response()->json($product_codes);
    }

    public function save_product_code(Request $req)
    {
        if (isset($req->product_id)) {
            $this->validate($req, [
                'product_code' => 'required|min:16|max:16',
                'code_description' => 'required',
                'cut_weight' => 'numeric',
                'cut_length' => 'numeric',
            ]);
            $check = PpcProductCode::where('product_code', $req->product_code)->where('id', '!=', $req->product_id)->count();
            if ($check > 0) {
                return response()->json(['errors' => ['product_code' => 'The product code has already been taken.']], 422);
            }
            $prod = PpcProductCode::find($req->product_id);

            $prod->product_type = $req->product_type;
            $prod->product_code = strtoupper($req->product_code);
            $prod->code_description = strtoupper($req->code_description);
            $prod->cut_weight = $req->cut_weight;
            $prod->cut_weight_uom = strtoupper($req->cut_weight_uom);
            $prod->cut_length = $req->cut_length;
            $prod->cut_length_uom = strtoupper($req->cut_length_uom);
            $prod->cut_width = $req->cut_width;
            $prod->cut_width_uom = strtoupper($req->cut_width_uom);
            $prod->item = $req->item;
            $prod->class = $req->class;
            $prod->alloy = $req->alloy;
            $prod->size = $req->size;
            $prod->standard_material_used = strtoupper($req->standard_material_used);
            //$prod->formula_classification = strtoupper($req->formula_classification);
            $prod->update_user = Auth::user()->id;

            $prod->update();

            PpcProductProcess::where('prod_id', $req->product_id)
                ->update(['prod_code' => $req->product_code]);

            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module_id' => $this->_moduleID,
                'module' => 'Product Master',
                'action' => 'Edited data ID: ' . $req->product_id . ',
                            Product Code: ' . $prod->product_code,
                'user' => Auth::user()->id,
            ]);

        } else {
            $this->validate($req, [
                'product_code' => 'required|min:16|max:16|unique:ppc_product_codes',
                'code_description' => 'required',
                'cut_weight' => 'numeric',
                'cut_length' => 'numeric',
            ]);

            if ($this->checkIfExist($req->product_type, strtoupper($req->product_code))) {
                return $data = [
                    'msg' => 'Product code already exists.',
                    'status' => 'failed',
                ];
            }

            $prod = new PpcProductCode();

            $prod->product_type = $req->product_type;
            $prod->product_code = strtoupper($req->product_code);
            $prod->code_description = strtoupper($req->code_description);
            $prod->cut_weight = $req->cut_weight;
            $prod->cut_weight_uom = strtoupper($req->cut_weight_uom);
            $prod->cut_length = $req->cut_length;
            $prod->cut_length_uom = strtoupper($req->cut_length_uom);
            $prod->cut_width = $req->cut_width;
            $prod->cut_width_uom = strtoupper($req->cut_width_uom);
            $prod->item = $req->item;
            $prod->class = $req->class;
            $prod->alloy = $req->alloy;
            $prod->size = $req->size;
            $prod->standard_material_used = strtoupper($req->standard_material_used);
            //$prod->formula_classification = strtoupper($req->formula_classification);
            $prod->create_user = Auth::user()->id;
            $prod->update_user = Auth::user()->id;

            $prod->save();

            $check = NotRegisteredProduct::where('prod_code', $req->product_code)->count();

            if ($check > 0) {
                PpcUploadOrder::where('prod_code', $req->product_code)
                    ->update([
                        'description' => strtoupper($req->code_description),
                        'update_user' => Auth::user()->id,
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);
                NotRegisteredProduct::where('prod_code', $req->product_code)->delete();
            }

            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module_id' => $this->_moduleID,
                'module' => 'Product Master',
                'action' => 'Inserted data Product Code: ' . $req->product_code,
                'user' => Auth::user()->id,
            ]);
        }

        return response()->json($prod);
    }

    public function destroy_product_code(Request $req)
    {
        $data = [
            'msg' => "Deleting failed",
            'status' => "warning",
        ];

        if (is_array($req->id)) {
            foreach ($req->id as $key => $id) {
                $product = PpcProductCode::find($id);
                $product->delete();

                $data = [
                    'msg' => "Data was successfully deleted.",
                    'status' => "success",
                ];
            }
        } else {
            $product = PpcProductCode::find($id);
            $product->delete();

            $data = [
                'msg' => "Data was successfully deleted.",
                'status' => "success",
            ];
        }

        $ids = implode(',', $req->id);
        $this->_audit->insert([
            'user_type' => Auth::user()->user_type,
            'module_id' => $this->_moduleID,
            'module' => 'Product Master',
            'action' => 'Deleted data Product Code ID: ' . $ids,
            'user' => Auth::user()->id,
        ]);

        return response()->json($data);
    }

    public function destroy_process_code(Request $req)
    {
        $data = [
            'msg' => "Deleting failed",
            'status' => "warning",
        ];

        if (is_array($req->id)) {
            foreach ($req->id as $key => $id) {
                $process = PpcProductProcess::find($id);
                $process->delete();

                $data = [
                    'msg' => "Data was successfully deleted.",
                    'status' => "success",
                ];
            }
        } else {
            $process = PpcProductProcess::find($id);
            $process->delete();

            $data = [
                'msg' => "Data was successfully deleted.",
                'status' => "success",
            ];
        }

        $this->_audit->insert([
            'user_type' => Auth::user()->user_type,
            'module_id' => $this->_moduleID,
            'module' => 'Product Master',
            'action' => 'Deleted data Process ID: ' . $req->id,
            'user' => Auth::user()->id,
        ]);

        return response()->json($data);
    }

    public function get_set()
    {
        $set = PpcProcessSet::all();
        return response()->json($set);
    }

    public function checkIfExist($product_type, $product_code)
    {
        $check = PpcProductCode::where('product_type', $product_type)
            ->where('product_code', $product_code)
            ->count();

        if ($check > 0) {
            return true;
        }

        return false;
    }

    public function selected_process_list(Request $req)
    {
        $process = PpcProcess::where('set_id', $req->set_id)
                                    // ->where('prod_id', $req->prod_id)
                                    // ->where('create_user',Auth::user()->id)
                                    ->get();
        return response()->json($process);
    }

    public function get_dropdown_product()
    {
        $process = DB::table('ppc_dropdown_items as pdt')
            ->leftjoin('admin_assign_production_lines as apl', 'apl.product_line', '=', 'pdt.dropdown_item')
            ->select([
                'apl.product_line as product_line',
            ])
            ->where('pdt.dropdown_name_id', 7) // Product line
            ->where('apl.user_id', Auth::user()->id)
            ->groupBy('apl.product_line')
            ->get();

        return response()->json($process);
    }

    public function getStandardMaterial()
    {
        $plines = AdminAssignProductionLine::select('product_line')->where('user_id', Auth::user()->id)->get();
        $standard_material_code = PpcMaterialCode::select('code_description')->whereIn('material_type', $plines)->get();
        return response()->json($standard_material_code);
    }

    public function updateAllData()
    {
        $products = PpcProductCode::all(); //S/S BARSTOCK FITTING

        $arr = [];
        $eleventh = '';

        foreach ($products as $key => $prod) {
            $forth = substr($prod->product_code, 3, 1);
            $fifth = substr($prod->product_code, 4, 1);
            $seventh = substr($prod->product_code, 6, 1);
            $eighth = substr($prod->product_code, 7, 2);

            $check = DB::select("SELECT length(character_code),character_code
                                FROM ppc_product_code_assemblies
                                WHERE character_num = 11
                                AND length(character_code) = 6
                                AND prod_type = '" . $prod->product_type . "'
                                AND character_code = '" . substr($prod->product_code, 10, 6) . "'");

            if (count((array) $check) > 0) {
                $eleventh = substr($prod->product_code, 10, 6);
            } else {
                $eleventh = substr($prod->product_code, 10, 3);
            }

            $class = DB::table('ppc_product_code_assemblies')->where('prod_type', $prod->product_type)
                ->where('character_num', 4)
                ->where('character_code', $forth)
                ->select('description')
                ->first();

            $alloy = DB::table('ppc_product_code_assemblies')->where('prod_type', $prod->product_type)
                ->where('character_num', 5)
                ->where('character_code', $fifth)
                ->select('description')
                ->first();

            $item1 = DB::table('ppc_product_code_assemblies')->where('prod_type', $prod->product_type)
                ->where('character_num', 7)
                ->where('character_code', $seventh)
                ->select('description')
                ->first();

            $item2 = DB::table('ppc_product_code_assemblies')->where('prod_type', $prod->product_type)
                ->where('character_num', 8)
                ->where('character_code', $eighth)
                ->select('description')
                ->first();

            $size = DB::table('ppc_product_code_assemblies')->where('prod_type', $prod->product_type)
                ->where('character_num', 11)
                ->where('character_code', $eleventh)
                ->select('description')
                ->first();

            $item_val1 = (isset($item1->description)) ? $item1->description : '';
            $item_val2 = (isset($item2->description)) ? $item2->description : '';

            array_push($arr, [
                'product_code' => $prod->product_code,
                'item' => $item_val1 . ' ' . $item_val2,
                'alloy' => (isset($alloy->description)) ? $alloy->description : '',
                'class' => (isset($class->description)) ? $class->description : '',
                'size' => (isset($size->description)) ? $size->description : '',
            ]);

            DB::table('ppc_product_codes')->where('id', $prod->id)
                ->update([
                    'item' => $item_val1 . ' ' . $item_val2,
                    'alloy' => (isset($alloy->description)) ? $alloy->description : '',
                    'class' => (isset($class->description)) ? $class->description : '',
                    'size' => (isset($size->description)) ? $size->description : '',
                ]);
        }

        return dd($arr);
    }
}
