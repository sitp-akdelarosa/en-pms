<?php

namespace App\Http\Controllers\PPC\Transaction;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use App\PpcUpdateInventory;
use App\PpcMaterialCode;
use App\PpcDropdownItem;
use App\NotRegisteredMaterial;
use App\PpcMaterialAssembly;
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
        if(isset($fields[0]['materialscode']) and isset($fields[0]['quantity']) and isset($fields[0]['uom']) and isset($fields[0]['heatnumber']) && isset($fields[0]['receiveddate'])){
            foreach ($fields as $key => $field) {
                    $line++;
                    if($field['materialscode'] == '' || $field['quantity'] == ''||$field['uom'] == ''||$field['heatnumber'] == '' ||$field['receiveddate'] == ''||$field['invoiceno'] == ''||$field['supplier'] == ''){
                        // $data = ['status' => 'validateRequired','line' => $line];
                        // return response()->json($data);
                    }
                    if (DateTime::createFromFormat('Y-m-d G:i:s', $field['receiveddate']) === FALSE) {
                        $data = ['status' => 'heatnumber error','msg' => 'The '.$field['receiveddate'].' format from receive date is wrong'];
                        return response()->json($data);
                    }

                    if (in_array($field['heatnumber'], $heatnumber)){
                        $data = ['status' => 'heatnumber error','msg' => 'The '.$field['heatnumber'].' Heat Number is same in Excel File'];
                        return response()->json($data);
                    }
                    $heatnumber[] =  $field['heatnumber'];

                    $num = $field['quantity'];

                    if(filter_var($num, FILTER_VALIDATE_INT) === false){
                        $data = ['status' => 'not num'];
                        return response()->json($data);
                    }

                    // $check = PpcUpdateInventory::where('heat_no',$field['heatnumber'])
                    //                             ->where('materials_code','!=',$field['materialscode'])
                    //                             ->count();
                    // if ($check > 0) {
                    //     $data = ['status' => 'heatnumber error','msg' => 'The '.$field['heatnumber'].' Heat Number is already in the Upload Inventory'];
                    //     return response()->json($data);
                    // }

            }  
        }else{
            $data = [
                'status' => 'failed',
                'fields' => $fields
            ];
        }         
        return response()->json($data);
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
            if($field['materialscode'] !== '' || $field['quantity'] !== ''||$field['uom'] !== ''||$field['heatnumber'] !== '' ||$field['receiveddate'] !== ''||$field['invoiceno'] !== ''||$field['supplier'] !== '') {

                $uom = preg_replace('/[0-9]+/', '', strtoupper($field['uom']));

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
                        'materials_type' => (isset($mat->mat_type))? $mat->mat_type: strtoupper($field['materialstype']),
                        'materials_code' => strtoupper($field['materialscode']),
                        'quantity' => $field['quantity'],
                        'uom' => $uom,
                        'heat_no' => strtoupper($field['heatnumber']),
                        'invoice_no' => strtoupper($field['invoiceno']),
                        'received_date' => DATE_FORMAT($field['receiveddate'], 'Y-m-d'),
                        'supplier' => strtoupper($field['supplier']),
                        'width' => (isset($field['width']))? strtoupper($field['width']): 'N/A',
                        'length' => (isset($field['length']))? strtoupper($field['length']): 'N/A',
                        'supplier_heat_no' => (isset($field['supplierheatno']))? strtoupper($field['supplierheatno']): 'N/A',
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
                }else{
                    $countAdded++;
                    PpcUpdateInventory::where('materials_code',$field['materialscode'])
                                        ->where('heat_no',$field['heatnumber'])
                                        ->delete();
                    $PpcMaterialCode = PpcMaterialCode::select('material_type',
                                                               'code_description',
                                                               'item',
                                                               'alloy',
                                                               'schedule',
                                                               'size' )
                                                        ->where('material_code',$field['materialscode'])->first();
                    PpcUpdateInventory::insert([
                        'materials_type' =>   $PpcMaterialCode->material_type,
                        'materials_code' => strtoupper($field['materialscode']),
                        'description' =>  $PpcMaterialCode->code_description,
                        'item' =>  $PpcMaterialCode->item,
                        'alloy' =>  $PpcMaterialCode->alloy,
                        'schedule' => $PpcMaterialCode->schedule,
                        'size' =>  $PpcMaterialCode->size,
                        'quantity' => $field['quantity'],
                        'uom' => $uom,
                        'heat_no' => strtoupper($field['heatnumber']),
                        'invoice_no' => strtoupper($field['invoiceno']),
                        'received_date' => DATE_FORMAT($field['receiveddate'], 'Y-m-d'),
                        'supplier' => strtoupper($field['supplier']),
                        'width' => (isset($field['width']))? strtoupper($field['width']): 'N/A',
                        'length' => (isset($field['length']))? strtoupper($field['length']): 'N/A',
                        'supplier_heat_no' => (isset($field['supplierheatno']))? strtoupper($field['supplierheatno']): 'N/A',
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s"),
                        'create_user' => Auth::user()->id,
                        'update_user' => Auth::user()->id
                    ]);
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
        $with_zero = "and quantity > 0";

        if ($req->with_zero > 0) {
            $with_zero = "";
        }

        $Datalist = DB::table('ppc_update_inventories as pui')
                        ->leftjoin('admin_assign_production_lines as apl', 'apl.product_line', '=', 'pui.materials_type')
                        ->select(
                            'pui.id as id',
                            'pui.materials_type as materials_type',
                            'pui.materials_code as materials_code',
                            'pui.description as description',
                            'pui.item as item',
                            'pui.alloy as alloy',
                            'pui.schedule as schedule',
                            'pui.size as size',
                            'pui.quantity as quantity',
                            'pui.uom as uom',
                            'pui.heat_no as heat_no',
                            'pui.invoice_no as invoice_no',
                            'pui.received_date as received_date',
                            'pui.supplier as supplier',
                            DB::raw("IFNULL(pui.length,'') as length"),
                            DB::raw("IFNULL(pui.width,'') as width"),
                            DB::raw("CONCAT(pui.width,' ','x',' ',pui.length) as wxl"),
                            DB::raw("IFNULL(pui.supplier_heat_no,'') as supplier_heat_no")
                        )
                        ->whereRaw('1=1 '.$with_zero)
                        ->where('apl.user_id' ,Auth::user()->id)
                        ->orderBy('pui.id','desc')->get();
                        
        return response()->json($Datalist);
    }

    public function AddManual(Request $req)
    {
        $result = "";
        if (isset($req->material_id)) {
            $this->validate($req, [
                'materials_type' => 'required',
                'materials_code' => 'required',
                'heat_no' => 'required',
                'quantity' => 'required|numeric',
                'item' => 'required',
                // 'size' => 'required',
                'alloy' => 'required',
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

                $UP->materials_type = strtoupper($req->materials_type);
                $UP->materials_code = strtoupper($req->materials_code);
                $UP->description = strtoupper($req->description);
                $UP->item = strtoupper($req->item);
                $UP->alloy = strtoupper($req->alloy);
                $UP->schedule = strtoupper($req->schedule);
                $UP->size = strtoupper($req->size);
                $UP->width = strtoupper($req->width);
                $UP->length = strtoupper($req->length);
                $UP->quantity = $req->quantity;
                $UP->uom = strtoupper($req->uom);
                $UP->heat_no = strtoupper($req->heat_no);
                $UP->invoice_no = strtoupper($req->invoice_no);
                $UP->received_date = $req->received_date;
                $UP->supplier = strtoupper($req->supplier);
                $UP->supplier_heat_no = strtoupper($req->supplier_heat_no);
                $UP->create_user =  Auth::user()->id;
                $UP->update_user =  Auth::user()->id;
                $UP->update();
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
                'materials_type' => 'required',
                'materials_code' => 'required',
                'heat_no' => 'required',
                'quantity' => 'required|numeric',
                'item' => 'required',
                // 'size' => 'required',
                'alloy' => 'required',
                'heat_no' => 'required',
                'invoice_no' => 'required',
                'received_date' => 'required',
                'supplier' => 'required'
            ]);

            $UP = new PpcUpdateInventory();

            $UP->materials_type = strtoupper($req->materials_type);
            $UP->materials_code = strtoupper($req->materials_code);
            $UP->description = strtoupper($req->description);
            $UP->item = strtoupper($req->item);
            $UP->alloy = strtoupper($req->alloy);
            $UP->schedule = strtoupper($req->schedule);
            $UP->size = strtoupper($req->size);
            $UP->width = strtoupper($req->width);
            $UP->length = strtoupper($req->length);
            $UP->quantity = $req->quantity;
            $UP->uom = strtoupper($req->uom);
            $UP->heat_no = strtoupper($req->heat_no);
            $UP->invoice_no = strtoupper($req->invoice_no);
            $UP->received_date = $req->received_date;
            $UP->supplier = strtoupper($req->supplier);
            $UP->supplier_heat_no = strtoupper($req->supplier_heat_no);
            $UP->create_user =  Auth::user()->id;
            $UP->update_user =  Auth::user()->id;
            $UP->save();
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
                                        'materials_type',
                                        'materials_code',
                                        'quantity',
                                        'uom',
                                        'heat_no',
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
                                        'materials_type',
                                        'materials_code',
                                        'quantity',
                                        'uom',
                                        'heat_no',
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
                                        'materials_code',
                                        'quantity',
                                        'uom',
                                        'heat_no',
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
                                        'materials_code',
                                        'quantity',
                                        'uom',
                                        'heat_no',
                                        'invoice_no',
                                        'received_date',
                                        'supplier',
                                        'width',
                                        'length',
                                        'supplier_heat_no',
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
                $sheet->cell('B6', "Quantity");
                $sheet->cell('C6', "UOM");
                $sheet->cell('D6', "Heat No.");
                $sheet->cell('E6', "Invoice No.");
                $sheet->cell('F6', "Received Date");
                $sheet->cell('G6', "Supplier");
                $sheet->cell('H6', "width");
                $sheet->cell('I6', "Length");
                $sheet->cell('J6', "Supplier Heat No.");
                $sheet->cell('K6', "Date Uploaded");

                $row = 7;

                foreach ($data as $key => $dt) {
                    $sheet->setHeight($row, 15);
                    $sheet->cell('A'.$row, $dt->materials_code);
                    $sheet->cell('B'.$row, $dt->quantity);
                    $sheet->cell('C'.$row, $dt->uom);
                    $sheet->cell('D'.$row, $dt->heat_no);
                    $sheet->cell('E'.$row, $dt->invoice_no);
                    $sheet->cell('F'.$row, $dt->received_date);
                    $sheet->cell('G'.$row, $dt->supplier);
                    $sheet->cell('H'.$row, $dt->width);
                    $sheet->cell('I'.$row, $dt->length);
                    $sheet->cell('J'.$row, $dt->supplier_heat_no);
                    $sheet->cell('K'.$row, $this->_helper->convertDate($dt->created_at,'Y-m-d'));
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
                $sheet->cells('A1:P1', function($cells) {
                    $cells->setFont([
                        'family'     => 'Calibri',
                        'size'       => '11',
                        'bold'       =>  true,
                    ]);
                });
                $sheet->cell('A1', "materialstype");
                $sheet->cell('B1', "materialscode");
                $sheet->cell('C1', "description");
                $sheet->cell('D1', "item");
                $sheet->cell('E1', "alloy");
                $sheet->cell('F1', "schedule");
                $sheet->cell('G1', "size");
                $sheet->cell('H1', "quantity");
                $sheet->cell('I1', "uom");
                $sheet->cell('J1', "heatnumber");
                $sheet->cell('K1', "invoiceno");
                $sheet->cell('L1', "receiveddate");
                $sheet->cell('M1', "supplier");
                $sheet->cell('N1', "width");
                $sheet->cell('O1', "length");
                $sheet->cell('P1', "supplierheatno");
            });
        })->download('xlsx');
    }
}