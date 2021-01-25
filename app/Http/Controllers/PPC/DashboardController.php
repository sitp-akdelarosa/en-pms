<?php

namespace App\Http\Controllers\PPC;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HelpersController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\PpcDashboard;
use DataTables;
use DB;
use Excel;
use Carbon;

class DashboardController extends Controller
{
	protected $_helper;

	public function __construct()
	{
		$this->_helper = new HelpersController;
	}

	public function index()
	{
		$dateFrom = date('Y-m-d');
		$dateTo = date('Y-m-d',strtotime(str_replace('-', '/', date('Y-m-d')) . "+1 days"));

		$user_accesses = $this->_helper->UserAccess();

		return view('ppc.dashboard',['from' => $dateFrom,'to' => $dateTo, 'user_accesses' => $user_accesses]);
	}

	public function pie_graph(Request $req)
	{
		if($req->jo_no == '') {
			$data = DB::select("SELECT  ifnull(
											(SUM(p.unprocessed)/(
												SUM(p.unprocessed)+SUM(p.good)+SUM(p.rework)+SUM(p.scrap)
											))*100,
										0) AS unprocessed, 
										ifnull(
											(SUM(p.good)/(
												SUM(p.unprocessed)+SUM(p.good)+SUM(p.rework)+SUM(p.scrap)
											))*100,
										0) AS good,
										ifnull(
											(SUM(p.rework)/(
												SUM(p.unprocessed)+SUM(p.good)+SUM(p.rework)+SUM(p.scrap)
											))*100,
										0) AS rework,
										ifnull(
											(SUM(p.scrap)/(
												SUM(p.unprocessed)+SUM(p.good)+SUM(p.rework)+SUM(p.scrap)
											))*100,
										0) AS scrap ,
										p.process AS process
								FROM prod_travel_sheets AS ts
								JOIN prod_travel_sheet_processes AS p ON ts.id = p.travel_sheet_id
								LEFT JOIN ppc_product_codes AS pc ON ts.prod_code = pc.product_code
								LEFT JOIN admin_assign_production_lines AS pl ON pl.product_line = pc.product_type
								WHERE ts.status = 2 and 
									  pl.user_id = '".Auth::user()->id."' and 
									  (p.unprocessed != 0 or p.good != 0 or p.rework != 0 or p.scrap != 0)
								GROUP BY p.process
								ORDER BY p.sequence ASC");
		} else {
			$data = DB::select("SELECT  ifnull(
											(SUM(p.unprocessed)/(
												SUM(p.unprocessed)+SUM(p.good)+SUM(p.rework)+SUM(p.scrap)
											))*100,
										0) AS unprocessed, 
										ifnull(
											(SUM(p.good)/(
												SUM(p.unprocessed)+SUM(p.good)+SUM(p.rework)+SUM(p.scrap)
											))*100,
										0) AS good,
										ifnull(
											(SUM(p.rework)/(
												SUM(p.unprocessed)+SUM(p.good)+SUM(p.rework)+SUM(p.scrap)
											))*100,
										0) AS rework,
										ifnull(
											(SUM(p.scrap)/(
												SUM(p.unprocessed)+SUM(p.good)+SUM(p.rework)+SUM(p.scrap)
											))*100,
										0) AS scrap ,
										p.process AS process
								FROM prod_travel_sheets AS ts
								JOIN prod_travel_sheet_processes AS p ON ts.id = p.travel_sheet_id
								WHERE ts.jo_sequence = '".$req->jo_no."' and 
								(p.unprocessed != 0 or p.good != 0 or p.rework != 0 or p.scrap != 0)
								GROUP BY p.process
								ORDER BY p.sequence ASC ");
		}

		$pie = [];
		foreach ($data as $key => $dt) {
			array_push($pie, [
				'process' => $dt->process,
				'records' => [
					[ 'y' => $dt->unprocessed, 'label' => 'Unprocessed', 'color' => '#ffc107'],
					[ 'y' => $dt->good, 'label' => 'Good', 'color' => '#28a745' ],
					[ 'y' => $dt->rework, 'label' => 'Rework', 'color' => '#004485' ],
					[ 'y' => $dt->scrap, 'label' => 'Scrap', 'color' => '#d84951' ]
				]
			]);
		}


		return response()->json($pie);
	}

