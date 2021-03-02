<?php

namespace App\Http\Controllers;

use App\AdminSettingIso;
use App\Http\Controllers\HelpersController;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
		// $raw_material = DB::select(
        //                     DB::raw("CALL GET_raw_material_withdrawal_slip(".$req->trans_id.")")
		// 				);
						
		$raw_material = DB::select("select distinct rmwd.id as id,
											rmwi.trans_no as trans_no,
											rmwd.alloy as alloy,
											rmwd.item as item,
											rmwd.`schedule` as `schedule`,
											rmwd.size as size,
											i.length as length,
											rmwd.issued_qty as issued_qty,
											rmwd.material_heat_no as material_heat_no,
											i.supplier_heat_no as supplier_heat_no,
											rmwd.inv_id as inv_id
									from ppc_raw_material_withdrawal_infos as rmwi

									join ppc_raw_material_withdrawal_details as rmwd
									on rmwi.id = rmwd.trans_id

									join ppc_update_inventories as i
									on rmwd.inv_id = i.id

									where rmwi.id = ".$req->trans_id. " 
									and rmwd.deleted <> 1
									order by rmwd.id asc");
		
		$data = [
			'date' => $this->_helper->convertDate($req->date, 'F d, Y'),
			'raw_materials' => $raw_material,
			'prepared_by' => $req->prepared_by,
			'received_by' => $req->received_by,
			'trans_no' => $req->trans_no,
			'print_format' => $req->print_format,
			'plant' => $req->plant,
			'iso_code' => $req->iso
		];

		$pdf = PDF::loadView('pdf.raw_material_withdrawal_slip', $data);
		return $pdf->inline();
	}

	public function CuttingSchedule(Request $req)
	{
		$cut_data = [];

		$iso = AdminSettingIso::where('iso_code', $req->iso_control_no)->first();
		$jo_no_arr = explode(',',$req->jo_no);

		$cut_data = DB::table('v_jo_list_for_cutting_sched')
						->select(
							// DB::raw("id as id"),
							DB::raw("jo_no as jo_no"),
							DB::raw("alloy as alloy"),
							DB::raw("size as size"),
							DB::raw("item as item"),
							DB::raw("class as class"),
							DB::raw("lot_no as lot_no"),
							DB::raw("sc_no as sc_no"),
							DB::raw("sched_qty as jo_qty"),
							DB::raw("cut_weight as cut_weight"),
							DB::raw("cut_length as cut_length"),
							DB::raw("cut_width as cut_width"),
							DB::raw("material_used as material_used"),
							DB::raw("material_heat_no as material_heat_no"),
							DB::raw("supplier_heat_no as supplier_heat_no"),
							DB::raw("assign_qty as qty_needed")
						)
						->where('user_id', Auth::user()->id)
						->where('rmw_no', $req->withdrawal_slip)
						->whereIn('jo_no',$jo_no_arr)
						->where('status','<>','3')
						->get()->toArray();

		$leader = DB::table('users')->where('id',$req->leader)->select(DB::raw("CONCAT(firstname,' ',lastname) as fullname"))->first();

		$data = [
			'date_issued' => $this->_helper->convertDate($req->date_issued, 'F d, Y'),
			'cut_data' => $cut_data,
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
			'margin-top'    => 3,
			'margin-right'  => 5,
			'margin-bottom' => 3,
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

		$cut_data = DB::table('ppc_cutting_schedule_details')
						->select(
							DB::raw("jo_no as jo_no"),
							DB::raw("alloy as alloy"),
							DB::raw("size as size"),
							DB::raw("item as item"),
							DB::raw("class as class"),
							DB::raw("cut_weight as cut_weight"),
							DB::raw("cut_length as cut_length"),
							DB::raw("cut_width as cut_width"),
							DB::raw("sc_no as sc_no"),
							DB::raw("SUM(jo_qty) as jo_qty"),
							DB::raw("material_used as material_used"),
							DB::raw("SUM(qty_needed) as qty_needed"),
							DB::raw("material_heat_no as material_heat_no"),
							DB::raw("lot_no as lot_no"),
							DB::raw("supplier_heat_no as supplier_heat_no")
						)
						->where('cutt_id', $req->id)
						->groupBy('jo_no',
							'alloy',
							'size',
							'item',
							'class',
							'cut_weight',
							'cut_length',
							'cut_width',
							'sc_no',
							'material_used',
							'material_heat_no',
							'lot_no',
							'supplier_heat_no')
						->get()->toArray();

		$iso = AdminSettingIso::where('iso_code', $info->iso_control_no)->first();

		$data = [
			'date_issued' => $info->date_issued,
			'cut_data' => $cut_data,
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
			'margin-bottom' => 3,
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
