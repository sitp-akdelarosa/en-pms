<?php

namespace App\Http\Controllers\PPC\Transaction;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use Illuminate\Support\Str;
use App\PpcUpdateInventory;
use App\PpcMaterialCode;
use App\PpcDropdownItem;
use App\NotRegisteredMaterial;
use App\PpcMaterialAssembly;
use App\Inventory;
use Excel;
use DB;
use DataTables;
use DateTime;

class UpdateInventoryController extends Controller
{
    protected $_helper;
    protected $_audit;
    protected $_moduleID;

    public function __construct()
    {
        $this->middleware('auth');
        $this->_helper = new HelpersController;
        $this->_audit = new AuditTrailController;

        $this->_moduleID = $this->_helper->moduleID('T0001');
    }

    public function index()
    {
        $user_accesses = $this->_helper->UserAccess();
        return view('ppc.transaction.update-inventory',['user_accesses' => $user_accesses]);
    }
  
    public function CheckFile(Request $req)
    {
        $file = $req->file('file_inventory');
        $fields;
        
        Excel::load($file, function($reader) use(&$fields){
            $fields = $reader->toArray();
        });

        $data = [
                'status' => 'success' 
                ];
        $heatnumber = [];
        $line =1;
        $failed = 0;

        $msg = [];
        $sameItem = [];

        foreach ($fields as $key => $field) {
            $line++;
            if($this->checkExcessSpace($field)) {

            } else {
                if (empty($field['receiving_no']) && is_null($field['receiving_no'])) {
                    $failed++;
                    array_push($msg, 'Please provide Receiving No. for Line '.$line.'.');
                }

                if (empty($field['materialscode']) && is_null($field['materialscode'])) {
                    $failed++;
                    array_push($msg, 'Please provide Material Code for Line '.$line.'.');
                }

                if (empty($field['qty_weight']) && is_null($field['qty_weight'])) {
                    $failed++;
                    array_push($msg, 'Please provide Quantity Weight for Line '.$line.'.');
                }

                if (empty($field['qty_pcs']) && is_null($field['qty_pcs'])) {
                    $failed++;
                    array_push($msg, 'Please provide Quantity PCS for Line '.$line.'.');
                }

                // if (empty($field['uom']) && is_null($field['uom'])) {
                //     $failed++;
                //     array_push($msg, 'Please provide Unit of Measurement(UoM) for Line '.$line.'.');
                // }

                if (empty($field['heatnumber']) && is_null($field['heatnumber'])) {
                    $failed++;
                    array_push($msg, 'Please provide Heat Number for Line '.$line.'.');
                }

                if (empty($field['receiveddate']) && is_null($field['receiveddate'])) {
                    $failed++;
                    array_push($msg, 'Please provide Received Date for Line '.$line.'.');
                }

                if ($failed == 0) {
                    if ((!empty($field['receiveddate']) && !is_null($field['receiveddate'])) && DateTime::createFromFormat('Y-m-d G:i:s', $field['receiveddate']) === FALSE) {
                        $data = ['status' => 'heatnumber error','msg' => 'The '.$field['receiveddate'].' format of receive date is wrong', 'excel' => $fields];
                        return response()->json($data);
                    }

                    // if ((!empty($field['heatnumber']) && !is_null($field['heatnumber'])) && in_array($field['heatnumber'], $heatnumber)) {
                    //     $data = ['status' => 'heatnumber error','msg' => 'The '.$field['heatnumber'].' Heat Number is same in Excel File'];
                    //     return response()->json($data);
                    // }
                    $heatnumber[] =  $field['heatnumber'];

                    $weight = $field['qty_weight'];

                    if(filter_var($weight, FILTER_VALIDATE_FLOAT) === false) {
                        $data = ['status' => 'not num'];
                        return response()->json($data);
                    }

                    $pcs = $field['qty_pcs'];

                    if(filter_var($pcs, FILTER_VALIDATE_INT) === false) {
                        $data = ['status' => 'not num'];
                        return response()->json($data);
                    }

                    $length = (isset($field['length']))? $field['length'] : 'N/A';

                    $received_items = DB::table('ppc_update_inventories')
                                        ->Where('materials_code',$field['materialscode'])
                                        ->where('heat_no',$field['heatnumber'])
                                        ->where('length', $length)
                                        ->where('qty_weight', $field['qty_weight'])
                                        ->where('qty_pcs', $field['qty_pcs'])
                                        ->where('receiving_no', $field['receiving_no'])
                                        ->where('create_user',Auth::user()->id)
                                        ->first();

                    if ($this->_helper->check_if_exists($received_items) > 0) {
                        array_push($sameItem,$received_items);
                    }
                }
                
            }
                

            // $check = PpcUpdateInventory::where('heat_no',$field['heatnumber'])
            //                             ->where('materials_code','!=',$field['materialscode'])
            //                             ->count();
            // if ($check > 0) {
            //     $data = ['status' => 'heatnumber error','msg' => 'The '.$field['heatnumber'].' Heat Number is already in the Upload Inventory'];
            //     return response()->json($data);
            // }

        }

        if ($failed > 0) {
            $message = '';
            foreach ($msg as $key => $m) {
                $message.= $m."\r\n";
            }

            $data = [
                'status' => 'failed',
                'msg' => $message
            ];
        }

        if (count($sameItem) > 0) {
            $data = [
                'status' => 'same_items',
                'msg' => 'These items were already uploaded.',
                'same_items' => $sameItem
            ];
        }

        // if($failed == 0){
              
        // } else {
        //     $data = [
        //         'status' => 'failed',
        //         'fields' => $fields,
        //     ];
        // }         
        return response()->json($data);
    }

