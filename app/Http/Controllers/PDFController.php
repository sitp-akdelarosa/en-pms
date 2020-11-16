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
					'cut_width' => $req->cut_width[$key],
					'schedule' => $req->schedule[$key],
					'qty_needed_inbox' => $req->qty_needed_inbox[$key],
					'sc_no' => $req->sc_no[$key],
					'jo_qty' => $req->jo_qty[$key],
					'needed_qty' => $req->needed_qty[$key],
					'qty_cut' => '0',
					'plate_qty' => $req->issued_qty[$key],
					'item' => $req->item[$key],
					'size' => $req->size[$key],
					'material_heat_no' => $req->mat_heat_no[$key],
					'lot_no' => $req->lot_no[$key],
					'supplier_heat_no' => $req->supplier_heat_no[$key],
					'material_used' => $req->material_used[$key]
				)
			);
		}

		$leader = DB::table('users')->where('id',$req->leader)->select(DB::raw("CONCAT(firstname,' ',lastname) as fullname"))->first();

		$data = [
			'date_issued' => $this->_helper->convertDate($req->date_issued, 'F d, Y'),
			'raw_materials' => (array)$cut_data,
			'machine_no' => $req->machine_no,
			'prepared_by' => $req->prepared_by,
			'leader' => $leader->fullname,
			'iso_control_no' => $iso->iso_code,
			'iso_photo' => $iso->photo,
			'withdrawal_slip' => $req->withdrawal_slip,
			'type' => $req->type,
		];
		// return dd($data);
		$options = [
			'margin-top'    => 5,
			'margin-right'  => 5,
			'margin-bottom' => 10,
			'margin-left'   => 5,
		];

		$pdf = PDF::loadView('pdf.cutting_schedule', $data)->setPaper('a4', 'portrait');

		foreach ($options as $margin => $value) {
			$pdf->setOption($margin, $value);
		}

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
									cut_width,
									schedule,
									qty_needed_inbox,
									sc_no,
									jo_qty,
									qty_needed AS needed_qty,
									plate_qty,
									material_desc_item AS item,
									material_desc_size AS size,
									material_desc_heat_no AS material_heat_no,
									material_desc_lot_no AS lot_no,
									material_desc_supplier_heat_no AS supplier_heat_no,
									material_used
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

		$options = [
			'margin-top'    => 5,
			'margin-right'  => 5,
			'margin-bottom' => 10,
			'margin-left'   => 5,
		];

		$pdf = PDF::loadView('pdf.cutting_schedule', $data)->setPaper('a4', 'portrait');

		foreach ($options as $margin => $value) {
			$pdf->setOption($margin, $value);
		}

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
			->orderBy('id','asc')
			->get();
		$iso = $this->_helper->getISO($req->iso_control);

		foreach ($pre_travel_sheet as $key => $ts) {
			array_push($travel_sheets, [
				'id' => $ts->id,
				'date' => date('M. d, Y'),
				'header' => DB::table('v_travel_sheet_pdf')->where('id', $ts->id)->get(),
				'process' => DB::select("SELECT tp.pre_travel_sheet_id,
												tp.process_name,
												pd.div_name,
												pd.div_code,
												tp.sequence,
												ifnull(tp.remarks,pp.remarks) as remarks
										FROM enpms.ppc_pre_travel_sheet_processes as tp
										inner join ppc_divisions as pd
										on tp.div_code = pd.div_code
										inner join ppc_product_processes as pp
                                    	on pp.process = tp.process_name
										where tp.pre_travel_sheet_id = ". $ts->id."
										AND pp.prod_code = '".$ts->prod_code."'
										group by tp.pre_travel_sheet_id,
												tp.process_name,
												pd.div_name,
												pd.div_code,
												tp.sequence
										order by sequence ASC"),
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

	public function ProductWithdrawalSlip(Request $req)
	{

		$product = DB::table('ppc_product_withdrawal_details as d')
						->join('ppc_product_codes as p','p.product_code','=','d.item_code')
							->where('d.trans_id',$req->trans_id)
							->get();

		$data = [
			'date' => $this->_helper->convertDate($req->date, 'F d, Y'),
			'products' => $product,
			'prepared_by' => $req->prepared_by,
			'issued_by' => $req->issued_by,
			'received_by' => $req->received_by,
			'trans_no' => $req->trans_no,
			'print_format' => $req->print_format,
			'plant' => $req->plant,
		];

		$pdf = PDF::loadView('pdf.product_withdrawal_slip', $data);
		return $pdf->inline();
	}
}
