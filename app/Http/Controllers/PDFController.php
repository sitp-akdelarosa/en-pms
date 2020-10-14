<?php

namespace App\Http\Controllers;

use App\AdminSettingIso;
use App\Http\Controllers\HelpersController;
use DB;
use Illuminate\Http\Request;
use PDF;

class PDFController extends Controller
{
	protected $_helper = '';

	public function __construct()
	{
		$this->_helper = new HelpersController;
	}

	public function RawMaterialWithdrawalSlip(Request $req)
	{
		$cutt = DB::table('ppc_cutting_schedules')->select('id')
					->where('withdrawal_slip_no', $req->trans_id)
					->first();

		$raw_material = DB::table('v_raw_material_withdrawal_slip')
							->where('trans_id',$req->trans_id)
							->get();

		// if ($req->print_format == 'material_withdrawal') {
		// 	$raw_material = DB::table('ppc_raw_material_withdrawal_details')
		// 						->where('trans_id', $req->trans_id)
		// 						->get();

		// 	$raw_material = DB::table('v_raw_material_withdrawal_slip')
		// 						->where('trans_id',$req->trans_id)
		// 						->get();
		// } else {
			// $raw_material = DB::select("SELECT cs.alloy as alloy,
			// 									cs.material_desc_item as item,
			// 									cs.material_desc_size as size,
			// 									cs.plate_qty as issued_qty,
			// 									cs.qty_needed as needed_qty,
			// 									cs.material_desc_lot_no as lot_no,
			// 									concat(cs.material_desc_heat_no,'/',cs.material_desc_supplier_heat_no) as material_heat_no,
			// 									cs.sc_no as sc_no,
			// 									concat(cs.size,' ',cs.class) as remarks
			// 							FROM ppc_raw_material_withdrawal_infos as rmw
			// 							join ppc_cutting_schedule_details as cs
			// 							where rmw.id = '" . $req->trans_id . "'
			// 							group by cs.alloy,
			// 									cs.material_desc_item,
			// 									cs.material_desc_size,
			// 									cs.plate_qty,
			// 									cs.qty_needed,
			// 									cs.material_desc_lot_no,
			// 									cs.material_desc_heat_no,
			// 									cs.material_desc_supplier_heat_no,
			// 									cs.sc_no,
			// 									cs.size,
			// 									cs.class");
		//}

		$data = [
			'date' => $this->_helper->convertDate($req->date, 'F d, Y'),
			'raw_materials' => $raw_material,
			'prepared_by' => $req->prepared_by,
			'received_by' => $req->received_by,
			'trans_no' => $req->trans_no,
			'print_format' => $req->print_format,
			'plant' => $req->plant,
		];

		$pdf = PDF::loadView('pdf.raw_material_withdrawal_slip', $data);
		return $pdf->inline();
	}

	// public function CuttingSchedule(Request $req)
	// {
	//     $ids = explode(',', $req->ids);
	//     $cut_data = [];

	//     $id = implode(',', $ids);
	//     $cut_data = DB::select("SELECT * FROM vcuttingsched where id in (".$id.")");

	//     $iso = AdminSettingIso::where('iso_code',$req->iso_control_no)->first();

	//     $data = [
	//         'date_issued' => $this->_helper->convertDate($req->date_issued,'F d, Y'),
	//         'raw_materials' => $cut_data,
	//         'machine_no' => $req->machine_no,
	//         'prepared_by' => $req->prepared_by,
	//         'leader' => $req->leader,
	//         'iso_control_no' => $iso->iso_code,
	//         'iso_photo' => $iso->photo,
	//         'withdrawal_slip' => $req->withdrawal_slip,
	//         'type' => $req->type
	//     ];
	//     // return dd($data);
	//     // return response()->json();
	//     $pdf = PDF::loadView('pdf.cutting_schedule', $data);
	//     return $pdf->inline();
	// }