    private function checkExcessSpace($field) 
    {
        $error = false;
        if ((empty($field['materialscode']) && is_null($field['materialscode'])) &&
            (empty($field['qty_weight']) && is_null($field['qty_weight'])) &&
            (empty($field['qty_pcs']) && is_null($field['qty_pcs'])) && 
            (empty($field['heatnumber']) && is_null($field['heatnumber'])) && 
            (empty($field['receiveddate']) && is_null($field['receiveddate'])) &&
            (empty($field['invoiceno']) && is_null($field['invoiceno'])) &&
            (empty($field['supplier']) && is_null($field['supplier'])) &&
            (empty($field['receiving_no']) && is_null($field['receiving_no']))
        ) {
            $error = true;
        }

        return $error;
    }

    public function UploadInventory(Request $req)
    {
        $file = $req->file('file_inventory');
        $fields;
        
        Excel::load($file, function($reader) use(&$fields){
            $fields = $reader->toArray();
        });
        $materialeArr = [];
        $countAdded = 0;
        foreach ($fields as $key => $field) {
            if((!empty($field['materialscode']) && !is_null($field['materialscode'])) || 
                (!empty($field['qty_weight']) && !is_null($field['qty_weight'])) || 
                (!empty($field['qty_pcs']) && !is_null($field['qty_pcs'])) ||
                (!empty($field['heatnumber']) && !is_null($field['heatnumber'])) ||
                (!empty($field['receiveddate']) && !is_null($field['receiveddate'])) ||
                (!empty($field['receiving_no']) && !is_null($field['receiving_no'])) ||
                (!empty($field['invoiceno']) && !is_null($field['invoiceno'])) ||
                (!empty($field['supplier']) && !is_null($field['supplier'])) ) {

                //$uom = preg_replace('/[0-9]+/', '', strtoupper($field['uom']));

                $checkMatCode = PpcMaterialCode::where('material_code',$field['materialscode'])->count();  
                if ($checkMatCode == 0) {

                    $mat =  DB::table('ppc_dropdown_items')
                                ->select([
                                    'dropdown_item as material_type',
                                ])
                                ->where([
                                    ['dropdown_name_id', 8],
                                    ['dropdown_item', strtoupper($field['materialstype'])]
                                ]) 
                                ->first();

                    NotRegisteredMaterial::where('materials_code',$field['materialscode'])
                                        ->where('heat_no',$field['heatnumber'])
                                        ->delete();

                    NotRegisteredMaterial::insert([
                        'receiving_no' => strtoupper($field['receiving_no']),
                        'materials_type' => (isset($mat->mat_type))? $mat->mat_type: strtoupper($field['materialstype']),
                        'materials_code' => strtoupper($field['materialscode']),
                        'qty_weight' => $field['qty_weight'],
                        'qty_pcs' => $field['qty_pcs'],
                        'heat_no' => strtoupper($field['heatnumber']),
                        'invoice_no' => strtoupper($field['invoiceno']),
                        'received_date' => DATE_FORMAT($field['receiveddate'], 'Y-m-d'),
                        'supplier' => strtoupper($field['supplier']),
                        'width' => (isset($field['width']))? strtoupper($field['width']): 'N/A',
                        'length' => (isset($field['length']))? strtoupper($field['length']): 'N/A',
                        'supplier_heat_no' => (isset($field['supplierheatno']))? strtoupper($field['supplierheatno']): 'N/A',
                        'thickness' => (isset($field['thickness']))? strtoupper($field['thickness']): 'N/A',
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s"),
                        'create_user' => Auth::user()->id,
                        'update_user' => Auth::user()->id
                    ]);

                    // $mat = PpcMaterialAssembly::select('mat_type')
                    //                             ->where('character_num',7)
                    //                             ->where('character_code',substr(strtoupper($field['materialscode']),6,3))
                    //                             ->first();

                    // PpcUpdateInventory::insert([
                    //     'materials_type' =>  (isset($mat->material_type))? $mat->material_type: strtoupper($field['materialstype']),
                    //     'materials_code' => strtoupper($field['materialscode']),
                    //     'description' =>  'N/A',
                    //     'item' =>  'N/A',
                    //     'alloy' =>  'N/A',
                    //     'schedule' => 'N/A',
                    //     'size' =>  'N/A',
                    //     'quantity' => $field['quantity'],
                    //     'uom' => $uom,
                    //     'heat_no' => strtoupper($field['heatnumber']),
                    //     'invoice_no' => strtoupper($field['invoiceno']),
                    //     'received_date' => DATE_FORMAT($field['receiveddate'], 'Y-m-d'),
                    //     'supplier' => strtoupper($field['supplier']),
                    //     'width' => (isset($field['width']))? strtoupper($field['width']): 'N/A',
                    //     'length' => (isset($field['length']))? strtoupper($field['length']): 'N/A',
                    //     'supplier_heat_no' => (isset($field['supplierheatno']))? strtoupper($field['supplierheatno']): 'N/A',
                    //     'created_at' => date("Y-m-d H:i:s"),
                    //     'updated_at' => date("Y-m-d H:i:s"),
                    //     'create_user' => Auth::user()->id,
                    //     'update_user' => Auth::user()->id
                    // ]);
                } else {
                    $countAdded++;
                    $received_id = 0;

                    // $received_items = PpcUpdateInventory::where('materials_code',$field['materialscode'])
                    //                                     ->where('heat_no',$field['heatnumber'])
                    //                                     ->select('id')
                    //                                     ->first();

                    $PpcMaterialCode = PpcMaterialCode::select('material_type',
                                                               'code_description',
                                                               'item',
                                                               'alloy',
                                                               'schedule',
                                                               'size' )
                                                        ->where('material_code',$field['materialscode'])
                                                        ->first();
                    
                    // if ($this->_helper->check_if_exists($received_items) > 0) {
                    //     PpcUpdateInventory::where('id', $received_items->id)
                    //                     ->update([
                    //                         'materials_type' =>   $PpcMaterialCode->material_type,
                    //                         'materials_code' => strtoupper($field['materialscode']),
                    //                         'description' =>  $PpcMaterialCode->code_description,
                    //                         'item' =>  $PpcMaterialCode->item,
                    //                         'alloy' =>  $PpcMaterialCode->alloy,
                    //                         'schedule' => $PpcMaterialCode->schedule,
                    //                         'size' =>  $PpcMaterialCode->size,
                    //                         //'quantity' => $field['quantity'],
                    //                         'qty_weight' => $field['qty_weight'],
                    //                         'qty_pcs' => $field['qty_pcs'],
                    //                         'weight_uom' => 'KGS',
                    //                         'pcs_uom' => 'PCS',
                    //                         //'uom' => $uom,
                    //                         'heat_no' => strtoupper($field['heatnumber']),
                    //                         'invoice_no' => strtoupper($field['invoiceno']),
                    //                         'received_date' => DATE_FORMAT($field['receiveddate'], 'Y-m-d').' '.date('H:i:s'),
                    //                         'supplier' => strtoupper($field['supplier']),
                    //                         'width' => (isset($field['width']))? strtoupper($field['width']): 'N/A',
                    //                         'length' => (isset($field['length']))? strtoupper($field['length']): 'N/A',
                    //                         'supplier_heat_no' => (isset($field['supplierheatno']))? strtoupper($field['supplierheatno']): 'N/A',
                    //                         'updated_at' => date("Y-m-d H:i:s"),
                    //                         'update_user' => Auth::user()->id,
                    //                         'mode' => 'Updated from Upload'
                    //                     ]);
                    //     Inventory::where('received_id',$received_items->id)
                    //             ->update([
                    //                 'materials_type' => $PpcMaterialCode->material_type,
                    //                 'materials_code' => strtoupper($field['materialscode']),
                    //                 'description' =>  $PpcMaterialCode->code_description,
                    //                 'item' =>  $PpcMaterialCode->item,
                    //                 'alloy' =>  $PpcMaterialCode->alloy,
                    //                 'schedule' => $PpcMaterialCode->schedule,
                    //                 'size' =>  $PpcMaterialCode->size,
                    //                 'width' => (isset($field['width']))? strtoupper($field['width']): 'N/A',
                    //                 'length' => (isset($field['length']))? strtoupper($field['length']): 'N/A',
                    //                 'orig_quantity' => $field['qty_pcs'],
                    //                 'qty_weight' => $field['qty_weight'],
                    //                 'qty_pcs' => $field['qty_pcs'],
                    //                 'weight_uom' => 'KGS',
                    //                 'pcs_uom' => 'PCS',
                    //                 //'uom' => $uom,
                    //                 'heat_no' => strtoupper($field['heatnumber']),
                    //                 'invoice_no' => strtoupper($field['invoiceno']),
                    //                 'received_date' => DATE_FORMAT($field['receiveddate'], 'Y-m-d').' '.date('H:i:s'),
                    //                 'supplier' => strtoupper($field['supplier']),
                    //                 'supplier_heat_no' => (isset($field['supplierheatno']))? strtoupper($field['supplierheatno']): 'N/A',
                    //                 'updated_at' => date("Y-m-d H:i:s"),
                    //                 'update_user' => Auth::user()->id,
                    //                 'mode' => 'Updated from Upload'
                    //             ]);
                    // } else {
                        

                        $received_id = PpcUpdateInventory::insertGetId([
                                        'receiving_no' => strtoupper($field['receiving_no']),
                                        'materials_type' =>   $PpcMaterialCode->material_type,
                                        'materials_code' => strtoupper($field['materialscode']),
                                        'description' =>  $PpcMaterialCode->code_description,
                                        'item' =>  $PpcMaterialCode->item,
                                        'alloy' =>  $PpcMaterialCode->alloy,
                                        'schedule' => $PpcMaterialCode->schedule,
                                        'size' =>  $PpcMaterialCode->size,
                                        //'quantity' => $field['quantity'],
                                        'qty_weight' => $field['qty_weight'],
                                        'qty_pcs' => $field['qty_pcs'],
                                        'weight_uom' => 'KGS',
                                        'pcs_uom' => 'PCS',
                                        //'uom' => $uom,
                                        'heat_no' => strtoupper($field['heatnumber']),
                                        'invoice_no' => strtoupper($field['invoiceno']),
                                        'received_date' => DATE_FORMAT($field['receiveddate'], 'Y-m-d').' '.date('H:i:s'),
                                        'supplier' => strtoupper($field['supplier']),
                                        'width' => (isset($field['width']))? strtoupper($field['width']): 'N/A',
                                        'length' => (isset($field['length']))? strtoupper($field['length']): 'N/A',
                                        'supplier_heat_no' => (isset($field['supplierheatno']))? strtoupper($field['supplierheatno']): 'N/A',
                                        // 'thickness' => (isset($field['thickness']))? strtoupper($field['thickness']): 'N/A',
                                        'created_at' => date("Y-m-d H:i:s"),
                                        'updated_at' => date("Y-m-d H:i:s"),
                                        'create_user' => Auth::user()->id,
                                        'update_user' => Auth::user()->id,
                                        'mode' => 'Inserted from Upload'
                                    ]);

                        Inventory::insert([
                            'receiving_no' => strtoupper($field['receiving_no']),
                            'materials_type' => $PpcMaterialCode->material_type,
                            'materials_code' => strtoupper($field['materialscode']),
                            'description' =>  $PpcMaterialCode->code_description,
                            'item' =>  $PpcMaterialCode->item,
                            'alloy' =>  $PpcMaterialCode->alloy,
                            'schedule' => $PpcMaterialCode->schedule,
                            'size' =>  $PpcMaterialCode->size,
                            'width' => (isset($field['width']))? strtoupper($field['width']): 'N/A',
                            'length' => (isset($field['length']))? strtoupper($field['length']): 'N/A',
                            'orig_quantity' => $field['qty_pcs'],
                            //'quantity' => $field['quantity'],
                            'qty_weight' => $field['qty_weight'],
                            'qty_pcs' => $field['qty_pcs'],
                            'weight_uom' => 'KGS',
                            'pcs_uom' => 'PCS',
                            //'uom' => $uom,
                            'heat_no' => strtoupper($field['heatnumber']),
                            'invoice_no' => strtoupper($field['invoiceno']),
                            'received_date' => DATE_FORMAT($field['receiveddate'], 'Y-m-d').' '.date('H:i:s'),
                            'supplier' => strtoupper($field['supplier']),
                            'supplier_heat_no' => (isset($field['supplierheatno']))? strtoupper($field['supplierheatno']): 'N/A',
                            // 'thickness' => (isset($field['thickness']))? strtoupper($field['thickness']): 'N/A',
                            'received_id' => $received_id,
                            'created_at' => date("Y-m-d H:i:s"),
                            'updated_at' => date("Y-m-d H:i:s"),
                            'create_user' => Auth::user()->id,
                            'update_user' => Auth::user()->id,
                            'mode' => 'Inserted from Upload'
                        ]);
                    //}

                    
                }
            }
                
        }

        // $params = array_chunk($materialeArr, 1000);

        // foreach ($params as $param) {
        //     NotRegisteredMaterial::insert($param);
        // }
        
        if($countAdded == 0){
            
            $data = [
                'msg' => "All of the Material code is not yet registered.",
                'status' => 'warning',
                'Material' => $materialeArr,
            ];  
        }else{
            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module_id' => $this->_moduleID,
                'module' => 'Update Inventory Module',
                'action' => 'Uploaded Inventory.',
                'user' => Auth::user()->id
            ]);
            $data = [
                'msg' => "Upload Successful.",
                'status' => 'success',
                'Material' => $materialeArr,
            ];   
        }

        return response()->json($data);
    }

    public function materialDataTable(Request $req)
    {
        $with_zero = "and i.qty_pcs > 0";

        if ($req->with_zero > 0) {
            $with_zero = "";
        }

        $Datalist = DB::table('ppc_update_inventories as pui')
                        ->leftJoin('admin_assign_production_lines as apl', 'apl.product_line', '=', 'pui.materials_type')
                        ->leftJoin('inventories as i','pui.id','=','i.received_id')
                        ->select(
                            'pui.id as id',
                            DB::raw("ifnull(pui.receiving_no,'') as receiving_no"),
                            'pui.materials_type as materials_type',
                            'pui.materials_code as materials_code',
                            'pui.description as description',
                            'pui.item as item',
                            'pui.alloy as alloy',
                            'pui.schedule as schedule',
                            'pui.size as size',
                            'pui.qty_weight as qty_weight',
                            'pui.qty_pcs as qty_pcs',
                            DB::raw("IFNULL(i.qty_pcs,0) as current_stock"),
                            //'pui.uom as uom',
                            'pui.heat_no as heat_no',
                            'pui.invoice_no as invoice_no',
                            'pui.received_date as received_date',
                            'pui.supplier as supplier',
                            DB::raw("IFNULL(pui.length,'') as length"),
                            DB::raw("IFNULL(pui.width,'') as width"),
                            DB::raw("CONCAT(pui.width,' ','x',' ',pui.length) as wxl"),
                            DB::raw("IFNULL(pui.supplier_heat_no,'') as supplier_heat_no")
                            // DB::raw("IFNULL(pui.thickness,'') as thickness")
                        )
                        ->whereRaw('1=1 '.$with_zero)
                        ->where('apl.user_id' ,Auth::user()->id)
                        ->where('i.deleted','<>','1')
                        ->groupBy('pui.id',
                                'pui.receiving_no',
                                'pui.materials_type',
                                'pui.materials_code',
                                'pui.description',
                                'pui.item',
                                'pui.alloy',
                                'pui.schedule',
                                'pui.size',
                                'pui.qty_weight',
                                'pui.qty_pcs',
                                'i.qty_pcs',
                                //'pui.uom',
                                'pui.heat_no',
                                'pui.invoice_no',
                                'pui.received_date',
                                'pui.supplier',
                                DB::raw("IFNULL(pui.length,'')"),
                                DB::raw("IFNULL(pui.width,'')"),
                                DB::raw("CONCAT(pui.width,' ','x',' ',pui.length)"),
                                DB::raw("IFNULL(pui.supplier_heat_no,'')")
                                // DB::raw("IFNULL(pui.thickness,'')"))
                        )
                        ->orderBy('pui.id','desc')->get();
                        
        return response()->json($Datalist);
    }

    public function AddManual(Request $req)
    {
        $result = "";
        if (isset($req->material_id)) {
            $this->validate($req, [
                'receiving_no' => 'required',
                'materials_type' => 'required',
                'materials_code' => 'required',
                //'quantity' => 'required|numeric',
                'qty_weight' => 'required|numeric',
                'qty_pcs' => 'required|numeric',
                // 'item' => 'required',
                // 'size' => 'required',
                // 'alloy' => 'required',
                'heat_no' => 'required',
                'invoice_no' => 'required',
                'received_date' => 'required',
                'supplier' => 'required'

            ]);
            // $check = PpcUpdateInventory::where('heat_no',$req->heat_no)->where('id','!=', $req->material_id)->count();
            // if ($check > 0) {
            //     return response()->json(['errors' => ['heat_no' => 'The Material Heat No has already taken.'] ], 422);
            // }

                $UP = PpcUpdateInventory::find($req->material_id);

                $UP->receiving_no = strtoupper($req->receiving_no);
                $UP->materials_type = strtoupper($req->materials_type);
                $UP->materials_code = strtoupper($req->materials_code);
                $UP->description = strtoupper($req->description);
                $UP->item = strtoupper($req->item);
                $UP->alloy = strtoupper($req->alloy);
                $UP->schedule = strtoupper($req->schedule);
                $UP->size = strtoupper($req->size);
                $UP->width = (!is_null($req->width))? strtoupper($req->width): 'N/A';
                $UP->length = (!is_null($req->length))? strtoupper($req->length): 'N/A';
                // $UP->quantity = $req->quantity;
                $UP->qty_weight = $req->qty_weight;
                $UP->qty_pcs = $req->qty_pcs;
                $UP->weight_uom = 'KGS';
                $UP->pcs_uom = 'PCS';
                // $UP->uom = strtoupper($req->uom);
                $UP->heat_no = strtoupper($req->heat_no);
                $UP->invoice_no = strtoupper($req->invoice_no);
                $UP->received_date = $req->received_date.' '.date('H:i:s');
                $UP->supplier = (!is_null($req->supplier))? strtoupper($req->supplier): 'N/A';
                $UP->supplier_heat_no = (!is_null($req->supplier_heat_no))? strtoupper($req->supplier_heat_no): 'N/A';
                // $UP->thickness = (!is_null($req->thickness))? strtoupper($req->thickness): 'N/A';
                $UP->update_user =  Auth::user()->id;
                $UP->mode = 'Updated from Manual';

                if ($UP->update()) {
                    Inventory::where('received_id',$req->material_id)
                            ->update([
                                'receiving_no' => strtoupper($req->receiving_no),
                                'materials_type' => strtoupper($req->materials_type),
                                'materials_code' => strtoupper($req->materials_code),
                                'description' => strtoupper($req->description),
                                'item' => strtoupper($req->item),
                                'alloy' => strtoupper($req->alloy),
                                'schedule' => strtoupper($req->schedule),
                                'size' => strtoupper($req->size),
                                'width' => (!is_null($req->width))? strtoupper($req->width): 'N/A',
                                'length' => (!is_null($req->length))? strtoupper($req->length): 'N/A',
                                // 'orig_quantity' => $req->qty_pcs,
                                // 'quantity' => $req->quantity,
                                'qty_weight' => $req->qty_weight,
                                'qty_pcs' => $req->qty_pcs,
                                'weight_uom' => 'KGS',
                                'pcs_uom' => 'PCS',
                                // 'uom' => strtoupper($req->uom),
                                'heat_no' => strtoupper($req->heat_no),
                                'invoice_no' => strtoupper($req->invoice_no),
                                'received_date' => $req->received_date.' '.date('H:i:s'),
                                'supplier' => (!is_null($req->supplier))? strtoupper($req->supplier): 'N/A',
                                'supplier_heat_no' => (!is_null($req->supplier_heat_no))? strtoupper($req->supplier_heat_no): 'N/A',
                                // 'thickness' => (!is_null($req->thickness))? strtoupper($req->thickness): 'N/A',
                                'update_user' =>  Auth::user()->id,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'mode' => 'Updated from Manual'
                            ]);
                }
                
                $result = "Update";

                $this->_audit->insert([
                    'user_type' => Auth::user()->user_type,
                    'module_id' => $this->_moduleID,
                    'module' => 'Update Inventory Module',
                    'action' => 'Updated Inventory data ID: '.$req->material_id.',
                                Material Code: '.$req->materials_code.' manually.',
                    'user' => Auth::user()->id
                ]);
        }else {
            $this->validate($req, [
                'receiving_no' => 'required',
                'materials_type' => 'required',
                'materials_code' => 'required',
                // 'quantity' => 'required|numeric',
                'qty_weight' => 'required|numeric',
                'qty_pcs' => 'required|numeric',
                // 'item' => 'required',
                // 'size' => 'required',
                // 'alloy' => 'required',
                'heat_no' => 'required',
                'invoice_no' => 'required',
                'received_date' => 'required',
                'supplier' => 'required'
            ]);

            $received = PpcUpdateInventory::insertGetId([
                            'receiving_no' => strtoupper($req->receiving_no),
                            'materials_type' => strtoupper($req->materials_type),
                            'materials_code' => strtoupper($req->materials_code),
                            'description' => strtoupper($req->description),
                            'item' => strtoupper($req->item),
                            'alloy' => strtoupper($req->alloy),
                            'schedule' => strtoupper($req->schedule),
                            'size' => strtoupper($req->size),
                            'width' => (!is_null($req->width))? strtoupper($req->width): 'N/A',
                            'length' => (!is_null($req->length))? strtoupper($req->length): 'N/A',
                            // 'quantity' => $req->quantity,
                            'qty_weight' => $req->qty_weight,
                            'qty_pcs' => $req->qty_pcs,
                            'weight_uom' => 'KGS',
                            'pcs_uom' => 'PCS',
                            // 'uom' => strtoupper($req->uom),
                            'heat_no' => strtoupper($req->heat_no),
                            'invoice_no' => strtoupper($req->invoice_no),
                            'received_date' => $req->received_date.' '.date('H:i:s'),
                            'supplier' => (!is_null($req->supplier))? strtoupper($req->supplier): 'N/A',
                            'supplier_heat_no' => (!is_null($req->supplier_heat_no))? strtoupper($req->supplier_heat_no): 'N/A',
                            // 'thickness' => (!is_null($req->thickness))? strtoupper($req->thickness): 'N/A',
                            'create_user' =>  Auth::user()->id,
                            'update_user' =>  Auth::user()->id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                            'mode' => 'Inserted from Manual'
                        ]);

            if ($received) {
                $inv = new Inventory();

                $inv->receiving_no = strtoupper($req->receiving_no);
                $inv->materials_type = strtoupper($req->materials_type);
                $inv->materials_code = strtoupper($req->materials_code);
                $inv->description = strtoupper($req->description);
                $inv->item = strtoupper($req->item);
                $inv->alloy = strtoupper($req->alloy);
                $inv->schedule = strtoupper($req->schedule);
                $inv->size = strtoupper($req->size);
                $inv->width = (!is_null($req->width))? strtoupper($req->width): 'N/A';
                $inv->length = (!is_null($req->length))? strtoupper($req->length): 'N/A';
                // $inv->quantity = $req->quantity;
                $inv->orig_quantity = $req->qty_pcs;
                $inv->qty_weight = $req->qty_weight;
                $inv->qty_pcs = $req->qty_pcs;
                $inv->weight_uom = 'KGS';
                $inv->pcs_uom = 'PCS';
                // $inv->uom = strtoupper($req->uom);
                $inv->heat_no = strtoupper($req->heat_no);
                $inv->invoice_no = strtoupper($req->invoice_no);
                $inv->received_date = $req->received_date.' '.date('H:i:s');
                $inv->supplier = (!is_null($req->supplier))? strtoupper($req->supplier): 'N/A';
                $inv->supplier_heat_no = (!is_null($req->supplier_heat_no))? strtoupper($req->supplier_heat_no): 'N/A';
                // $inv->thickness = (!is_null($req->thickness))? strtoupper($req->thickness): 'N/A';
                $inv->received_id = $received;
                $inv->create_user =  Auth::user()->id;
                $inv->update_user =  Auth::user()->id;
                $inv->mode = 'Inserted from Manual';

                $inv->save();
            }
            
            $result = "Added";

            $this->_audit->insert([
                'user_type' => Auth::user()->user_type,
                'module_id' => $this->_moduleID,
                'module' => 'Update Inventory Module',
                'action' => 'Inserted Inventory data Material Code: '.$req->materials_code.' manually.',
                'user' => Auth::user()->id
            ]);
        }
           
         return response()->json(['msg'=>"Data was successfully saved.",'status' => 'success']);
    }

    public function GetMaterialType()
    {
        $type = DB::table('ppc_dropdown_items as pdt')
                    ->leftjoin('admin_assign_production_lines as apl', 'apl.product_line', '=', 'pdt.dropdown_item')
                    ->select([
                        'apl.product_line as material_type',
                    ])
                    ->where('pdt.dropdown_name_id', 8) // material type
                    ->where('apl.user_id' , Auth::user()->id)
                    ->groupBy('apl.product_line')
                    ->get();

                    // DB::table('ppc_material_codes as pmc')
                    // ->leftjoin('admin_assign_production_lines as apl', 'apl.product_line', '=', 'pmc.material_type')
                    // ->select(['pmc.material_type as material_type'])
                    // ->where('apl.user_id' ,Auth::user()->id)
                    // ->groupBy('pmc.material_type')->get();

        return $data = [
                    'type' => $type,
                ];
    }

    public function GetMaterialCode(Request $req)
    {
        $code = DB::select("select pmc.material_code as material_code,
                                    pmc.material_type as material_type,
                                    apl.user_id
                            from ppc_material_codes as pmc
                            left join admin_assign_production_lines as apl
                            on apl.product_line = pmc.material_type
                            where apl.user_id = '".Auth::user()->id."'
                            and pmc.material_type = '".$req->mat_type."'
                            order by pmc.id desc");
        // $code = DB::table('ppc_material_codes as pmc')
        //             ->leftjoin('admin_assign_production_lines as apl', 'apl.product_line', '=', 'pmc.material_type')
        //             ->select(['pmc.material_code as material_code'])
        //             ->where('apl.user_id' ,Auth::user()->id)
        //             ->where('pmc.material_type', $req->mat_type)
        //             ->orderBy('pmc.id','desc')
        //             ->get();

        return $code;
        // return $data = [
        //             'code' => $code,
        //         ];
    }

    public function GetMaterialCodeDetails(Request $req)
    {
        $data = PpcMaterialCode::select('code_description',
                                    'item',
                                    'alloy',
                                    'schedule',
                                    'size')
                ->where('material_code', $req->mat_code)
                ->first();
        return response()->json($data);
    }

    public function unRegisteredMaterials()
    {
        $none = NotRegisteredMaterial::select(
                                        'receiving_no',
                                        'materials_type',
                                        'materials_code',
                                        'qty_weight',
                                        'qty_pcs',
                                        'heat_no',
                                        'invoice_no',
                                        'received_date',
                                        'supplier',
                                        'width',
                                        'length',
                                        'supplier_heat_no',
                                        // 'thickness',
                                        DB::raw("left(created_at,10) as created_at")
                                    )
                                    ->where('create_user',Auth::user()->id)
                                    ->groupBy(
                                        'receiving_no',
                                        'materials_type',
                                        'materials_code',
                                        'qty_weight',
                                        'qty_pcs',
                                        'heat_no',
                                        'invoice_no',
                                        'received_date',
                                        'supplier',
                                        'width',
                                        'length',
                                        'supplier_heat_no',
                                        // 'thickness',
                                        DB::raw("left(created_at,10)")
                                    )
                                    ->get();
        return response()->json($none);
    }

    public function unRegisteredMaterialsExcel()
    {
        $date = date('Y-m-d');
        $data = NotRegisteredMaterial::select(
                                        'receiving_no',
                                        'materials_code',
                                        'qty_weight',
                                        'qty_pcs',
                                        'heat_no',
                                        'invoice_no',
                                        'received_date',
                                        'supplier',
                                        'width',
                                        'length',
                                        'supplier_heat_no',
                                        // 'thickness',
                                        DB::raw("left(created_at,10) as created_at")
                                    )
                                    ->where('create_user',Auth::user()->id)
                                    ->groupBy(
                                        'receiving_no',
                                        'materials_code',
                                        'qty_weight',
                                        'qty_pcs',
                                        'heat_no',
                                        'invoice_no',
                                        'received_date',
                                        'supplier',
                                        'width',
                                        'length',
                                        'supplier_heat_no',
                                        // 'thickness',
                                        DB::raw("left(created_at,10)")
                                    )
                                    ->get();

        Excel::create('Unregistered_Materials_'.$date, function($excel) use($data)
        {
            $excel->sheet('Report', function($sheet) use($data)
            {
                $sheet->setHeight(1, 15);
                $sheet->mergeCells('A1:K1');
                $sheet->cells('A1:K1', function($cells) {
                    $cells->setAlignment('center');
                    $cells->setFont([
                        'family'     => 'Calibri',
                        'size'       => '14',
                        'bold'       =>  true,
                    ]);
                });
                $sheet->cell('A1'," Upload Material Inventory");

                $sheet->setHeight(2, 15);
                $sheet->mergeCells('A2:K2');
                $sheet->cells('A2:K2', function($cells) {
                    $cells->setAlignment('center');
                });

                $sheet->setHeight(4, 20);
                $sheet->mergeCells('A4:K4');
                $sheet->cells('A4:K4', function($cells) {
                    $cells->setAlignment('center');
                    $cells->setFont([
                        'family'     => 'Calibri',
                        'size'       => '14',
                        'bold'       =>  true,
                        'underline'  =>  true
                    ]);
                });
                $sheet->cell('A4',"UNREGISTERED MATERIALS");

                $sheet->setHeight(6, 15);
                $sheet->cells('A6:K6', function($cells) {
                    $cells->setFont([
                        'family'     => 'Calibri',
                        'size'       => '11',
                        'bold'       =>  true,
                    ]);
                    // Set all borders (top, right, bottom, left)
                    $cells->setBorder('solid', 'solid', 'solid', 'solid');
                });
                $sheet->cell('A6', "Material Code");
                $sheet->cell('B6', "Quantity(KGS)");
                $sheet->cell('C6', "Quantity(PCS)");
                $sheet->cell('D6', "Heat No.");
                $sheet->cell('E6', "Invoice No.");
                $sheet->cell('F6', "Received Date");
                $sheet->cell('G6', "Supplier");
                $sheet->cell('H6', "width");
                $sheet->cell('I6', "Length");
                $sheet->cell('J6', "Supplier Heat No.");
                // $sheet->cell('K6', "Thickness");
                $sheet->cell('K6', "Receiving No.");
                $sheet->cell('L6', "Date Uploaded");

                $row = 7;

                foreach ($data as $key => $dt) {
                    $sheet->setHeight($row, 15);
                    $sheet->cell('A'.$row, $dt->materials_code);
                    $sheet->cell('B'.$row, $dt->qty_weight);
                    $sheet->cell('C'.$row, $dt->qty_pcs);
                    $sheet->cell('D'.$row, $dt->heat_no);
                    $sheet->cell('E'.$row, $dt->invoice_no);
                    $sheet->cell('F'.$row, $dt->received_date);
                    $sheet->cell('G'.$row, $dt->supplier);
                    $sheet->cell('H'.$row, $dt->width);
                    $sheet->cell('I'.$row, $dt->length);
                    $sheet->cell('J'.$row, $dt->supplier_heat_no);
                    // $sheet->cell('K'.$row, $dt->thickness);
                    $sheet->cell('K'.$row, $dt->receiving_no);
                    $sheet->cell('L'.$row, $this->_helper->convertDate($dt->created_at,'Y-m-d'));
                    
                    $row++;
                }
                
                $sheet->cells('A6:K'.$row, function($cells) {
                    $cells->setBorder('solid', 'solid', 'solid', 'solid');
                });
            });
        })->download('xlsx');
    }

    public function downloadExcelFormat()
    {
        $date = date('Ymd');
        Excel::create('UPDATE_INVENTORY_FORMAT_'.$date, function($excel)
        {
            $excel->sheet('materials', function($sheet)
            {
                $sheet->setHeight(6, 15);
                $sheet->cells('A1:R1', function($cells) {
                    $cells->setFont([
                        'family'     => 'Calibri',
                        'size'       => '11',
                        'bold'       =>  true,
                    ]);
                });
                $sheet->cell('A1', "receiving_no");
                $sheet->cell('B1', "materialstype");
                $sheet->cell('C1', "materialscode");
                $sheet->cell('D1', "description");
                $sheet->cell('E1', "item");
                $sheet->cell('F1', "alloy");
                $sheet->cell('G1', "schedule");
                $sheet->cell('H1', "size");
                // $sheet->cell('I1', "thickness");
                $sheet->cell('I1', "qty_weight");
                $sheet->cell('J1', "qty_pcs");
                $sheet->cell('K1', "heatnumber");
                $sheet->cell('L1', "invoiceno");
                $sheet->cell('M1', "receiveddate");
                $sheet->cell('N1', "supplier");
                $sheet->cell('O1', "width");
                $sheet->cell('P1', "length");
                $sheet->cell('Q1', "supplierheatno");
                
            });
        })->download('xlsx');
    }

    public function searchFilter(Request $req)
    {
        $srch_received_date = '';
        $srch_receiving_no = '';
        $srch_materials_type = '';
        $srch_materials_code = '';
        $srch_item = '';
        $srch_alloy = '';
        $srch_schedule = '';
        $srch_size = '';
        $srch_thickness = '';
        $srch_width = '';
        $srch_length = '';
        $srch_heat_no = '';
        $srch_invoice_no = '';
        $srch_supplier = '';
        $srch_supplier_heat_no = '';

        if (!is_null($req->srch_received_date_from) && !is_null($req->srch_received_date_to)) {
            $srch_received_date = " AND DATE_FORMAT(pui.received_date,'%Y-%m-%d') BETWEEN '".$req->srch_received_date_from."' AND '".$req->srch_received_date_to."'";
        }

        if (!is_null($req->srch_receiving_no)) {
            $equal = "= ";
            $_value = $req->srch_receiving_no;

            if (Str::contains($req->srch_receiving_no, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_receiving_no);
            }
            $srch_receiving_no = " AND pui.receiving_no ".$equal."'".$_value."'";
        }

        if (!is_null($req->srch_materials_type)) {
            $equal = "= ";
            $_value = $req->srch_materials_type;
            
            if (Str::contains($req->srch_materials_type, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_materials_type);
            }
            $srch_materials_type = " AND pui.materials_type ".$equal." '".$_value."'";
        }

        if (!is_null($req->srch_materials_code)) {
            $equal = "= ";
            $_value = $req->srch_materials_code;
            
            if (Str::contains($req->srch_materials_code, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_materials_code);
            }
            $srch_materials_code = " AND pui.materials_code ".$equal." '".$_value."'";
        }

        if (!is_null($req->srch_item)) {
            $equal = "= ";
            $_value = $req->srch_item;
            
            if (Str::contains($req->srch_item, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_item);
            }
            $srch_item = " AND pui.item ".$equal." '".$_value."'";
        }

        if (!is_null($req->srch_alloy)) {
            $equal = "= ";
            $_value = $req->srch_alloy;
            
            if (Str::contains($req->srch_alloy, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_alloy);
            }
            $srch_alloy = " AND pui.alloy ".$equal." '".$_value."'";
        }

        if (!is_null($req->srch_schedule)) {
            $equal = "= ";
            $_value = $req->srch_schedule;
            
            if (Str::contains($req->srch_schedule, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_schedule);
            }
            $srch_schedule = " AND pui.schedule ".$equal." '".$_value."'";
        }

        if (!is_null($req->srch_size)) {
            $equal = "= ";
            $_value = $req->srch_size;
            
            if (Str::contains($req->srch_size, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_size);
            }
            $srch_size = " AND pui.size ".$equal." '".$_value."'";
        }

        if (!is_null($req->srch_thickness)) {
            $equal = "= ";
            $_value = $req->srch_thickness;
            
            if (Str::contains($req->srch_thickness, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_thickness);
            }
            $srch_thickness = " AND pui.thickness ".$equal." '".$_value."'";
        }

        if (!is_null($req->srch_width)) {
            $equal = "= ";
            $_value = $req->srch_width;
            
            if (Str::contains($req->srch_width, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_width);
            }
            $srch_width = " AND pui.width ".$equal." '".$_value."'";
        }

        if (!is_null($req->srch_length)) {
            $equal = "= ";
            $_value = $req->srch_length;
            
            if (Str::contains($req->srch_length, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_length);
            }
            $srch_length = " AND pui.`length` ".$equal." '".$_value."'";
        }
        if (!is_null($req->srch_heat_no)) {
            $equal = "= ";
            $_value = $req->srch_heat_no;
            
            if (Str::contains($req->srch_heat_no, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_heat_no);
            }
            $srch_heat_no = " AND pui.heat_no ".$equal." '".$_value."'";
        }
        if (!is_null($req->srch_invoice_no)) {
            $equal = "= ";
            $_value = $req->srch_invoice_no;
            
            if (Str::contains($req->srch_invoice_no, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_invoice_no);
            }
            $srch_invoice_no = " AND pui.invoice_no ".$equal." '".$_value."'";
        }
        if (!is_null($req->srch_supplier)) {
            $equal = "= ";
            $_value = $req->srch_supplier;
            
            if (Str::contains($req->srch_supplier, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_supplier);
            }
            $srch_supplier = " AND pui.supplier ".$equal." '".$_value."'";
        }
        if (!is_null($req->srch_supplier_heat_no)) {
            $equal = "= ";
            $_value = $req->srch_supplier_heat_no;
            
            if (Str::contains($req->srch_supplier_heat_no, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_supplier_heat_no);
            }
            $srch_supplier_heat_no = " AND pui.supplier_heat_no ".$equal." '".$_value."'";
        }

        $data = DB::table('ppc_update_inventories as pui')
                    ->leftJoin('admin_assign_production_lines as apl', 'apl.product_line', '=', 'pui.materials_type')
                    ->leftJoin('inventories as i','pui.id','=','i.received_id')
                    ->select(
                        'pui.id as id',
                        DB::raw("ifnull(pui.receiving_no,'') as receiving_no"),
                        'pui.materials_type as materials_type',
                        'pui.materials_code as materials_code',
                        'pui.description as description',
                        'pui.item as item',
                        'pui.alloy as alloy',
                        'pui.schedule as schedule',
                        'pui.size as size',
                        'pui.qty_weight as qty_weight',
                        'pui.qty_pcs as qty_pcs',
                        DB::raw("IFNULL(i.qty_pcs,0) as current_stock"),
                        //'pui.uom as uom',
                        'pui.heat_no as heat_no',
                        'pui.invoice_no as invoice_no',
                        'pui.received_date as received_date',
                        'pui.supplier as supplier',
                        DB::raw("IFNULL(pui.length,'') as length"),
                        DB::raw("IFNULL(pui.width,'') as width"),
                        DB::raw("CONCAT(pui.width,' ','x',' ',pui.length) as wxl"),
                        DB::raw("IFNULL(pui.supplier_heat_no,'') as supplier_heat_no"),
                        DB::raw("IFNULL(pui.thickness,'') as thickness")
                    )
                    ->whereRaw('1=1 '.$srch_received_date.$srch_receiving_no.$srch_materials_type.$srch_materials_code.$srch_item.$srch_alloy.$srch_schedule.$srch_size.$srch_thickness.$srch_width.$srch_length.$srch_heat_no.$srch_invoice_no.$srch_supplier.$srch_supplier_heat_no)
                    ->where('apl.user_id' ,Auth::user()->id)
                    ->where('i.deleted','<>','1')
                    ->groupBy('pui.id',
                            'pui.receiving_no',
                            'pui.materials_type',
                            'pui.materials_code',
                            'pui.description',
                            'pui.item',
                            'pui.alloy',
                            'pui.schedule',
                            'pui.size',
                            'pui.qty_weight',
                            'pui.qty_pcs',
                            'i.qty_pcs',
                            //'pui.uom',
                            'pui.heat_no',
                            'pui.invoice_no',
                            'pui.received_date',
                            'pui.supplier',
                            DB::raw("IFNULL(pui.length,'')"),
                            DB::raw("IFNULL(pui.width,'')"),
                            DB::raw("CONCAT(pui.width,' ','x',' ',pui.length)"),
                            DB::raw("IFNULL(pui.supplier_heat_no,'')"),
                            DB::raw("IFNULL(pui.thickness,'')"))
                    ->orderBy('pui.id','desc')->get();
                        
        return response()->json($data);
    }
}