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
    public function filterReport(Request $req)
    {
        return $this->getFilteredProuctionSummary($req);
    }

    public function downloadExcel(Request $req)
    {
        $data = $this->getFilteredProuctionSummary($req);

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
                $sheet->cell('A1',"PRODUCTION SUMMARY REPORT");

                $sheet->mergeCells('A4:A5');
                $sheet->mergeCells('B4:B5');
                $sheet->mergeCells('C4:C5');
                $sheet->mergeCells('D4:D5');
                $sheet->mergeCells('E4:E5');
                $sheet->mergeCells('F4:F5');
                $sheet->mergeCells('G4:G5');
                $sheet->mergeCells('H4:H5');
                $sheet->mergeCells('I4:I5');
                $sheet->mergeCells('J4:J5');
                $sheet->mergeCells('K4:K5');
                $sheet->mergeCells('L4:L5');

                $sheet->mergeCells('M4:P4');
                $sheet->mergeCells('Q4:T4');
                $sheet->mergeCells('U4:V4');

                $sheet->mergeCells('W4:W5');

                $sheet->setHeight(6, 15);

                $sheet->cells('A4:W5', function($cells) {
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

                $sheet->cell('D4', function ($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("DESCRIPTION");
                    $cell->setBorder('thick', 'thick', 'thick', 'thick');
                });

                $sheet->cell('E4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("ITEM");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('F4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("ALLOY");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('G4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("SIZE");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('H4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("CLASS");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('I4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("HEAT NO.");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('J4', function ($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("LOT NO.");
                    $cell->setBorder('thick', 'thick', 'thick', 'thick');
                });

                $sheet->cell('K4', function ($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("DIV NO.");
                    $cell->setBorder('thick', 'thick', 'thick', 'thick');
                });

                $sheet->cell('L4', function ($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("PROCESS");
                    $cell->setBorder('thick', 'thick', 'thick', 'thick');
                });

                // ====== outputs ======
                $sheet->cell('M4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("OUTPUT (QTY)");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                
                $sheet->cell('M5', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("TOTAL");
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

                // ====== weight ======
                $sheet->cell('Q4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("WEIGHT");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('Q5', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("UNIT WT.");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('R5', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("GOOD");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('S5', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("REWORK");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('T5', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("SCRAP");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                // ====== reject rate ======
                $sheet->cell('U4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("REJECTION RATE");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('U5', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("REWORK");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('V5', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("SCRAP");
                    $cell->setBorder('thick','thick','thick','thick');
                });


                $sheet->cell('W4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("JOB ORDER NO.");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                

                $row = 6;

                foreach ($data as $key => $dt) {
                    $sheet->setHeight($row, 15);
                    $sheet->cell('A'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->created_at);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('B'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->machine_no);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('C'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->prod_code);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('D'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->code_description);
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
                        $cell->setValue($dt->size);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('H'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->class);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('I'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->heat_no);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('J' . $row, function ($cell) use ($dt) {
                        $cell->setValue($dt->lot_no);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('K'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->div_code);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('L'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->process_name);
                        $cell->setBorder('thin','thin','thin','thin');
                    });

                    // output
                    $sheet->cell('M'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->total);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('N'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->good);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('O'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->rework);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('P'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->scrap);
                        $cell->setBorder('thin','thin','thin','thin');
                    });

                    //weight
                    $sheet->cell('Q'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->finish_weight);
                        $cell->setBorder('thin','thin','thin','thin');
                    });                    
                    $sheet->cell('R'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->wgood);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('S'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->wrework);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('T'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->wscrap);
                        $cell->setBorder('thin','thin','thin','thin');
                    });

                    //rejection rate
                    $sheet->cell('U' . $row, function ($cell) use ($dt) {
                        $cell->setValue($dt->rrework);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('V' . $row, function ($cell) use ($dt) {
                        $cell->setValue($dt->rscrap);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('W' . $row, function ($cell) use ($dt) {
                        $cell->setValue($dt->jo_no);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    
                    $row++;
                }
                
                // $sheet->cells('A4:H'.$row, function($cells) {
                //     $cells->setBorder('thick', 'thick', 'thick', 'thick');
                // });
                // $sheet->cells('I4:L'.$row, function($cells) {
                //     $cells->setBorder('thick', 'thick', 'thick', 'thick');
                // });
                // $sheet->cells('M4:P'.$row, function($cells) {
                //     $cells->setBorder('thick', 'thick', 'thick', 'thick');
                // });
                // $sheet->cells('Q4:R'.$row, function($cells) {
                //     $cells->setBorder('thick', 'thick', 'thick', 'thick');
                // });
                // $sheet->cells('S4:S'.$row, function($cells) {
                //     $cells->setBorder('thick', 'thick', 'thick', 'thick');
                // });
            });
        })->download('xlsx');
    }

    public function getFilteredProuctionSummary($req)
    {
        $date_from = "NULL";
        $date_to = "NULL";
        $jo_no = "NULL";
        $prod_code = "NULL";
        $code_description = "NULL";
        $div_code = "NULL";
        $process_name = "NULL";

        if (!is_null($req->date_from) && !is_null($req->date_to)) {
            $date_from = "'" . $req->date_from . "'";
            $date_to = "'" . $req->date_to . "'";
        }

        if (!is_null($req->jo_no)) {
            $jo_no = "'" . $req->jo_no . "'";
        }

        if (!is_null($req->prod_code)) {
            $prod_code = "'" . $req->prod_code . "'";
        }

        if (!is_null($req->code_description)) {
            $code_description = "'" . $req->code_description . "'";
        }

        if (!is_null($req->div_code)) {
            $div_code = "'" . $req->div_code . "'";
        }

        if (!is_null($req->process_name)) {
            $process_name = "'" . $req->process_name . "'";
        }

        $data = DB::select(
            DB::raw("CALL RPT_production_summary_report(" . $date_from . ",
                                                                " . $date_to . ",
                                                                " . $jo_no . ",
                                                                " . $prod_code . ",
                                                                " . $code_description . ",
                                                                " . $div_code . ",
                                                                " . $process_name . ",
                                                                " . Auth::user()->id . ")")
        );

        return $data;
    }
}