	public function get_dashboard(Request $req)
	{
		if (isset($req->date_from) && !isset($req->date_to)) {
			$data = ['msg' => 'The Date to required if the Date from have value.','status' => 'failed'];
			return response()->json($data);
		}
		if (isset($req->date_from) && isset($req->date_to)) {
			
			$travel_sheet = DB::table('v_dashboard_ppc')
								->where('user_id' ,Auth::user()->id)
								->whereIn('travel_sheet_status',[0,1,2,5])
								->whereBetween('updated_at', [$req->date_from, $req->date_to]);
									
		}else{
			$travel_sheet = DB::table('v_dashboard_ppc')
								->where('user_id' ,Auth::user()->id)
								->where('travel_sheet_status',2);
		}
		// return dd(DB::getQueryLog());
	   return DataTables::of($travel_sheet)
				->editColumn('status', function($data) {
                    switch ($data->status) {
                        case 1:
                        case '1':
                            return 'DONE PROCESS';
                            break;
                        case 2:
                        case '2':
                            return 'ON-GOING';
                            break;
                        case 3:
                        case '31':
                            return 'CANCELLED';
                            break;
                        case 4:
                        case '4':
                            return 'TRANSFER ITEM';
                            break;
                        case 5:
                        case '5':
                            return 'ALL PROCESS DONE';
                            break;
                    
                        default:
                            return 'PENDING';
                            break;
                    }
					// $status = ''; //ON PROCESS
					// if ($data->status == 1) {
					// 	$status = 'READY FOR FG';
					// }else if($data->status == 2){
					// 	$status = 'FINISHED';
					// }else if($data->status == 3){
					// 	$status = 'CANCELLED';
					// }else if($data->status == 4){
					// 	$status = 'TRANSFER ITEM';
					// }
					// return $status;
				})->make(true);
	}

	public function searchFilter(Request $req)
    {
        return response()->json($this->getFilteredData($req));
    }

