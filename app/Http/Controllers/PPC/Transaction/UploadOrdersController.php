<?php

namespace App\Http\Controllers\PPC\Transaction;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use Illuminate\Support\Str;
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
    protected $_moduleID;

    public function __construct()
    {
        // $this->middleware('ajax-session-expired');
        // $this->middleware('auth');
        $this->_helper = new HelpersController;
        $this->_audit = new AuditTrailController;

        $this->_moduleID = $this->_helper->moduleID('T0002');
    }

    public function index()
    {
        $user_accesses = $this->_helper->UserAccess();
        $permission_access = $this->_helper->check_permission('T0002');

        return view('ppc.transaction.upload-orders', [
            'user_accesses' => $user_accesses,
            'permission_access' => $permission_access
        ]);
        return view('ppc.transaction.upload-orders',['user_accesses' => $user_accesses]);
    }

    public function CheckFile(Request $req)
    {
        $file = $req->file('fileupload');
        $fields = [];
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

        PpcUploadOrder::where('create_user',Auth::user()->id)->delete();

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
                        'uploader' => Auth::user()->id,
                        'date_upload' => date("Y-m-d H:i:s"),
                        'create_user' => Auth::user()->id,
                        'update_user' => Auth::user()->id,
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
                        'uploader' => Auth::user()->id,
                        'date_upload' => date("Y-m-d H:i:s"),
                        'create_user' => Auth::user()->id,
                        'update_user' => Auth::user()->id,
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);

                    array_push($not_registered, [
                        'sc_no' => $field['scno'],
                        'prod_code' => $field['productcode'],
                        'quantity' => $field['quantity'],
                        'po' => $field['pono'],
                        'uploader' => Auth::user()->id,
                        'create_user' => Auth::user()->id,
                        'update_user' => Auth::user()->id,
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
                                ->where('create_user',Auth::user()->id)
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
                        'create_user' => Auth::user()->id,
                        'update_user' => Auth::user()->id,
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
            'module_id' => $this->_moduleID,
            'module' => 'Upload Orders Module',
            'action' => 'Uploaded Back Orders.',
            'user' => Auth::user()->id,
            'fullname' => Auth::user()->firstname. ' ' .Auth::user()->lastname
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
                                ->update([
                                    'quantity' => $up['quantity'],
                                    'update_user' => Auth::user()->id,
                                    'updated_at' => date("Y-m-d H:i:s")
                                ]);
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
        $Datalist = DB::select(
            DB::raw("CALL GET_orders(". Auth::user()->id .")")
        );

        return DataTables::of($Datalist)
						->editColumn('id', function($data) {
							return $data->id;
						})
						->make(true);
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
                                    ->where('create_user',Auth::user()->id)
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
                                    ->where('create_user',Auth::user()->id)
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

    public function searchFilter(Request $req)
    {
        return DataTables::of($this->getFilteredOrders($req))
						->editColumn('id', function($data) {
							return $data->id;
						})
						->make(true);
    }

    private function getFilteredOrders($req)
    {
        $srch_date_upload = "";
        $srch_sc_no = "";
        $srch_prod_code = "";
        $srch_description = "";
        $srch_po = "";

        if (!is_null($req->srch_date_upload_from) && !is_null($req->srch_date_upload_to)) {
            $srch_date_upload = " AND DATE_FORMAT(date_upload,'%Y-%m-%d') BETWEEN '".$req->srch_date_upload_from."' AND '".$req->srch_date_upload_to."'";
        }

        if (!is_null($req->srch_sc_no)) {
            $equal = "= ";
            $_value = $req->srch_sc_no;

            if (Str::contains($req->srch_sc_no, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_sc_no);
            }
            $srch_sc_no = " AND sc_no ".$equal."'".$_value."'";
        }

        if (!is_null($req->srch_prod_code)) {
            $equal = "= ";
            $_value = $req->srch_prod_code;

            if (Str::contains($req->srch_prod_code, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_prod_code);
            }
            $srch_prod_code = " AND prod_code ".$equal."'".$_value."'";
        }

        if (!is_null($req->srch_description)) {
            $equal = "= ";
            $_value = $req->srch_description;

            if (Str::contains($req->srch_description, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_description);
            }
            $srch_description = " AND `description` ".$equal."'".$_value."'";
        }
        if (!is_null($req->srch_po)) {
            $equal = "= ";
            $_value = $req->srch_po;

            if (Str::contains($req->srch_po, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_po);
            }
            $srch_po = " AND po ".$equal."'".$_value."'";
        }

        $query = "SELECT uo.id,
                        uo.sc_no,
                        uo.prod_code,
                        uo.description,
                        uo.quantity,
                        uo.po,
                        u.nickname as uploader,
                        uo.date_upload 
                FROM enpms.ppc_upload_orders as uo
                inner join users as u
                on uo.uploader = u.id
                left join ppc_product_codes as ppc
                on ppc.product_code = uo.prod_code
                left join admin_assign_production_lines as apl
                on apl.product_line = ppc.product_type
                where apl.user_id = ".Auth::user()->id
                .$srch_date_upload.$srch_sc_no.$srch_prod_code.$srch_description.$srch_po.
                " group by uo.id,
                        uo.sc_no,
                        uo.prod_code,
                        uo.description,
                        uo.quantity,
                        uo.po,
                        u.nickname,
                        uo.date_upload
                order by uo.date_upload desc";

        $Datalist = DB::select($query);

        return $Datalist;
    }

    public function excelFilteredData(Request $req)
    {
        $data = $this->getFilteredOrders($req);
        $date = date('Ymd');

        Excel::create('UploadedOrders_'.$date, function($excel) use($data)
        {
            $excel->sheet('Summary', function($sheet) use($data)
            {
                $sheet->setHeight(4, 20);
                $sheet->mergeCells('A2:F2');
                $sheet->cells('A2:F2', function($cells) {
                    $cells->setAlignment('center');
                    $cells->setFont([
                        'family'     => 'Calibri',
                        'size'       => '14',
                        'bold'       =>  true,
                        'underline'  =>  true
                    ]);
                });
                $sheet->cell('A2',"Uploaded Orders Summary");

                $sheet->setHeight(6, 15);
                $sheet->cells('A4:F4', function($cells) {
                    $cells->setFont([
                        'family'     => 'Calibri',
                        'size'       => '11',
                        'bold'       =>  true,
                    ]);
                    // Set all borders (top, right, bottom, left)
                    $cells->setBorder('solid', 'solid', 'solid', 'solid');
                });

                $sheet->cell('A4', function($cell) {
                    $cell->setValue("SC No.");
                    $cell->setBorder('thick','thick','thick','thick');
                });
            
                $sheet->cell('B4', function($cell) {
                    $cell->setValue("Product Code");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('C4', function($cell) {
                    $cell->setValue("Description");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('D4', function($cell) {
                    $cell->setValue("Quantity");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('E4', function($cell) {
                    $cell->setValue("P.O.");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('F4', function($cell) {
                    $cell->setValue("Date Uploaded");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cells('A4:F4', function($cells) {
                    $cells->setBorder('thick', 'thick', 'thick', 'thick');
                });

                $row = 5;

                foreach ($data as $key => $dt) {
                    $sheet->setHeight($row, 15);

                    $sheet->cell('A'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->sc_no);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('B'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->prod_code);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('C'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->description);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('D'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->quantity);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('E'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->po);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('F'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->date_upload);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    
                    $row++;
                }
                
                $sheet->cells('A4:F'.$row, function($cells) {
                    $cells->setBorder('thick', 'thick', 'thick', 'thick');
                });
            });
        })->download('xlsx');
    }
}
