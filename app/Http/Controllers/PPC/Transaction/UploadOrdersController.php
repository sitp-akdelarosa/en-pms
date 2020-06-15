<?php

namespace App\Http\Controllers\PPC\Transaction;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use DataTables;
use App\PpcProductCode;
use App\PpcUploadOrder;
use App\PpcProductionSummary;
use App\NotRegisteredProduct;
use Excel;
use DB;

class UploadOrdersController extends Controller
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
        return view('ppc.transaction.upload-orders',['user_accesses' => $user_accesses]);
    }

    public function CheckFile(Request $req)
    {
        $file = $req->file('fileupload');
        $fields;
        Excel::load($file, function($reader) use(&$fields){
                    $fields = $reader->toArray();
                });
        if(isset($fields[0]['scno']))
        {
            $line = 1;
            foreach ($fields as $key => $field) {
                $line++;
                if ($field['scno'] == null ||
                    $field['productcode'] == null ||
                    $field['quantity'] == null ||
                    $field['pono'] == null) {

                    // $data = [
                    //         'msg' => "File had an empty value in line ".$line,
                    //         'status' => 'warning',
                    //         'fields' => $fields
                    //     ];
                    // return response()->json($data);
                }
                $num = $field['quantity'];
                if($field['quantity'] !== null && filter_var($num, FILTER_VALIDATE_INT) === false){
                    $data = [
                        'msg' => 'Invalid input of Quantity in line '.$line,
                        'status' => 'warning',
                        ];
                    return response()->json($data);
                }
            }
        }
        return response()->json($fields);
    }

    public function UploadUP(Request $req)
    {
        $file = $req->file('fileupload');
        $fields;

        Excel::load($file, function($reader) use(&$fields){
            $fields = $reader->toArray();
        });

        PpcUploadOrder::where('create_user',Auth::user()->user_id)->delete();

        $for_overwrite = [];
        $Schedule = [];
        $not_registered = [];
        $countAddedRow = 0;

        foreach ($fields as $key => $field) {
            $prod = PpcProductCode::where('product_code',$field['productcode'])->count();
            if ($prod > 0) {
                if ($field['scno'] == null ||
                    $field['productcode'] == null ||
                    $field['quantity'] == null ||
                    $field['pono'] == null) {

                } else {
                    $countAddedRow++;
                    $description = PpcProductCode::select('code_description')->where('product_code',$field['productcode'])->first();
                    PpcUploadOrder::insert([
                        'sc_no' => $field['scno'],
                        'prod_code' => $field['productcode'],
                        'description' => $description->code_description,
                        'quantity' => $field['quantity'],
                        'po' => $field['pono'],
                        'date_upload' => date("Y-m-d H:i:s"),
                        'create_user' => Auth::user()->user_id,
                        'update_user' => Auth::user()->user_id,
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);
                }
                    
            } else {
                if ($field['scno'] == null ||
                    $field['productcode'] == null ||
                    $field['quantity'] == null ||
                    $field['pono'] == null) {

                } else {
                    $countAddedRow++;
                    $description = "Please register this code in Product Master.";
                    PpcUploadOrder::insert([
                        'sc_no' => $field['scno'],
                        'prod_code' => $field['productcode'],
                        'description' => $description,
                        'quantity' => $field['quantity'],
                        'po' => $field['pono'],
                        'date_upload' => date("Y-m-d H:i:s"),
                        'create_user' => Auth::user()->user_id,
                        'update_user' => Auth::user()->user_id,
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);

                    array_push($not_registered, [
                        'sc_no' => $field['scno'],
                        'prod_code' => $field['productcode'],
                        'quantity' => $field['quantity'],
                        'po' => $field['pono'],
                        'create_user' => Auth::user()->user_id,
                        'update_user' => Auth::user()->user_id,
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);
                }
                    
            }
        }

        $params = array_chunk($not_registered, 1000);

        foreach ($params as $param) {
            NotRegisteredProduct::insert($param);
        }

        $for_prod_sum = PpcUploadOrder::groupBy('sc_no','prod_code','description','po','date_upload')
                                ->where('create_user',Auth::user()->user_id)
                                ->select([
                                    'sc_no',
                                    'prod_code',
                                    'description',
                                    DB::raw('SUM(quantity) as quantity'),
                                    'po',
                                    DB::raw("DATE_FORMAT(date_upload, '%m/%d/%Y') as date_upload"),
                                ])
                                ->get();

        foreach ($for_prod_sum as $key => $field) {
            $overwrite = PpcProductionSummary::where('sc_no', $field->sc_no)
                    ->where('prod_code',$field->prod_code)
                    ->count();                         
            if($overwrite == 0){
                    PpcProductionSummary::insert([
                        'sc_no' => $field->sc_no,
                        'prod_code' =>$field->prod_code,
                        'description' =>$field->description,
                        'quantity' =>$field->quantity,
                        'po' =>$field->po,
                        'date_upload' => date("Y-m-d H:i:s"),
                        'create_user' => Auth::user()->user_id,
                        'update_user' => Auth::user()->user_id,
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s")
                    ]);
            }else{
                $NotScheduled = PpcProductionSummary::where('sc_no', $field->sc_no)
                        ->where('prod_code',$field->prod_code)
                        ->where('po' , $field->po)
                        ->where('sched_qty','==',0)
                        ->count();
                if($NotScheduled != 0){
                    $quantity = PpcProductionSummary::select('quantity')
                        ->where('sc_no', $field->sc_no)
                        ->where('prod_code',$field->prod_code)
                        ->where('quantity', '!=' , $field->quantity)
                        ->first();
                    if(isset($quantity->quantity)){  
                         array_push($for_overwrite, [
                        'sc_no' => $field->sc_no,
                        'prod_code' => $field->prod_code,
                        'oldquantity' => $quantity->quantity,
                        'quantity' => $field->quantity,
                        'po' =>$field->po
                        ]);
                    }
                }else{
                     array_push($Schedule, [
                    'sc_no' => $field->sc_no,
                    'prod_code' => $field->prod_code,
                    'quantity' => $field->quantity,
                    'po' =>$field->po
                    ]);
                }
            }
        }

        $this->_audit->insert([
            'user_type' => Auth::user()->user_type,
            'module' => 'Upload Orders Module',
            'action' => 'Uploaded Back Orders.',
            'user' => Auth::user()->user_id
        ]);
       
        $data = [
                'msg' => "Data was successfully saved.",
                'status' => 'success',
                'countAddedRow' => $countAddedRow,
                'not_registered' => $not_registered,
                'for_overwrite' => $for_overwrite,
                'Schedule' => $Schedule,
            ];

        return response()->json($data);
    }

    public function overwrite(Request $req)
    {
        $updated = false;
        foreach ($req->data as $key => $up) {
            PpcProductionSummary::where('sc_no', $up['sc_no'])
                                ->where('prod_code',$up['prod_code'])
                                ->where('po',$up['po'])
                                ->update(['quantity' => $up['quantity']]);
            $updated = true;
        }
        return response()->json($updated);
    }

    public function deletefromtemp(Request $request)
    {
        $tray = $request->tray;
        $traycount = $request->traycount;
        try {
            if($traycount > 0){
                PpcUploadOrder::wherein('id',$tray)->delete();
            }
            return 1;
        } catch(Exception $err) {
            return 2;
        }
    }

    public function DatatableUpload()
    {
        $Datalist = PpcUploadOrder::orderBy('id','DESC')
                                    ->where('create_user',Auth::user()->user_id)
                                    ->select(
                                        'id',
                                        'sc_no',
                                        'prod_code',
                                        'description',
                                        'quantity',
                                        'po',
                                        'date_upload'
                                    )->get();
        return response()->json($Datalist);
    }

    public function unRegisteredProducts()
    {
        $none = NotRegisteredProduct::select(
                                        "sc_no",
                                        "prod_code",
                                        "quantity",
                                        "po",
                                        DB::raw("left(created_at,10) as created_at")
                                    )
                                    ->where('create_user',Auth::user()->user_id)
                                    ->groupBy(
                                        "sc_no",
                                        "prod_code",
                                        "quantity",
                                        "po",
                                        DB::raw("left(created_at,10)")
                                    )
                                    ->get();
        return response()->json($none);
    }

    public function unRegisteredProductsExcel()
    {
        $date = date('Y-m-d');
        $data = NotRegisteredProduct::select(
                                        "sc_no",
                                        "prod_code",
                                        "quantity",
                                        "po",
                                        DB::raw("left(created_at,10) as created_at")
                                    )
                                    ->where('create_user',Auth::user()->user_id)
                                    ->groupBy(
                                        "sc_no",
                                        "prod_code",
                                        "quantity",
                                        "po",
                                        DB::raw("left(created_at,10)")
                                    )
                                    ->get();

        Excel::create('Unregistered_Products_'.$date, function($excel) use($data)
        {
            $excel->sheet('Report', function($sheet) use($data)
            {
                $sheet->setHeight(1, 15);
                $sheet->mergeCells('A1:E1');
                $sheet->cells('A1:E1', function($cells) {
                    $cells->setAlignment('center');
                    $cells->setFont([
                        'family'     => 'Calibri',
                        'size'       => '14',
                        'bold'       =>  true,
                    ]);
                });
                $sheet->cell('A1'," Upload Orders");

                $sheet->setHeight(2, 15);
                $sheet->mergeCells('A2:E2');
                $sheet->cells('A2:E2', function($cells) {
                    $cells->setAlignment('center');
                });

                $sheet->setHeight(4, 20);
                $sheet->mergeCells('A4:E4');
                $sheet->cells('A4:E4', function($cells) {
                    $cells->setAlignment('center');
                    $cells->setFont([
                        'family'     => 'Calibri',
                        'size'       => '14',
                        'bold'       =>  true,
                        'underline'  =>  true
                    ]);
                });
                $sheet->cell('A4',"UNREGISTERED PRODUCTS");

                $sheet->setHeight(6, 15);
                $sheet->cells('A6:E6', function($cells) {
                    $cells->setFont([
                        'family'     => 'Calibri',
                        'size'       => '11',
                        'bold'       =>  true,
                    ]);
                    // Set all borders (top, right, bottom, left)
                    $cells->setBorder('solid', 'solid', 'solid', 'solid');
                });
                $sheet->cell('A6', "SC No.");
                $sheet->cell('B6', "Product Code");
                $sheet->cell('C6', "Quantity");
                $sheet->cell('D6', "P.O. No.");
                $sheet->cell('E6', "Date Upload");

                $row = 7;

                foreach ($data as $key => $dt) {
                    $sheet->setHeight($row, 15);
                    $sheet->cell('A'.$row, $dt->sc_no);
                    $sheet->cell('B'.$row, $dt->prod_code);
                    $sheet->cell('C'.$row, $dt->quantity);
                    $sheet->cell('D'.$row, $dt->po);
                    $sheet->cell('E'.$row, $this->_helper->convertDate($dt->created_at,'Y-m-d'));
                    $row++;
                }
                
                $sheet->cells('A6:E'.$row, function($cells) {
                    $cells->setBorder('solid', 'solid', 'solid', 'solid');
                });
            });
        })->download('xlsx');
    }
}
