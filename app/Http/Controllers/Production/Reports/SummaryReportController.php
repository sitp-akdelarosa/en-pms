<?php

namespace App\Http\Controllers\Production\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use DB;
use Excel;

class SummaryReportController extends Controller
{
    protected $_helper;
    protected $_audit;
    protected $_moduleID;

    public function __construct()
    {
        // $this->middleware('auth');
        $this->_helper = new HelpersController;
        $this->_audit = new AuditTrailController;

        $this->_moduleID = $this->_helper->moduleID('R0005');
    }

    public function index()
    {
        $user_accesses = $this->_helper->UserAccess();
        $permission_access = $this->_helper->check_permission('R0005');

        return view('production.reports.summary-report', [
            'user_accesses' => $user_accesses,
            'permission_access' => $permission_access
        ]);
    }
    public function search_summart_report(Request $req)
    {
        $date_from = "NULL";
        $date_to = "NULL";
       
        if (isset($req->date_from) && !isset($req->date_to)) {
            $data = [
                        'msg' => 'The Date to required if the Date from have value.',
                        'status' => 'failed'
                    ];

            return response()->json($data);
        }
        $summart_report=$this->GetSummaryReportList($req);

        if (count($summart_report) > 0) {
            $data = ['status' => 'success','ppo' => $summart_report];
        } else {
                $data = ['msg' => 'No data in that date range.','status' => 'failed'];
        }

        return response()->json($data);
    }
    public function downloadExcel(Request $req)
    {
        $data=$this->GetSummaryReportList($req);
        $date = date('Ymd');
        Excel::create('PRODUCTION_SUMMARY_REPORT_'.$date, function($excel)use($data)
        {
            $excel->sheet('Production Summary Report', function($sheet) use($data)
            {
                $sheet->setHeight(4, 20);
                $sheet->mergeCells('A1:C1');
                $sheet->cells('A1:C1', function($cells) {
                    $cells->setAlignment('left');
                    $cells->setFont([
                        'family'     => 'Calibri',
                        'size'       => '14',
                        'bold'       =>  true
                    ]);
                });
                $sheet->cell('A1',"EN CORPORATION");

                $sheet->mergeCells('A4:A5');
                $sheet->mergeCells('B4:B5');
                $sheet->mergeCells('C4:C5');
                $sheet->mergeCells('D4:D5');
                $sheet->mergeCells('E4:E5');
                $sheet->mergeCells('F4:F5');
                $sheet->mergeCells('G4:G5');
                $sheet->mergeCells('H4:H5');
                $sheet->mergeCells('I4:L4');
                $sheet->mergeCells('M4:P4');
                $sheet->mergeCells('Q4:R4');
                $sheet->mergeCells('S4:S5');
                $sheet->setHeight(6, 15);
                $sheet->cells('A4:S5', function($cells) {
                    $cells->setFont([
                        'family'     => 'Calibri',
                        'size'       => '11',
                        'bold'       =>  true,
                    ]);
                    // Set all borders (top, right, bottom, left)
                    $cells->setBorder('solid', 'solid', 'solid', 'solid');
                });

                $sheet->cell('A4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("DATE");
                    $cell->setBorder('thick','thick','thick','thick');
                });
            
                $sheet->cell('B4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("M/C");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('C4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("CODE");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('D4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("ITEM");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('E4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("ALLOY");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('F4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("SIZE");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('G4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("CLASS");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('H4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("HEAT NO.");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('I4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("OUTPUT (QTY)");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('I5', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("TOTAL");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('J5', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("GOOD");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('K5', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("REWORK");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('L5', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("SCRAP");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('M4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("WEIGHT");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('M5', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("CRUDE WT.");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('N5', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("GOOD");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('O5', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("REWORK");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('P5', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("SCRAP");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('Q4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("REJECTION RATE");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('Q5', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("REWORK");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('R5', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("SCRAP");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('S4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("JOB ORDER NO.");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                

                $row = 6;

                foreach ($data as $key => $dt) {
                    $sheet->setHeight($row, 15);
                    $sheet->cell('A'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->date_upload);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('B'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->sc_no);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('C'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->prod_code);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('D'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->description);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('E'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->alloy);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('F'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->size);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('G'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->class);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('H'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->heatno);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('I'.$row, function($cell) use($dt,$row) {
                        $cell->setValue("=SUM(J" . $row .":L" . $row . ")");
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('J'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->good);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('K'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->rework);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('L'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->scrap);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('M'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->finish_weight);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('N'.$row, function($cell) use($dt,$row) {
                        $cell->setValue("=M". $row ."*I". $row);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('O'.$row, function($cell) use($dt,$row) {
                        $cell->setValue("=M". $row ."*K". $row);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('P'.$row, function($cell) use($dt,$row) {
                        $cell->setValue("=M". $row ."/L". $row);
                        $cell->setBorder('thin','thin','thin','thin');
                    });                    
                    $sheet->cell('Q'.$row, function($cell) use($dt,$row) {
                        $cell->setValue("=K". $row ."/I". $row);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('R'.$row, function($cell) use($dt,$row) {
                        $cell->setValue("=I". $row ."/L". $row);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('S'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->jono);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    
                    
                    $row++;
                }
                
                $sheet->cells('A4:H'.$row, function($cells) {
                    $cells->setBorder('thick', 'thick', 'thick', 'thick');
                });
                $sheet->cells('I4:L'.$row, function($cells) {
                    $cells->setBorder('thick', 'thick', 'thick', 'thick');
                });
                $sheet->cells('M4:P'.$row, function($cells) {
                    $cells->setBorder('thick', 'thick', 'thick', 'thick');
                });
                $sheet->cells('Q4:R'.$row, function($cells) {
                    $cells->setBorder('thick', 'thick', 'thick', 'thick');
                });
                $sheet->cells('S4:S'.$row, function($cells) {
                    $cells->setBorder('thick', 'thick', 'thick', 'thick');
                });
            });
        })->download('xlsx');
    }
    public function GetSummaryReportList($req){
        $date_from = "";
        $date_to = "";

        if (!is_null($req->date_from) && !is_null($req->date_to)) {
            $date_from = "'".$req->date_from."'";
            $date_to = "'".$req->date_to."'";
        }

        $summart_report = DB::select(
                                        DB::raw(
                                            "CALL GET_production_summaries_report()"
                                        )
                                    );
        return $summart_report;                          
    }
}