	public function CuttingSchedule(Request $req)
	{
		$cut_data = [];

		$iso = AdminSettingIso::where('iso_code', $req->iso_control_no)->first();
		// $id = implode(',', $ids);
		// $cut_data = DB::select("SELECT * FROM vCuttingSched where id in (".$id.")");

		// $iso = AdminSettingIso::where('iso_code',$req->iso_control_no)->first();

		foreach ($req->no as $key => $no) {
			array_push(
				$cut_data,
				(object)array(
					'id'=>$req->id[$key],
					'jo_no' => $req->no[$key],
					'p_alloy' => $req->p_alloy[$key],
					'p_size' => $req->p_size[$key],
					'p_item' => $req->p_item[$key],
					'p_class' => $req->p_class[$key],
					'cut_weight' => $req->cut_weight[$key],
					'cut_length' => $req->cut_length[$key],
					'schedule' => $req->schedule[$key],
					'qty_needed_inbox' => $req->qty_needed_inbox[$key],
					'sc_no' => $req->sc_no[$key],
					'order_qty' => $req->order_qty[$key],
					'needed_qty' => $req->needed_qty[$key],
					'qty_cut' => '0',
					'plate_qty' => $req->issued_qty[$key],
					'item' => $req->item[$key],
					'size' => $req->size[$key],
					'material_heat_no' => $req->mat_heat_no[$key],
					'lot_no' => $req->lot_no[$key],
					'supplier_heat_no' => $req->supplier_heat_no[$key]
				)
			);
		}
		$data = [
			'date_issued' => $this->_helper->convertDate($req->date_issued, 'F d, Y'),
			'raw_materials' => (array)$cut_data,
			'machine_no' => $req->machine_no,
			'prepared_by' => $req->prepared_by,
			'leader' => $req->leader,
			'iso_control_no' => $iso->iso_code,
			'iso_photo' => $iso->photo,
			'withdrawal_slip' => $req->withdrawal_slip,
			'type' => $req->type,
		];
		// return dd($data);
		$pdf = PDF::loadView('pdf.cutting_schedule', $data);
		return $pdf->inline();
	}