    private function getFilteredData($req)
    {
        $srch_date= "";
		$srch_jo_sequence = "";
		$srch_prod_code = "";
		$srch_description = "";
		$srch_div_code = "";
		$srch_plant = "";
		$srch_process = "";
		$srch_material_used = "";
		$srch_material_heat_no = "";
		$srch_lot_no = "";
		$srch_status = "";

        if (!is_null($req->srch_date_from) && !is_null($req->srch_date_to)) {
            $srch_date = " AND DATE_FORMAT(updated_at,'%Y-%m-%d') BETWEEN '".$req->srch_date_from."' AND '".$req->srch_date_to."'";
        }

        if (!is_null($req->srch_jo_sequence)) {
            $equal = "= ";
            $_value = $req->srch_jo_sequence;

            if (Str::contains($req->srch_jo_sequence, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_jo_sequence);
            }
            $srch_jo_sequence = " AND jo_sequence ".$equal."'".$_value."'";
        }

        if (!is_null($req->srch_prod_code)) {
            $equal = "= ";
            $_value = $req->srch_prod_code;
            
            if (Str::contains($req->srch_prod_code, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_prod_code);
            }
            $srch_prod_code = " AND call  ".$equal." '".$_value."'";
        }

        if (!is_null($req->srch_description)) {
            $equal = "= ";
            $_value = $req->srch_description;
            
            if (Str::contains($req->srch_description, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_description);
            }
            $srch_description = " AND description ".$equal." '".$_value."'";
        }

        if (!is_null($req->srch_div_code)) {
            $equal = "= ";
            $_value = $req->srch_div_code;
            
            if (Str::contains($req->srch_div_code, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_div_code);
            }
            $srch_div_code = " AND div_code ".$equal." '".$_value."'";
        }

        if (!is_null($req->srch_plant)) {
            $equal = "= ";
            $_value = $req->srch_plant;
            
            if (Str::contains($req->srch_plant, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_plant);
            }
            $srch_plant = " AND plant ".$equal." '".$_value."'";
        }

        if (!is_null($req->srch_process)) {
            $equal = "= ";
            $_value = $req->srch_process;
            
            if (Str::contains($req->srch_process, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_process);
            }
            $srch_process = " AND process ".$equal." '".$_value."'";
        }

        if (!is_null($req->srch_material_used)) {
            $equal = "= ";
            $_value = $req->srch_material_used;
            
            if (Str::contains($req->srch_material_used, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_material_used);
            }
            $srch_material_used = " AND material_used ".$equal." '".$_value."'";
        }

        if (!is_null($req->srch_material_heat_no)) {
            $equal = "= ";
            $_value = $req->srch_material_heat_no;
            
            if (Str::contains($req->srch_material_heat_no, '*')){
                $equal = "LIKE ";
                $_value = str_replace("*","%",$req->srch_material_heat_no);
            }
            $srch_material_heat_no = " AND material_heat_no ".$equal." '".$_value."'";
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

        if (!is_null($req->srch_status)) {

            $_value = "";
            $comma = "";

            foreach ($req->srch_status as $key => $status) {
                if ($key > 0) {
                    $comma = ", ";
                }
                $_value .= $comma . "'".$status."'";
            }

            $srch_status = " AND `pstatus` in (".$_value.")";
		}
		
        
		
		$data = DB::table('v_dashboard_ppc')
								->where('user_id' ,Auth::user()->id)
								->whereIn('travel_sheet_status',[0,1,2,5])
								->whereRaw("1=1".$srch_date.
									$srch_jo_sequence.
									$srch_prod_code.
									$srch_description.
									$srch_div_code.
									$srch_plant.
									$srch_process.
									$srch_material_used.
									$srch_material_heat_no.
									$srch_lot_no.
									$srch_status)
								->get();
                        
        return $data;
    }

    public function downloadExcelSearchFilter(Request $req)
    {
        $data = $this->getFilteredData($req);
        $date = date('Ymd');

        Excel::create('MaterialInventory_'.$date, function($excel) use($data)
        {
            $excel->sheet('Report', function($sheet) use($data)
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
                $sheet->cell('A2',"Travel Sheet in Production");

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
                $sheet->cell('A4', function($cell) use($dt) {
                    $cell->setValue("J.O. No.");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('B4', function($cell) use($dt) {
                    $cell->setValue("Item Code");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('C4', function($cell) use($dt) {
                    $cell->setValue("Description");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('D4', function($cell) use($dt) {
                    $cell->setValue("Division");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('E4', function($cell) use($dt) {
                    $cell->setValue("Plant");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('F4', function($cell) use($dt) {
                    $cell->setValue("Process");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('G4', function($cell) use($dt) {
                    $cell->setValue("Material");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('H4', function($cell) use($dt) {
                    $cell->setValue("Heat No.");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('I4', function($cell) use($dt) {
                    $cell->setValue("Lot No.");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('J4', function($cell) use($dt) {
                    $cell->setValue("Sched Qty");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('K4', function($cell) use($dt) {
                    $cell->setValue("Unprocess");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('L4', function($cell) use($dt) {
                    $cell->setValue("Good");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('M4', function($cell) use($dt) {
                    $cell->setValue("Scrap");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('N4', function($cell) use($dt) {
                    $cell->setValue("Total Output");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('O4', function($cell) use($dt) {
                    $cell->setValue("Order Qty");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('P4', function($cell) use($dt) {
                    $cell->setValue("Total Issued Qty.");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('Q4', function($cell) use($dt) {
                    $cell->setValue("Issued Qty.");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('R4', function($cell) use($dt) {
                    $cell->setValue("End Date");
                    $cell->setBorder('thick','thick','thick','thick');
                });
                $sheet->cell('S4', function($cell) use($dt) {
                    $cell->setValue("Status");
                    $cell->setBorder('thick','thick','thick','thick');
                });

                $sheet->cells('A4:S4', function($cells) {
                    $cells->setBorder('thick', 'thick', 'thick', 'thick');
                });

                $row = 5;

                foreach ($data as $key => $dt) {
                    $sheet->setHeight($row, 15);

                    $sheet->cell('A'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->jo_sequence);
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
                        $cell->setValue($dt->div_code);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('E'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->plant);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('F'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->process);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('G'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->material_used);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('H'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->material_heat_no);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('I'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->lot_no);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('J'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->sched_qty);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('K'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->unprocessed);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('L'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->good);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('M'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->scrap);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('N'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->total_output);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('O'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->order_qty);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('P'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->total_issued_qty);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('Q'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->issued_qty);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('R'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->end_date);
                        $cell->setBorder('thin','thin','thin','thin');
                    });
                    $sheet->cell('S'.$row, function($cell) use($dt) {
                        $cell->setValue($dt->status);
                        $cell->setBorder('thin','thin','thin','thin');
                    });

                    $sheet->cells('A'.$row.':R'.$row, function($cells) {
                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    
                    $row++;
                }
                
                $sheet->cells('A4:R'.$row, function($cells) {
                    $cells->setBorder('thick', 'thick', 'thick', 'thick');
                });
            });
        })->download('xlsx');
    }

	public function get_jono()
	{
		$jo_no = DB::table('prod_travel_sheets as ts')
					->leftjoin('ppc_product_codes as pc', 'ts.prod_code', '=', 'pc.product_code')
					->leftjoin('admin_assign_production_lines as pl', 'pl.product_line', '=', 'pc.product_type')
					->select(['ts.jo_sequence'])
					->where('pl.user_id' ,Auth::user()->id)
					->where('ts.status','!=',3)
					->get();
		return response()->json($jo_no);
	}
}
