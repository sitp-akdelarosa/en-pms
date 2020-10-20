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
use App\PpcProductCode;
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
        $item_classes = $this->_helper->getItemClassification();

        return view('ppc.transaction.update-inventory',[
                    'user_accesses' => $user_accesses,
                    'item_classes' => $item_classes
                ]);
    }
  
    public function CheckFile(Request $req)
    {
        $file = $req->file('file_inventory');
        $fields;
        
        Excel::load($file, function($reader) use(&$fields){
            $fields = $reader->toArray();
        });

        switch ($req->up_item_class) {
            case 'RAW MATERIAL':
                if (!array_key_exists('itemcode',$fields[0]) AND 
                    !array_key_exists('qty_weight',$fields[0]) AND 
                    !array_key_exists('qty_pcs',$fields[0]) AND 
                    !array_key_exists('heatnumber',$fields[0]) AND 
                    !array_key_exists('receiveddate',$fields[0]) AND 
                    !array_key_exists('invoiceno',$fields[0]) AND 
                    !array_key_exists('supplier',$fields[0]) AND 
                    !array_key_exists('receiving_no',$fields[0])) {
                    $data = [
                        'status' => 'failed',
                        'msg' => 'Please use the correct upload format for Raw material.'
                    ];

                    return response()->json($data);
                } else {
                    return $this->CheckFileMaterial($fields,$req->up_item_class);
                }
                break;
            
            default:
                if (!array_key_exists('itemcode',$fields[0]) AND 
                        !array_key_exists('lotnumber',$fields[0]) AND 
                        !array_key_exists('product_line',$fields[0]) AND 
                        !array_key_exists('jo_no',$fields[0])) {
                    $data = [
                        'status' => 'failed',
                        'msg' => 'Please use the correct upload format for Product (Finished / Crude).'
                    ];

                    return response()->json($data);
                } else {
                    return $this->CheckFileProduct($fields,$req->up_item_class);
                }
                break;
        }
    }

    public function CheckFileMaterial($fields,$item_class)
    {
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
            if($this->checkExcessSpace($field,$item_class)) {

            } else {
                if (empty($field['receiving_no']) && is_null($field['receiving_no'])) {
                    $failed++;
                    array_push($msg, 'Please provide Receiving No. for Line '.$line.'.');
                }

                if (empty($field['itemcode']) && is_null($field['itemcode'])) {
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
                                        ->Where('item_code',$field['itemcode'])
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
        }

        if ($failed > 0) {
            $message = '';
            foreach ($msg as $key => $m) {
                $message.= $m."\r\n";
            }

            $data = [
                'status' => 'failed',
                'msg' => $message,
                'item_class' => $item_class
            ];
        }

        if (count($sameItem) > 0) {
            $data = [
                'status' => 'same_items',
                'msg' => 'These items were already uploaded.',
                'same_items' => $sameItem,
                'item_class' => $item_class
            ];
        } 
        return response()->json($data);
    }

    public function CheckFileProduct($fields,$item_class)
    {
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
            if($this->checkExcessSpace($field,$item_class)) {

            } else {
                if (empty($field['jo_no']) && is_null($field['jo_no'])) {
                    $failed++;
                    array_push($msg, 'Please provide J.O. No. for Line '.$line.'.');
                }

                if (empty($field['product_line']) && is_null($field['product_line'])) {
                    $failed++;
                    array_push($msg, 'Please provide Product Line for Line '.$line.'.');
                }

                if (empty($field['itemcode']) && is_null($field['itemcode'])) {
                    $failed++;
                    array_push($msg, 'Please provide Product Code for Line '.$line.'.');
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

                if (empty($field['lotnumber']) && is_null($field['lotnumber'])) {
                    $failed++;
                    array_push($msg, 'Please provide Heat Number for Line '.$line.'.');
                }

                if ($failed == 0) {

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
                                        ->Where('item_code',$field['itemcode'])
                                        ->where('heat_no',$field['heatnumber'])
                                        ->where('lot_no',$field['lotnumber'])
                                        ->where('qty_weight', $field['qty_weight'])
                                        ->where('qty_pcs', $field['qty_pcs'])
                                        ->where('jo_no', $field['jo_no'])
                                        ->where('create_user',Auth::user()->id)
                                        ->first();

                    if ($this->_helper->check_if_exists($received_items) > 0) {
                        array_push($sameItem,$received_items);
                    }
                }
                
            }
        }

        if ($failed > 0) {
            $message = '';
            foreach ($msg as $key => $m) {
                $message.= $m."\r\n";
            }

            $data = [
                'status' => 'failed',
                'msg' => $message,
                'item_class' => $item_class
            ];
        }

        if (count($sameItem) > 0) {
            $data = [
                'status' => 'same_items',
                'msg' => 'These items were already uploaded.',
                'same_items' => $sameItem,
                'item_class' => $item_class
            ];
        } 
        return response()->json($data);
    }

    private function checkExcessSpace($field,$item_class) 
    {
        $error = false;

        switch ($item_class) {
            case 'RAW MATERIAL':
                if ((empty($field['itemcode']) && is_null($field['itemcode'])) &&
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
                break;
            
            default:
                if ((empty($field['itemcode']) && is_null($field['itemcode'])) &&
                    (empty($field['qty_weight']) && is_null($field['qty_weight'])) &&
                    (empty($field['qty_pcs']) && is_null($field['qty_pcs'])) && 
                    (empty($field['heatnumber']) && is_null($field['heatnumber'])) && 
                    (empty($field['lotnumber']) && is_null($field['lotnumber'])) &&
                    (empty($field['product_line']) && is_null($field['product_line'])) &&
                    (empty($field['jo_no']) && is_null($field['jo_no']))
                ) {
                    $error = true;
                }

                return $error;
                break;
        }
        
    }

    public function UploadInventory(Request $req)
    {
        $file = $req->file('file_inventory');
        $fields;
        
        Excel::load($file, function($reader) use(&$fields){
            $fields = $reader->toArray();
        });

        switch ($req->up_item_class) {
            case 'RAW MATERIAL':
                return $this->uploadMaterialExcel($fields,$req->up_item_class);
                break;
            
            default:
                return $this->uploadProductExcel($fields,$req->up_item_class);
                break;
        }
    }

    public function uploadMaterialExcel($fields,$item_class)
    {
        $materialeArr = [];
        $countAdded = 0;
        foreach ($fields as $key => $field) {
            if((!empty($field['itemcode']) && !is_null($field['itemcode'])) || 
                (!empty($field['qty_weight']) && !is_null($field['qty_weight'])) || 
                (!empty($field['qty_pcs']) && !is_null($field['qty_pcs'])) ||
                (!empty($field['heatnumber']) && !is_null($field['heatnumber'])) ||
                (!empty($field['receiveddate']) && !is_null($field['receiveddate'])) ||
                (!empty($field['receiving_no']) && !is_null($field['receiving_no'])) ||
                (!empty($field['invoiceno']) && !is_null($field['invoiceno'])) ||
                (!empty($field['supplier']) && !is_null($field['supplier'])) ) {

                //$uom = preg_replace('/[0-9]+/', '', strtoupper($field['uom']));

                $checkMatCode = PpcMaterialCode::where('material_code',$field['itemcode'])->count();  
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

                    NotRegisteredMaterial::where('item_code',$field['itemcode'])
                                        ->where('heat_no',$field['heatnumber'])
                                        ->delete();

                    NotRegisteredMaterial::insert([
                        'item_class' => $item_class,
                        'receiving_no' => strtoupper($field['receiving_no']),
                        'materials_type' => (isset($mat->material_type))? $mat->material_type: strtoupper($field['materialstype']),
                        'item_code' => strtoupper($field['itemcode']),
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
                } else {
                    $countAdded++;
                    $received_id = 0;

                    $PpcMaterialCode = PpcMaterialCode::select('material_type',
                                                               'code_description',
                                                               'item',
                                                               'alloy',
                                                               'schedule',
                                                               'size' )
                                                        ->where('material_code',$field['itemcode'])
                                                        ->first();
                    

                        $received_id = PpcUpdateInventory::insertGetId([
                                        'item_class' => $item_class,
                                        'receiving_no' => strtoupper($field['receiving_no']),
                                        'materials_type' =>   $PpcMaterialCode->material_type,
                                        'item_code' => strtoupper($field['itemcode']),
                                        'description' =>  $PpcMaterialCode->code_description,
                                        'item' =>  $PpcMaterialCode->item,
                                        'alloy' =>  $PpcMaterialCode->alloy,
                                        'schedule' => $PpcMaterialCode->schedule,
                                        'size' =>  $PpcMaterialCode->size,
                                        'qty_weight' => $field['qty_weight'],
                                        'qty_pcs' => $field['qty_pcs'],
                                        'weight_uom' => 'KGS',
                                        'pcs_uom' => 'PCS',
                                        'heat_no' => strtoupper($field['heatnumber']),
                                        'invoice_no' => strtoupper($field['invoiceno']),
                                        'received_date' => DATE_FORMAT($field['receiveddate'], 'Y-m-d').' '.date('H:i:s'),
                                        'supplier' => strtoupper($field['supplier']),
                                        'width' => (isset($field['width']))? strtoupper($field['width']): 'N/A',
                                        'length' => (isset($field['length']))? strtoupper($field['length']): 'N/A',
                                        'supplier_heat_no' => (isset($field['supplierheatno']))? strtoupper($field['supplierheatno']): 'N/A',
                                        'created_at' => date("Y-m-d H:i:s"),
                                        'updated_at' => date("Y-m-d H:i:s"),
                                        'create_user' => Auth::user()->id,
                                        'update_user' => Auth::user()->id,
                                        'mode' => 'Inserted from Upload'
                                    ]);

                        Inventory::insert([
                            'item_class' => $item_class,
                            'receiving_no' => strtoupper($field['receiving_no']),
                            'materials_type' => $PpcMaterialCode->material_type,
                            'item_code' => strtoupper($field['itemcode']),
                            'description' =>  $PpcMaterialCode->code_description,
                            'item' =>  $PpcMaterialCode->item,
                            'alloy' =>  $PpcMaterialCode->alloy,
                            'schedule' => $PpcMaterialCode->schedule,
                            'size' =>  $PpcMaterialCode->size,
                            'width' => (isset($field['width']))? strtoupper($field['width']): 'N/A',
                            'length' => (isset($field['length']))? strtoupper($field['length']): 'N/A',
                            'orig_quantity' => $field['qty_pcs'],
                            'qty_weight' => $field['qty_weight'],
                            'qty_pcs' => $field['qty_pcs'],
                            'weight_uom' => 'KGS',
                            'pcs_uom' => 'PCS',
                            'heat_no' => strtoupper($field['heatnumber']),
                            'invoice_no' => strtoupper($field['invoiceno']),
                            'received_date' => DATE_FORMAT($field['receiveddate'], 'Y-m-d').' '.date('H:i:s'),
                            'supplier' => strtoupper($field['supplier']),
                            'supplier_heat_no' => (isset($field['supplierheatno']))? strtoupper($field['supplierheatno']): 'N/A',
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

    public function uploadProductExcel($fields,$item_class)
    {
        $materialeArr = [];
        $countAdded = 0;
        foreach ($fields as $key => $field) {
            if((!empty($field['itemcode']) && !is_null($field['itemcode'])) || 
                (!empty($field['qty_weight']) && !is_null($field['qty_weight'])) || 
                (!empty($field['qty_pcs']) && !is_null($field['qty_pcs'])) ||
                (!empty($field['heatnumber']) && !is_null($field['heatnumber'])) ||
                (!empty($field['lotnumber']) && !is_null($field['lotnumber'])) ||
                (!empty($field['product_line']) && !is_null($field['product_line'])) ||
                (!empty($field['jo_no']) && !is_null($field['jo_no'])) ) {

                //$uom = preg_replace('/[0-9]+/', '', strtoupper($field['uom']));

                $checkProdCode = PpcProductCode::where('product_code',$field['itemcode'])->count();  
                if ($checkProdCode == 0) {

                    $prod =  DB::table('ppc_dropdown_items')
                                ->select([
                                    'dropdown_item as product_line',
                                ])
                                ->where([
                                    ['dropdown_name_id', 7],
                                    ['dropdown_item', strtoupper($field['product_line'])]
                                ]) 
                                ->first();

                    NotRegisteredMaterial::where('item_code',$field['itemcode'])
                                        ->where('heat_no',$field['heatnumber'])
                                        ->delete();

                    $iclass = (mb_substr($field['itemcode'], 0, 1, "UTF-8") == 'Y')? 'CRUDE': 'FINISHED';

                    NotRegisteredMaterial::insert([
                        'item_class' => $iclass,
                        'jo_no' => strtoupper($field['jo_no']),
                        'product_line' => (isset($prod->product_line))? $prod->product_line: strtoupper($field['product_line']),
                        'item_code' => strtoupper($field['itemcode']),
                        'qty_weight' => $field['qty_weight'],
                        'qty_pcs' => $field['qty_pcs'],
                        'heat_no' => strtoupper($field['heatnumber']),
                        'lot_no' => strtoupper($field['lotnumber']),
                        'invoice_no' => 'N/A',
                        'supplier' => 'N/A',
                        'supplier_heat_no' => 'N/A',
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s"),
                        'create_user' => Auth::user()->id,
                        'update_user' => Auth::user()->id
                    ]);
                } else {
                    $countAdded++;
                    $received_id = 0;

                    $PpcProductCode = PpcProductCode::select('product_type',
                                                               'code_description',
                                                               'item',
                                                               'alloy',
                                                               'class',
                                                               'size' )
                                                        ->where('product_code',$field['itemcode'])
                                                        ->first();


                        $iclass = (mb_substr($field['itemcode'], 0, 1, "UTF-8") == 'Y')? 'CRUDE': 'FINISHED';
                    

                        $received_id = PpcUpdateInventory::insertGetId([
                                        'item_class' => $iclass,
                                        'jo_no' => strtoupper($field['jo_no']),
                                        'product_line' =>   $PpcProductCode->product_type,
                                        'item_code' => strtoupper($field['itemcode']),
                                        'description' =>  $PpcProductCode->code_description,
                                        'item' =>  $PpcProductCode->item,
                                        'alloy' =>  $PpcProductCode->alloy,
                                        'schedule' => $PpcProductCode->class,
                                        'size' =>  $PpcProductCode->size,
                                        'qty_weight' => $field['qty_weight'],
                                        'qty_pcs' => $field['qty_pcs'],
                                        'weight_uom' => 'KGS',
                                        'pcs_uom' => 'PCS',
                                        'heat_no' => strtoupper($field['heatnumber']),
                                        'lot_no' => strtoupper($field['lotnumber']),
                                        'invoice_no' => 'N/A',
                                        //'received_date' => DATE_FORMAT($field['receiveddate'], 'Y-m-d').' '.date('H:i:s'),
                                        'supplier' => strtoupper($field['supplier']),
                                        'width' => 'N/A',
                                        'length' => 'N/A',
                                        'supplier_heat_no' => 'N/A',
                                        'created_at' => date("Y-m-d H:i:s"),
                                        'updated_at' => date("Y-m-d H:i:s"),
                                        'create_user' => Auth::user()->id,
                                        'update_user' => Auth::user()->id,
                                        'mode' => 'Inserted from Upload'
                                    ]);

                        Inventory::insert([
                            'item_class' => $iclass,
                            'jo_no' => strtoupper($field['jo_no']),
                            'product_line' =>   $PpcProductCode->product_type,
                            'item_code' => strtoupper($field['itemcode']),
                            'description' =>  $PpcProductCode->code_description,
                            'item' =>  $PpcProductCode->item,
                            'alloy' =>  $PpcProductCode->alloy,
                            'schedule' => $PpcProductCode->class,
                            'size' =>  $PpcProductCode->size,
                            'width' => 'N/A',
                            'length' => 'N/A',
                            'orig_quantity' => $field['qty_pcs'],
                            'qty_weight' => $field['qty_weight'],
                            'qty_pcs' => $field['qty_pcs'],
                            'weight_uom' => 'KGS',
                            'pcs_uom' => 'PCS',
                            'heat_no' => strtoupper($field['heatnumber']),
                            'lot_no' => strtoupper($field['lotnumber']),
                            'invoice_no' => 'N/A',
                            // 'received_date' => DATE_FORMAT($field['receiveddate'], 'Y-m-d').' '.date('H:i:s'),
                            'supplier' => 'N/A',
                            'supplier_heat_no' => 'N/A',
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
        
        if($countAdded == 0){
            
            $data = [
                'msg' => "All of the Product codes / Product Lines are not yet registered.",
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
        $Datalist = DB::table('v_inventories')->where('user_id', Auth::user()->id);

        if ((int)$req->with_zero == 0) {
            $Datalist->where('current_stock','>',0);
        }
                                
        return $Datalist->get();
    }

    public function AddManual(Request $req)
    {
        switch ($req->item_class) {
            case 'RAW MATERIAL':
                return $this->saveMaterialInventory($req);
                break;
            
            default:
                return $this->saveProductInventory($req);
                break;
        }

        
    }

    public function saveMaterialInventory($req)
    {
        $result = "";
        if (isset($req->item_id)) {
            
            $this->validate($req, [
                'receiving_no' => 'required',
                'materials_type' => 'required',
                'item_code' => 'required',
                'qty_weight' => 'required|numeric',
                'qty_pcs' => 'required|numeric',
                'heat_no' => 'required',
                'invoice_no' => 'required',
                'received_date' => 'required',
                'supplier' => 'required'

            ]);

                $UP = PpcUpdateInventory::find($req->item_id);

                $UP->item_class = "RAW MATERIAL";
                $UP->receiving_no = strtoupper($req->receiving_no);
                $UP->received_date = $req->received_date.' '.date('H:i:s');
                $UP->materials_type = strtoupper($req->materials_type);
                $UP->item_code = strtoupper($req->item_code);
                $UP->description = strtoupper($req->description);
                $UP->item = strtoupper($req->item);
                $UP->alloy = strtoupper($req->alloy);
                $UP->schedule = strtoupper($req->schedule);
                $UP->size = strtoupper($req->size);
                $UP->width = (!is_null($req->width))? strtoupper($req->width): 'N/A';
                $UP->length = (!is_null($req->length))? strtoupper($req->length): 'N/A';
                $UP->qty_weight = $req->qty_weight;
                $UP->qty_pcs = $req->qty_pcs;
                $UP->weight_uom = 'KGS';
                $UP->pcs_uom = 'PCS';
                $UP->heat_no = strtoupper($req->heat_no);
                $UP->invoice_no = strtoupper($req->invoice_no);
                $UP->supplier = (!is_null($req->supplier))? strtoupper($req->supplier): 'N/A';
                $UP->supplier_heat_no = (!is_null($req->supplier_heat_no))? strtoupper($req->supplier_heat_no): 'N/A';
                $UP->update_user =  Auth::user()->id;
                $UP->mode = 'Updated from Manual';

                if ($UP->update()) {
                    Inventory::where('received_id',$req->item_id)
                            ->update([
                                'item_class' => "RAW MATERIAL",
                                'receiving_no' => strtoupper($req->receiving_no),
                                'received_date' => $req->received_date.' '.date('H:i:s'),
                                'materials_type' => strtoupper($req->materials_type),
                                'item_code' => strtoupper($req->item_code),
                                'description' => strtoupper($req->description),
                                'item' => strtoupper($req->item),
                                'alloy' => strtoupper($req->alloy),
                                'schedule' => strtoupper($req->schedule),
                                'size' => strtoupper($req->size),
                                'width' => (!is_null($req->width))? strtoupper($req->width): 'N/A',
                                'length' => (!is_null($req->length))? strtoupper($req->length): 'N/A',
                                'qty_weight' => $req->qty_weight,
                                'qty_pcs' => $req->qty_pcs,
                                'weight_uom' => 'KGS',
                                'pcs_uom' => 'PCS',
                                'heat_no' => strtoupper($req->heat_no),
                                'invoice_no' => strtoupper($req->invoice_no),
                                'supplier' => (!is_null($req->supplier))? strtoupper($req->supplier): 'N/A',
                                'supplier_heat_no' => (!is_null($req->supplier_heat_no))? strtoupper($req->supplier_heat_no): 'N/A',
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
                    'action' => 'Updated Inventory data ID: '.$req->item_id.',
                                Material Code: '.$req->item_code.' manually.',
                    'user' => Auth::user()->id
                ]);
        }else {
            $this->validate($req, [
                'receiving_no' => 'required',
                'materials_type' => 'required',
                'item_code' => 'required',
                'qty_weight' => 'required|numeric',
                'qty_pcs' => 'required|numeric',
                'heat_no' => 'required',
                'invoice_no' => 'required',
                'received_date' => 'required',
                'supplier' => 'required'
            ]);

            $received = PpcUpdateInventory::insertGetId([
                            'item_class' => "RAW MATERIAL",
                            'receiving_no' => strtoupper($req->receiving_no),
                            'received_date' => $req->received_date.' '.date('H:i:s'),
                            'materials_type' => strtoupper($req->materials_type),
                            'item_code' => strtoupper($req->item_code),
                            'description' => strtoupper($req->description),
                            'item' => strtoupper($req->item),
                            'alloy' => strtoupper($req->alloy),
                            'schedule' => strtoupper($req->schedule),
                            'size' => strtoupper($req->size),
                            'width' => (!is_null($req->width))? strtoupper($req->width): 'N/A',
                            'length' => (!is_null($req->length))? strtoupper($req->length): 'N/A',
                            'qty_weight' => $req->qty_weight,
                            'qty_pcs' => $req->qty_pcs,
                            'weight_uom' => 'KGS',
                            'pcs_uom' => 'PCS',
                            'heat_no' => strtoupper($req->heat_no),
                            'invoice_no' => strtoupper($req->invoice_no),
                            'supplier' => (!is_null($req->supplier))? strtoupper($req->supplier): 'N/A',
                            'supplier_heat_no' => (!is_null($req->supplier_heat_no))? strtoupper($req->supplier_heat_no): 'N/A',
                            'create_user' =>  Auth::user()->id,
                            'update_user' =>  Auth::user()->id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                            'mode' => 'Inserted from Manual'
                        ]);

            if ($received) {
                $inv = new Inventory();

                $inv->item_class = "RAW MATERIAL";
                $inv->receiving_no = strtoupper($req->receiving_no);
                $inv->received_date = $req->received_date.' '.date('H:i:s');
                $inv->materials_type = strtoupper($req->materials_type);
                $inv->item_code = strtoupper($req->item_code);
                $inv->description = strtoupper($req->description);
                $inv->item = strtoupper($req->item);
                $inv->alloy = strtoupper($req->alloy);
                $inv->schedule = strtoupper($req->schedule);
                $inv->size = strtoupper($req->size);
                $inv->width = (!is_null($req->width))? strtoupper($req->width): 'N/A';
                $inv->length = (!is_null($req->length))? strtoupper($req->length): 'N/A';
                $inv->orig_quantity = $req->qty_pcs;
                $inv->qty_weight = $req->qty_weight;
                $inv->qty_pcs = $req->qty_pcs;
                $inv->weight_uom = 'KGS';
                $inv->pcs_uom = 'PCS';
                $inv->heat_no = strtoupper($req->heat_no);
                $inv->invoice_no = strtoupper($req->invoice_no);
                $inv->supplier = (!is_null($req->supplier))? strtoupper($req->supplier): 'N/A';
                $inv->supplier_heat_no = (!is_null($req->supplier_heat_no))? strtoupper($req->supplier_heat_no): 'N/A';
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
                'action' => 'Inserted Inventory data Material Code: '.$req->item_code.' manually.',
                'user' => Auth::user()->id
            ]);
        }
           
        return response()->json(['msg'=>"Data was successfully saved.",'status' => 'success']);
    }

    public function saveProductInventory($req)
    {
        $result = "";
        if (isset($req->item_id)) {
            
            $this->validate($req, [
                'jo_no' => 'required',
                'product_line' => 'required',
                'item_code' => 'required',
                'qty_weight' => 'required|numeric',
                'qty_pcs' => 'required|numeric',
                'heat_no' => 'required',
                'lot_no' => 'required'

            ]);

                $UP = PpcUpdateInventory::find($req->item_id);

                $UP->item_class = $req->item_class;
                $UP->jo_no = strtoupper($req->jo_no);
                $UP->product_line = strtoupper($req->product_line);
                $UP->item_code = strtoupper($req->item_code);
                $UP->description = strtoupper($req->description);
                $UP->item = strtoupper($req->item);
                $UP->alloy = strtoupper($req->alloy);
                $UP->schedule = strtoupper($req->schedule);
                $UP->size = strtoupper($req->size);
                $UP->width = 'N/A';
                $UP->length = 'N/A';
                $UP->qty_weight = $req->qty_weight;
                $UP->qty_pcs = $req->qty_pcs;
                $UP->weight_uom = 'KGS';
                $UP->pcs_uom = 'PCS';
                $UP->heat_no = strtoupper($req->heat_no);
                $UP->lot_no = strtoupper($req->lot_no);
                $UP->invoice_no = 'N/A';
                $UP->supplier = 'N/A';
                $UP->supplier_heat_no = 'N/A';
                $UP->update_user =  Auth::user()->id;
                $UP->mode = 'Updated from Manual';

                if ($UP->update()) {
                    Inventory::where('received_id',$req->item_id)
                            ->update([
                                'item_class' => $req->item_class,
                                'jo_no' => strtoupper($req->jo_no),
                                'product_line' => strtoupper($req->product_line),
                                'item_code' => strtoupper($req->item_code),
                                'description' => strtoupper($req->description),
                                'item' => strtoupper($req->item),
                                'alloy' => strtoupper($req->alloy),
                                'schedule' => strtoupper($req->schedule),
                                'size' => strtoupper($req->size),
                                'width' => (!is_null($req->width))? strtoupper($req->width): 'N/A',
                                'length' => (!is_null($req->length))? strtoupper($req->length): 'N/A',
                                'qty_weight' => $req->qty_weight,
                                'qty_pcs' => $req->qty_pcs,
                                'weight_uom' => 'KGS',
                                'pcs_uom' => 'PCS',
                                'heat_no' => strtoupper($req->heat_no),
                                'lot_no' => strtoupper($req->lot_no),
                                'invoice_no' => 'N/A',
                                //'received_date' => $req->received_date.' '.date('H:i:s'),
                                'supplier' => 'N/A',
                                'supplier_heat_no' => 'N/A',
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
                    'action' => 'Updated Inventory data ID: '.$req->item_id.',
                                Material Code: '.$req->item_code.' manually.',
                    'user' => Auth::user()->id
                ]);
        }else {
            $this->validate($req, [
                'jo_no' => 'required',
                'product_line' => 'required',
                'item_code' => 'required',
                'qty_weight' => 'required|numeric',
                'qty_pcs' => 'required|numeric',
                'heat_no' => 'required',
                'lot_no' => 'required'
            ]);

            $received = PpcUpdateInventory::insertGetId([
                            'item_class' => $req->item_class,
                            'jo_no' => strtoupper($req->jo_no),
                            'product_line' => strtoupper($req->product_line),
                            'item_code' => strtoupper($req->item_code),
                            'description' => strtoupper($req->description),
                            'item' => strtoupper($req->item),
                            'alloy' => strtoupper($req->alloy),
                            'schedule' => strtoupper($req->schedule),
                            'size' => strtoupper($req->size),
                            'width' => (!is_null($req->width))? strtoupper($req->width): 'N/A',
                            'length' => (!is_null($req->length))? strtoupper($req->length): 'N/A',
                            'qty_weight' => $req->qty_weight,
                            'qty_pcs' => $req->qty_pcs,
                            'weight_uom' => 'KGS',
                            'pcs_uom' => 'PCS',
                            'heat_no' => strtoupper($req->heat_no),
                            'lot_no' => strtoupper($req->lot_no),
                            'invoice_no' => 'N/A',
                            // 'received_date' => $req->received_date.' '.date('H:i:s'),
                            'supplier' => 'N/A',
                            'supplier_heat_no' => 'N/A',
                            'create_user' =>  Auth::user()->id,
                            'update_user' =>  Auth::user()->id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                            'mode' => 'Inserted from Manual'
                        ]);

            if ($received) {
                $inv = new Inventory();

                $inv->item_class = $req->item_class;
                $inv->jo_no = strtoupper($req->jo_no);
                $inv->product_line = strtoupper($req->product_line);
                $inv->item_code = strtoupper($req->item_code);
                $inv->description = strtoupper($req->description);
                $inv->item = strtoupper($req->item);
                $inv->alloy = strtoupper($req->alloy);
                $inv->schedule = strtoupper($req->schedule);
                $inv->size = strtoupper($req->size);
                $inv->width = (!is_null($req->width))? strtoupper($req->width): 'N/A';
                $inv->length = (!is_null($req->length))? strtoupper($req->length): 'N/A';
                $inv->orig_quantity = $req->qty_pcs;
                $inv->qty_weight = $req->qty_weight;
                $inv->qty_pcs = $req->qty_pcs;
                $inv->weight_uom = 'KGS';
                $inv->pcs_uom = 'PCS';
                $inv->heat_no = strtoupper($req->heat_no);
                $inv->lot_no = strtoupper($req->lot_no);
                $inv->invoice_no = 'N/A';
                //$inv->received_date = $req->received_date.' '.date('H:i:s');
                $inv->supplier = 'N/A';
                $inv->supplier_heat_no = 'N/A';
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
                'action' => 'Inserted Inventory data Material Code: '.$req->item_code.' manually.',
                'user' => Auth::user()->id
            ]);
        }
           
        return response()->json(['msg'=>"Data was successfully saved.",'status' => 'success']);
    }

    public function getProductLine()
    {
        $data = DB::table('ppc_dropdown_items as pdt')
                    ->leftjoin('admin_assign_production_lines as apl', 'apl.product_line', '=', 'pdt.dropdown_item')
                    ->select([
                        'apl.product_line as id',
                        'apl.product_line as text',
                    ])
                    ->where('pdt.dropdown_name_id', 7) // material type
                    ->where('apl.user_id' , Auth::user()->id)
                    ->groupBy('apl.product_line')
                    ->get();

                    // DB::table('ppc_material_codes as pmc')
                    // ->leftjoin('admin_assign_production_lines as apl', 'apl.product_line', '=', 'pmc.material_type')
                    // ->select(['pmc.material_type as material_type'])
                    // ->where('apl.user_id' ,Auth::user()->id)
                    // ->groupBy('pmc.material_type')->get();

        return $data;
    }

    public function GetMaterialType()
    {
        $data = DB::table('ppc_dropdown_items as pdt')
                    ->leftjoin('admin_assign_production_lines as apl', 'apl.product_line', '=', 'pdt.dropdown_item')
                    ->select([
                        'apl.product_line as id',
                        'apl.product_line as text'
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

        return $data;
    }

    public function GetItemCode(Request $req)
    {
        $code = [];
        if ($req->item_class == 'material') {
            $code = DB::select("select pmc.material_code as id,
                                    pmc.material_code as `text`,
                                    pmc.material_type as material_type,
                                    apl.user_id
                            from ppc_material_codes as pmc
                            left join admin_assign_production_lines as apl
                            on apl.product_line = pmc.material_type
                            where apl.user_id = '".Auth::user()->id."'
                            and pmc.material_type = '".$req->type."'
                            order by pmc.id desc");
        } else {
            $code_type = " AND LEFT(ppc.product_code,1) = 'Z'";
            if ($req->item_class == 'crude') {
                $code_type = " AND LEFT(ppc.product_code,1) = 'Y'";
            }
            $code = DB::select("select ppc.product_code as id,
                                    ppc.product_code as `text`,
                                    ppc.product_type as product_type,
                                    apl.user_id
                            from ppc_product_codes as ppc
                            left join admin_assign_production_lines as apl
                            on apl.product_line = ppc.product_type
                            where apl.user_id = '".Auth::user()->id."' ".$code_type."
                            and ppc.product_type = '".$req->type."'
                            order by ppc.id desc");
        }

        return $code;
        // return $data = [
        //             'code' => $code,
        //         ];
    }

    public function GetItemCodeDetails(Request $req)
    {
        $data = [];
        if ($req->item_class == 'material') {
            $data = PpcMaterialCode::select('code_description',
                                    'item',
                                    'alloy',
                                    'schedule',
                                    'size')
                ->where('material_code', $req->item_code)
                ->first();
        } else {
            $data = PpcProductCode::select('code_description',
                                    'item',
                                    'alloy',
                                    'class as schedule',
                                    'size',
                                    'finish_weight')
                ->where('product_code', $req->item_code)
                ->first();
        }
        
        return response()->json($data);
    }

    public function unRegisteredMaterials()
    {
        $none = NotRegisteredMaterial::select(
                                        'item_class',
                                        DB::raw("ifnull(receiving_no,jo_no) as receive_jo_no"),
                                        DB::raw("ifnull(materials_type,product_line) as item_type_line"),
                                        'item_code',
                                        'qty_weight',
                                        'qty_pcs',
                                        'heat_no',
                                        'lot_no',
                                        'invoice_no',
                                        'received_date',
                                        'supplier',
                                        'width',
                                        'length',
                                        'supplier_heat_no',
                                        DB::raw("left(created_at,10) as created_at")
                                    )
                                    ->where('create_user',Auth::user()->id)
                                    ->groupBy(
                                        'item_class',
                                        DB::raw("ifnull(receiving_no,jo_no)"),
                                        DB::raw("ifnull(materials_type,product_line)"),
                                        'item_code',
                                        'qty_weight',
                                        'qty_pcs',
                                        'heat_no',
                                        'lot_no',
                                        'invoice_no',
                                        'received_date',
                                        'supplier',
                                        'width',
                                        'length',
                                        'supplier_heat_no',
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
                                        'item_code',
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
                                        'item_code',
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
                    $sheet->cell('A'.$row, $dt->item_code);
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

    public function downloadExcelMaterialFormat()
    {
        $date = date('Ymd');
        Excel::create('MATERIAL_FORMAT_'.$date, function($excel)
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
                $sheet->cell('C1', "itemcode");
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

    public function downloadExcelProductFormat()
    {
        $date = date('Ymd');
        Excel::create('PRODUCT_FORMAT_'.$date, function($excel)
        {
            $excel->sheet('products', function($sheet)
            {
                $sheet->setHeight(6, 15);
                $sheet->cells('A1:R1', function($cells) {
                    $cells->setFont([
                        'family'     => 'Calibri',
                        'size'       => '11',
                        'bold'       =>  true,
                    ]);
                });
                $sheet->cell('A1', "jo_no");
                $sheet->cell('B1', "product_line");
                $sheet->cell('C1', "itemcode");
                $sheet->cell('D1', "description");
                $sheet->cell('E1', "item");
                $sheet->cell('F1', "alloy");
                $sheet->cell('G1', "class");
                $sheet->cell('H1', "size");
                $sheet->cell('I1', "qty_weight");
                $sheet->cell('J1', "qty_pcs");
                $sheet->cell('K1', "heatnumber");
                $sheet->cell('L1', "lotnumber");
                
            });
        })->download('xlsx');
    }

    public function searchFilter(Request $req)
    {
        return response()->json($this->getFilteredData($req));
    }

    private function getFilteredData($req)
    {
        $srch_item_class = "";
        $srch_received_date = "";
        $srch_receiving_no = "";
        $srch_jo_no = "";
        $srch_materials_type = "";
        $srch_product_line = "";
        $srch_item_code = "";
        $srch_item = "";
        $srch_alloy = "";
        $srch_schedule = "";
        $srch_size = "";
        $srch_width = "";
        $srch_length = "";
        $srch_heat_no = "";
        $srch_lot_no = "";
        $srch_invoice_no = "";
        $srch_supplier = "";
        $srch_supplier_heat_no = "";

        if (!is_null($req->srch_received_date_from) && !is_null($req->srch_received_date_to)) {
            $srch_received_date = " AND DATE_FORMAT(pui.received_date,'%Y-%m-%d') BETWEEN '".$req->srch_received_date_from."' AND '".$req->srch_received_date_to."'";
        }

        if (!is_null($req->srch_item_class)) {
            $equal = "= ";
            $_value = $req->srch_item_class;

            if (Str::contains($req->srch_item_class, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_item_class);
            }
            $srch_item_class = " AND item_class ".$equal."'".$_value."'";
        }

        if (!is_null($req->srch_jo_no)) {
            $equal = "= ";
            $_value = $req->srch_jo_no;

            if (Str::contains($req->srch_jo_no, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_jo_no);
            }
            $srch_jo_no = " AND receive_jo_no ".$equal."'".$_value."'";
        }

        if (!is_null($req->srch_receiving_no)) {
            $equal = "= ";
            $_value = $req->srch_receiving_no;

            if (Str::contains($req->srch_receiving_no, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_receiving_no);
            }
            $srch_receiving_no = " AND receive_jo_no ".$equal."'".$_value."'";
        }

        if (!is_null($req->srch_materials_type)) {
            $equal = "= ";
            $_value = $req->srch_materials_type;
            
            if (Str::contains($req->srch_materials_type, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_materials_type);
            }
            $srch_materials_type = " AND item_type_line ".$equal." '".$_value."'";
        }

        if (!is_null($req->srch_product_line)) {
            $equal = "= ";
            $_value = $req->srch_product_line;
            
            if (Str::contains($req->srch_product_line, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_product_line);
            }
            $srch_product_line = " AND item_type_line ".$equal." '".$_value."'";
        }

        if (!is_null($req->srch_item_code)) {
            $equal = "= ";
            $_value = $req->srch_item_code;
            
            if (Str::contains($req->srch_item_code, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_item_code);
            }
            $srch_item_code = " AND item_code ".$equal." '".$_value."'";
        }

        if (!is_null($req->srch_item)) {
            $equal = "= ";
            $_value = $req->srch_item;
            
            if (Str::contains($req->srch_item, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_item);
            }
            $srch_item = " AND item ".$equal." '".$_value."'";
        }

        if (!is_null($req->srch_alloy)) {
            $equal = "= ";
            $_value = $req->srch_alloy;
            
            if (Str::contains($req->srch_alloy, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_alloy);
            }
            $srch_alloy = " AND alloy ".$equal." '".$_value."'";
        }

        if (!is_null($req->srch_schedule)) {
            $equal = "= ";
            $_value = $req->srch_schedule;
            
            if (Str::contains($req->srch_schedule, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_schedule);
            }
            $srch_schedule = " AND schedule ".$equal." '".$_value."'";
        }

        if (!is_null($req->srch_size)) {
            $equal = "= ";
            $_value = $req->srch_size;
            
            if (Str::contains($req->srch_size, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_size);
            }
            $srch_size = " AND size ".$equal." '".$_value."'";
        }

        if (!is_null($req->srch_width)) {
            $equal = "= ";
            $_value = $req->srch_width;
            
            if (Str::contains($req->srch_width, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_width);
            }
            $srch_width = " AND width ".$equal." '".$_value."'";
        }

        if (!is_null($req->srch_length)) {
            $equal = "= ";
            $_value = $req->srch_length;
            
            if (Str::contains($req->srch_length, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_length);
            }
            $srch_length = " AND `length` ".$equal." '".$_value."'";
        }

        if (!is_null($req->srch_heat_no)) {
            $equal = "= ";
            $_value = $req->srch_heat_no;
            
            if (Str::contains($req->srch_heat_no, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_heat_no);
            }
            $srch_heat_no = " AND heat_no ".$equal." '".$_value."'";
        }

        if (!is_null($req->srch_lot_no)) {
            $equal = "= ";
            $_value = $req->srch_lot_no;
            
            if (Str::contains($req->srch_lot_no, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_lot_no);
            }
            $srch_lot_no = " AND lot_no ".$equal." '".$_value."'";
        }

        if (!is_null($req->srch_invoice_no)) {
            $equal = "= ";
            $_value = $req->srch_invoice_no;
            
            if (Str::contains($req->srch_invoice_no, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_invoice_no);
            }
            $srch_invoice_no = " AND invoice_no ".$equal." '".$_value."'";
        }

        if (!is_null($req->srch_supplier)) {
            $equal = "= ";
            $_value = $req->srch_supplier;
            
            if (Str::contains($req->srch_supplier, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_supplier);
            }
            $srch_supplier = " AND supplier ".$equal." '".$_value."'";
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

        $data = DB::table('v_inventories')
                    ->whereRaw('1=1 '.$srch_item_class
                        .$srch_received_date
                        .$srch_receiving_no
                        .$srch_jo_no
                        .$srch_materials_type
                        .$srch_product_line
                        .$srch_item_code
                        .$srch_item
                        .$srch_alloy
                        .$srch_schedule
                        .$srch_size
                        .$srch_width
                        .$srch_length
                        .$srch_heat_no
                        .$srch_lot_no
                        .$srch_invoice_no
                        .$srch_supplier
                        .$srch_supplier_heat_no)
                    ->where('user_id' ,Auth::user()->id)->get();
                        
        return $data;
    }

    public function downloadExcelSearchFilter(Request $req)
    {
        $data = $this->getFilteredData($req);
        $date = date('Ymd');

        Excel::create('Inventory_'.$date, function($excel) use($data,$req)
        {
            $excel->sheet('Report', function($sheet) use($data,$req)
            {

                $sheet->setHeight(4, 20);
                $sheet->mergeCells('A2:S2');
                $sheet->cells('A2:S2', function($cells) {
                    $cells->setAlignment('center');
                    $cells->setFont([
                        'family'     => 'Calibri',
                        'size'       => '14',
                        'bold'       =>  true,
                        'underline'  =>  true
                    ]);
                });
                $sheet->cell('A2',"INVENTORY");

                $sheet->setHeight(6, 15);
                $sheet->cells('A4:S4', function($cells) {
                    $cells->setFont([
                        'family'     => 'Calibri',
                        'size'       => '11',
                        'bold'       =>  true,
                    ]);
                    // Set all borders (top, right, bottom, left)
                    $cells->setBorder('solid', 'solid', 'solid', 'solid');
                });

                $sheet->cell('A4', function($cell) use($req) {
                    if ($req->item_class == 'RAW MATERIAL') {
                        $cell->setValue("Receiving No.");
                    } else {
                        $cell->setValue("J.O. No.");
                    }
                    
                    $cell->setBorder('thin','thin','thin','thin');
                });

                $sheet->cell('B4', function($cell) use($req) {
                    if ($req->item_class == 'RAW MATERIAL') {
                        $cell->setValue("Material Type");
                    } else {
                        $cell->setValue("Product Line");
                    }
                    
                    $cell->setBorder('thin','thin','thin','thin');
                });

                $sheet->cell('C4', function($cell) {
                    $cell->setValue("Item Code");
                    $cell->setBorder('thin','thin','thin','thin');
                });

                $sheet->cell('D4', function($cell) {
                    $cell->setValue("Description");
                    $cell->setBorder('thin','thin','thin','thin');
                });

                $sheet->cell('E4', function($cell) {
                    $cell->setValue("Item");
                    $cell->setBorder('thin','thin','thin','thin');
                });

                $sheet->cell('F4', function($cell) {
                    $cell->setValue("Alloy");
                    $cell->setBorder('thin','thin','thin','thin');
                });

                $sheet->cell('G4', function($cell) use($req) {
                    if ($req->srch_item_class == 'RAW MATERIAL') {
                        $cell->setValue("Schedule");
                    } else {
                        $cell->setValue("Class");
                    }
                    
                    $cell->setBorder('thin','thin','thin','thin');
                });

                $sheet->cell('H4', function($cell) {
                    $cell->setValue("Size");
                    $cell->setBorder('thin','thin','thin','thin');
                });

                $sheet->cell('I4', function($cell) {
                    $cell->setValue("Length");
                    $cell->setBorder('thin','thin','thin','thin');
                });

                $sheet->cell('J4', function($cell) {
                    $cell->setValue("Width");
                    $cell->setBorder('thin','thin','thin','thin');
                });

                $sheet->cell('K4', function($cell) {
                    $cell->setValue("QTY(Weight)");
                    $cell->setBorder('thin','thin','thin','thin');
                });

                $sheet->cell('L4', function($cell) {
                    $cell->setValue("QTY(Pcs)");
                    $cell->setBorder('thin','thin','thin','thin');
                });

                $sheet->cell('M4', function($cell) {
                    $cell->setValue("Current Stock");
                    $cell->setBorder('thin','thin','thin','thin');
                });

                $sheet->cell('N4', function($cell) {
                    $cell->setValue("Heat No.");
                    $cell->setBorder('thin','thin','thin','thin');
                });

                $sheet->cell('O4', function($cell) {
                    $cell->setValue("Lot No.");
                    $cell->setBorder('thin','thin','thin','thin');
                });

                $sheet->cell('P4', function($cell) {
                    $cell->setValue("Invoice No.");
                    $cell->setBorder('thin','thin','thin','thin');
                });

                $sheet->cell('Q4', function($cell) {
                    $cell->setValue("Supplier");
                    $cell->setBorder('thin','thin','thin','thin');
                });

                $sheet->cell('R4', function($cell) {
                    $cell->setValue("Supplier Heat No.");
                    $cell->setBorder('thin','thin','thin','thin');
                });

                $sheet->cell('S4', function($cell) {
                    $cell->setValue("Received Date");
                    $cell->setBorder('thin','thin','thin','thin');
                });

                $row = 5;

                foreach ($data as $key => $dt) {
                    $sheet->setHeight($row, 15);

                    $sheet->cell('A'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->receive_jo_no);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('B'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->item_type_line);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('C'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->item_code);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('D'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->description);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('E'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->item);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('F'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->alloy);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('G'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->schedule);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('H'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->size);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('I'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->length);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('J'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->width);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('K'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->qty_weight);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('L'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->qty_pcs);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('M'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->current_stock);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('N'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->heat_no);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('O'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->lot_no);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('P'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->invoice_no);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('Q'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->supplier);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('R'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->supplier_heat_no);
                        $cell->setBorder('thin','thin','thin','thin');
                    });

                    $sheet->cell('S'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->received_date);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    
                    $row++;
                }
            });
        })->download('xlsx');
    }
}