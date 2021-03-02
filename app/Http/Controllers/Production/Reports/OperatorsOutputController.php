<?php

namespace App\Http\Controllers\Production\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use DB;
use App\ProdProductionOutput;
use Excel;

class OperatorsOutputController extends Controller
{
    protected $_helper;
    protected $_audit;
    protected $_moduleID;
    private $_Operators_data;

    public function __construct()
    {
        // $this->middleware('auth');
        $this->_helper = new HelpersController;
        $this->_audit = new AuditTrailController;

        $this->_moduleID = $this->_helper->moduleID('R0004');
    }

    public function index()
    {
        $user_accesses = $this->_helper->UserAccess();
        $permission_access = $this->_helper->check_permission('R0004');

        return view('production.reports.operators-output', [
            'user_accesses' => $user_accesses,
            'permission_access' => $permission_access
        ]);
    }

    public function search_operator(Request $req)
    {
        $date_from = "NULL";
        $date_to = "NULL";
        $search_operator = "NULL";

       
        if (isset($req->date_from) && !isset($req->date_to)) {
            $data = [
                        'msg' => 'The Date to required if the Date from have value.',
                        'status' => 'failed'
                    ];

            return response()->json($data);
        }
        $prod_production_outputs=$this->GetOperationOuputList($req);

        $existing = ProdProductionOutput::where('operator',$req->search_operator)->get();
        if (count($prod_production_outputs) > 0) {
            $data = ['status' => 'success','ppo' => $prod_production_outputs];
        } elseif (count($existing) > 0) {

            if (count($prod_production_outputs) == 0) {
                $data = ['msg' => 'There are no existing data on that input date range','status' => 'failed'];
            } else {
                $this->$_Operators_data = $prod_production_outputs;
                $data = ['status' => 'success','ppo' => $prod_production_outputs];
            }

        } else {
                $data = ['msg' => 'No Operator ID existing.','status' => 'failed'];
        }

        return response()->json($data);
    }
    public function downloadExcel(Request $req)
    {
        $data=$this->GetOperationOuputList($req);
        $date = date('Ymd');
        Excel::create('OPERATORS_OUTPUT_'.$date, function($excel)use($data)
        {
            $excel->sheet('Operators Output', function($sheet) use($data)
            {
                $sheet->setHeight(4, 20);
                $sheet->mergeCells('A2:M2');
                $sheet->cells('A2:M2', function($cells) {
                    $cells->setAlignment('center');
                    $cells->setFont([
                        'family'     => 'Calibri',
                        'size'       => '14',
                        'bold'       =>  true,
                        'underline'  =>  true
                    ]);
                });
                $sheet->cell('A2',"Operator's  Output");

                $sheet->setHeight(6, 15);
                $sheet->cells('A4:M4', function($cells) {
                    $cells->setFont([
                        'family'     => 'Calibri',
                        'size'       => '11',
                        'bold'       =>  true,
                    ]);
                    // Set all borders (top, right, bottom, left)
                    $cells->setBorder('solid', 'solid', 'solid', 'solid');
                });

                $sheet->cell('A4', function($cell) {
                    $cell->setValue("#");
                    $cell->setBorder('thick','thick','thick','thick');
                });
            
                $sheet->cell('B4', function($cell) {
                    $cell->setValue("Operators ID");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('C4', function($cell) {
                    $cell->setValue("Operator Name");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('D4', function($cell) {
                    $cell->setValue("M/C");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('E4', function($cell) {
                    $cell->setValue("Process Date");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('F4', function($cell) {
                    $cell->setValue("Item Code");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('G4', function($cell) {
                    $cell->setValue("Descrption");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('H4', function($cell) {
                    $cell->setValue("Good");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('I4', function($cell) {
                    $cell->setValue("Reword");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('J4', function($cell) {
                    $cell->setValue("Scarp");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('K4', function($cell) {
                    $cell->setValue("Proccess");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('L4', function($cell) {
                    $cell->setValue("Heat No.");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('M4', function($cell) {
                    $cell->setValue("Remarks");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                

                $row = 5;

                foreach ($data as $key => $dt) {
                    $sheet->setHeight($row, 15);
                    $sheet->cell('A'.$row, function($cell) use($dt,$row) {
                        $cell->setValue($row - 4);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('B'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->UserID);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('C'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->UserName);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('D'.$row, function($cell) use($dt) {
                        // $cell->setValue($dt->mc);
                        $cell->setValue("");
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('E'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->updated_at);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('F'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->prod_code);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('G'.$row, function($cell) use($dt) {
                        // $cell->setValue($dt->Descrption);
                        $cell->setValue("");
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('H'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->good);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('I'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->rework);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('J'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->scrap);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('K'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->current_process);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('L'.$row, function($cell) use($dt) {
                        // $cell->setValue($dt->HeatNo);
                        $cell->setValue("");
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('M'.$row, function($cell) use($dt) {
                        // $cell->setValue($dt->Remarks);
                        $cell->setValue("");
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    
                    
                    $row++;
                }
                
                $sheet->cells('A4:M'.$row, function($cells) {
                    $cells->setBorder('thick', 'thick', 'thick', 'thick');
                });
            });
        })->download('xlsx');
    }
    public function GetOperationOuputList($req){
        $date_from = "";
        $date_to = "";
        $search_operator = "";

        if (!is_null($req->date_from) && !is_null($req->date_to)) {
            $date_from = "'".$req->date_from."'";
            $date_to = "'".$req->date_to."'";
        }

        if (!is_null($req->search_operator)) {
            $search_operator = "'".$req->search_operator."'";
        }

        $prod_production_outputs = DB::select(
                                        DB::raw(
                                            "CALL GET_operators_output(
                                                ".$search_operator.",
                                                ".$date_from.",
                                                ".$date_to."
                                            )"
                                        )
                                    );
        return $prod_production_outputs;                          
    }
}