	public function CuttingScheduleReprint(Request $req)
	{
		$info = DB::table('ppc_cutting_schedules')
			->select(
				'iso_control_no',
				'withdrawal_slip_no',
				'date_issued',
				'machine_no',
				'prepared_by',
				'leader'
			)->where('id', $req->id)->first();
		// $jo = [];
		$cut_data = [];

		// $saved_cutting = DB::table('ppc_cutting_schedule_details')->where('cutt_id', $req->id)->get();

		// foreach ($saved_cutting as $key => $cut) {
		//     array_push($jo, "'" . $cut->item_no . "'");
		// }

		// $jos = implode(",", $jo);

		$cut_data = DB::select("SELECT 
									id,
									item_no AS jo_no,
									alloy AS p_alloy,
									size AS p_size,
									size AS p_size,
									item AS p_item,
									class AS p_class,
									cut_weight,
									cut_length,
									schedule,
									qty_needed_inbox,
									sc_no,
									order_qty,
									qty_needed AS needed_qty,
									plate_qty,
									material_desc_item AS item,
									material_desc_size AS size,
									material_desc_heat_no AS material_heat_no,
									material_desc_lot_no AS lot_no,
									material_desc_supplier_heat_no AS supplier_heat_no
								FROM ppc_cutting_schedule_details 
								WHERE cutt_id=$req->id");
		// $cut_data = DB::select("SELECT * FROM vcuttingsched where jo_no in (" . $jos . ")");

		// $cut_data = DB::table('vcuttingsched')->whereIn('jo_no',$jo)->get();

		// return dd($cut_data);

		$iso = AdminSettingIso::where('iso_code', $info->iso_control_no)->first();

		$data = [
			'date_issued' => $info->date_issued,
			'raw_materials' => (array) $cut_data,
			'machine_no' => $info->machine_no,
			'prepared_by' => $info->prepared_by,
			'leader' => $info->leader,
			'iso_control_no' => $iso->iso_code,
			'iso_photo' => $iso->photo,
			'withdrawal_slip' => $info->withdrawal_slip_no,
			'type' => '',
		];

		// return dd($data);
		$pdf = PDF::loadView('pdf.cutting_schedule', $data);
		return $pdf->inline();

	}

	public function TravelSheet(Request $req)
	{
		$jo_no = explode(',', $req->jo_no);

		$prod_ids = [];
		$travel_sheets = [];

		$pre_travel_sheet = DB::table('ppc_pre_travel_sheets')
			->whereIn('jo_no', $jo_no)
			->select('id', 'jo_no', 'prod_code')
			->get();
		$iso = $this->_helper->getISO($req->iso_control);

		foreach ($pre_travel_sheet as $key => $ts) {
			array_push($travel_sheets, [
				'id' => $ts->id,
				'date' => date('M d Y'),
				'header' => DB::table('v_travel_sheet_pdf')->where('id', $ts->id)->get(),
					// DB::table('ppc_pre_travel_sheet_products as tsp')
					// 	->join('ppc_jo_travel_sheets as jts', 'tsp.jo_no', '=', 'jts.jo_no')
					// 	->join('ppc_pre_travel_sheets as ts', 'tsp.pre_travel_sheet_id', '=', 'ts.id')
					// 	// ->join('ppc_product_codes as pc','pc.product_code','=','tsp.prod_code')
					// 	->where('tsp.pre_travel_sheet_id', $ts->id)
					// // ->where('jts.jo_no',$ts->jo_no)
					// // ->where('jts.prod_code',$ts->prod_code)
					// 	->select(
					// 		DB::raw('tsp.pre_travel_sheet_id as id'),
					// 		DB::raw('tsp.prod_code as prod_code'),
					// 		DB::raw('tsp.issued_qty_per_sheet as issued_qty'),
					// 		DB::raw('tsp.jo_sequence as jo_sequence'),
					// 		DB::raw('jts.jo_no as jo_no'),
					// 		DB::raw('tsp.sc_no as sc_no'),
					// 		DB::raw("(select back_order_qty from ppc_jo_details as jd
					// 							join ppc_jo_details_summaries as jds
					// 							on jd.jo_summary_id = jds.id
					// 							where jd.sc_no = tsp.sc_no
					// 							LIMIT 1) as order_qty"),
							
					// 		// DB::raw('jts.order_qty as order_qty'),
					// 		DB::raw('jts.sched_qty as sched_qty'),
					// 		DB::raw('jts.material_used as material_used'),
					// 		DB::raw('jts.material_heat_no as material_heat_no'),
					// 		DB::raw('jts.lot_no as prod_heat_no'),
					// 		DB::raw("IF(LEFT(jts.prod_code,1) = 'Z','Finish','Semi-Finish') as type"),
					// 		DB::raw("ts.iso_code as iso_code"),
					// 		DB::raw("ts.iso_name as iso_name"),
					// 		DB::raw("ts.iso_photo as iso_photo"),

					// 		DB::raw('ifnull(select p.code_description
					// 						from ppc_product_codes as p
					// 						where p.product_code = tsp.prod_code),jts.description) as description'),
							
					// 		DB::raw("(select concat(p.cut_weight,p.cut_weight_uom)
					// 						from ppc_product_codes as p
					// 						where p.product_code = tsp.prod_code) as cut_weight"),
					// 		DB::raw("(select size
					// 						from ppc_material_codes as m
					// 						where m.code_description = jts.material_used) as bar_size")
					// 	)
					// 	->get(),
				'process' => DB::table('ppc_pre_travel_sheet_processes')
								->where('pre_travel_sheet_id', $ts->id)
								->select('pre_travel_sheet_id', 'process_name')
								->get(),
				'iso' => $iso,
			]);
		}

		$pdf = PDF::loadView('pdf.travel_sheet', ['data' => $travel_sheets]);
		return $pdf->setPaper('A4')
			->setOption('margin-top', 6)
			->setOption('margin-left', 5)
			->setOption('margin-right', 5)
			->setOption('margin-bottom', 5)
			->inline();
	}
}
