<?php

namespace App\Http\Controllers\PPC\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelpersController;
use App\Http\Controllers\Admin\AuditTrailController;
use DB;
use Excel;

class TravelSheetStatusController extends Controller
{
    protected $_helper = '';
    protected $_audit;
    protected $_moduleID;

    public function __construct()
    {
        // $this->middleware('ajax-session-expired');
        // $this->middleware('auth');
        $this->_helper = new HelpersController;
        $this->_audit = new AuditTrailController;

        $this->_moduleID = $this->_helper->moduleID('R0001');
    }

    public function index()
    {
        $user_accesses = $this->_helper->UserAccess();
        return view('ppc.reports.travel-sheet-status',['user_accesses' => $user_accesses]);
    }
    public function search_travelsheet(Request $req)
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
        $travelSheet_status=$this->GetTravelSheetStatusList($req);

        if (count($travelSheet_status) > 0) {
            $data = ['status' => 'success','ppo' => $travelSheet_status];
        } else {
                $data = ['msg' => 'No travel sheet in that date range.','status' => 'failed'];
        }

        return response()->json($data);
    }
    public function downloadExcel(Request $req)
    {
        $data=$this->GetTravelSheetStatusList($req);
        $date = date('Ymd');
        Excel::create('TRAVELSHEET_STATUS_'.$date, function($excel)use($data)
        {
            $excel->sheet('TravelSheetStatus', function($sheet) use($data)
            {
                $sheet->setHeight(4, 20);
                $sheet->mergeCells('A2:L2');
                $sheet->cells('A2:L2', function($cells) {
                    $cells->setAlignment('center');
                    $cells->setFont([
                        'family'     => 'Calibri',
                        'size'       => '14',
                        'bold'       =>  true,
                        'underline'  =>  true
                    ]);
                });
                $sheet->cell('A2',"Travel Sheet Status");

                $sheet->setHeight(6, 15);
                $sheet->cells('A4:L4', function($cells) {
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
                    $cell->setValue("#");
                    $cell->setBorder('thick','thick','thick','thick');
                });
            
                $sheet->cell('B4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("SC#");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('C4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("JO#");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('D4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("ProductCode /C");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('E4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("Description");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('F4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("Based Qty");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('G4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("Prod Output Qty");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('H4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("Remaining");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('I4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("Current Process");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('J4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("Status");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cell('K4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("FG Stocks");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('L4', function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValue("CRUDE Stocks");
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
                        $cell->setValue($dt->SC);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('C'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->JO );
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('D'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->ProductCode);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('E'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->Description);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('F'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->BasedQty);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('G'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->ProdOutputQty);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('H'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->Remaining);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('I'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->CurrentProcess);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('J'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->Status);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('K'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->FGStocks);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('L'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->CRUDEStocks);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    
                    $row++;
                }
                
                $sheet->cells('A4:L'.$row, function($cells) {
                    $cells->setBorder('thick', 'thick', 'thick', 'thick');
                });
            });
        })->download('xlsx');
    }
    public function GetTravelSheetStatusList($req){
        $date_from = "";
        $date_to = "";

        if (!is_null($req->date_from) && !is_null($req->date_to)) {
            $date_from = "'".$req->date_from."'";
            $date_to = "'".$req->date_to."'";
        }

        $travelSheet_status = DB::select(
                                        DB::raw(
                                            "CALL Get_TravelSheet_Status(
                                                ".$date_from.",
                                                ".$date_to."
                                            )"
                                        )
                                    );
        return $travelSheet_status;                          
    }
}
