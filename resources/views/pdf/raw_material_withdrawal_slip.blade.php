@extends('pdf.layout')

@section('title')
	Raw Material Withdrawal Slip
@endsection

@section('body')

	@if ($print_format == 'material_withdrawal')
		<div class="container">
			<h5 style="text-align: center;">RAW MATERIAL WITHDRAWAL SLIP</h5>
			<div class="row">
				<div class="col-xs-12 text-center" style="font-size:10px;text-indent: 185px;">
					<div style="float:right;text-indent: 400px;">
						<b>Withdrawal Slip #:</b> {{ $trans_no }}
					</div>
					<b>Date:</b> {{ $date }}
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<table class="table table-bordered table-sm text-center" style="font-size:10px;width:100%;">
						<thead>
							<tr>
								<th width="8.3%">ALLOY</th>
								<th width="8.3%">ITEM/DESCRIPTION</th>
								<th width="8.3%">SCHED</th>
								<th width="8.3%">SIZE/OD THICKNESS</th>
								<th width="8.3%">LENGTH</th>
								<th width="8.3%">ISSUED QTY</th>
								<th width="8.3%">MATERIAL HEAT#</th>
								<th width="8.3%">SUPPLIER HEAT#</th>
								<th width="0.01%"></th>
								<th width="8.3%" class="left-double-border">PRODUCT LOT#</th>
								<th width="8.3%">SC#</th>
								<th width="12.3%">PRODUCT NAME</th>
								<th width="4.3%">NEEDED QTY</th>
							</tr>
						</thead>
						<tbody>
							<?php $row = 0; ?>
							@foreach ($raw_materials as $key => $rm)

							<?php 
								$items = null;

								if (isset($rm->id)) {
									$scs = \DB::table('v_jo_details_for_rmw')
												->where([
													['rmw_no', '=', $rm->trans_no],
													['rmw_id', '=', $rm->id]
												])
												->select('sc_no')
												->get();
									$items = \DB::select("SELECT DISTINCT rmw_no,
															jo_summary_id,
															jo_no,
															lot_no,
															product_code,
															`description`,
															needed_qty as needed_qty
													FROM v_jo_details_for_rmw
													where rmw_no = '".$rm->trans_no."'
													and rmw_id = ".$rm->id);

									// $items = \DB::select("SELECT rmw_no,
									// 						jo_summary_id,
									// 						jo_no,
									// 						lot_no,
									// 						product_code,
									// 						`description`,
									// 						SUM(assign_qty) as assign_qty,
									// 						needed_qty as needed_qty
									// 				FROM v_jo_details_for_rmw
									// 				where rmw_no = '".$rm->trans_no."'
									// 				and rmw_id = ".$rm->id."
									// 				group by rmw_no,
									// 						jo_summary_id,
									// 						jo_no,
									// 						lot_no,
									// 						product_code,
									// 						`description`,
									// 						needed_qty");
								}
								$row++;
							?>
								<tr>
									<td width="8.3%">{{ $rm->alloy }}</td>
									<td width="8.3%">{{ $rm->item }}</td>
									<td width="8.3%">{{ $rm->schedule }}</td>
									<td width="8.3%">{{ $rm->size }}</td>
									<td width="8.3%">{{ $rm->length." MM" }}</td>
									<td width="8.3%">{{ $rm->issued_qty }}</td>
									<td width="8.3%">{{ $rm->material_heat_no }}</td>
									<td width="8.3%">{{ $rm->supplier_heat_no }}</td>
									<th width="0.01%"></th>
									<td width="8.3%" class="left-double-border">
										<?php 
											if (count((array)$items) > 0) {
												$prev_lot_no = [];
												array_push($prev_lot_no, $items[0]->lot_no);
												$lot_no = '';
												foreach ($items as $key => $item) {
													if (!in_array($item->lot_no, $prev_lot_no)) {
														$lot_no .= $item->lot_no.'</br>';
													} else {
														$lot_no = $item->lot_no.'</br>';
													}

													array_push($prev_lot_no, $item->lot_no);
												}
												echo $lot_no;
											}
											// if ($rm->lot_no == null) {
											// 	echo '';
											// } else {
											// 	echo $rm->lot_no;
											// 	// $lt = explode(',',$rm->lot_no);
											// 	// $lt_whole = '';
											// 	// foreach ($lt as $key => $lot_no) {
											// 	// 	$lt_whole .= $lot_no."<br/>";
											// 	// }

											// 	// echo $lt_whole;
											// } 
										?>
									</td>
									
									<td width="8.3%">
										<?php
											if (count((array)$scs) > 0) {
												$prev_sc_no = [];
												array_push($prev_sc_no, $scs[0]->sc_no);
												$sc_no = '';
												foreach ($scs as $key => $sc) {
													if (!in_array($sc->sc_no, $prev_sc_no)) {
														$sc_no .= $sc->sc_no.'</br>';
													} else {
														$sc_no = $sc->sc_no.'</br>';
													}

													array_push($prev_sc_no, $sc->sc_no);
												}
												echo $sc_no;
											}
											// if ($rm->sc_no == null) {
											// 	echo '';
											// } else {
											// 	echo $rm->sc_no;
											// 	// $scno = explode(',',$rm->sc_no);
											// 	// $sc_whole = '';
											// 	// foreach ($scno as $key => $sc) {
											// 	// 	$sc_whole .= $sc."<br/>";
											// 	// }

											// 	// echo $sc_whole;
											// } 
										?>
									</td>
									<td width="12.3%">
										<?php
											if (count((array)$items) > 0) {
												
												$prev_description = [];
												array_push($prev_description, $items[0]->description);
												$description = '';
												foreach ($items as $key => $item) {
													if (!in_array($item->description, $prev_description)) {
														$description .= $item->description.'</br>';
													} else {
														$description = $item->description.'</br>';
													}

													array_push($prev_description, $item->description);
												}
												echo $description;
											}
											// if ($rm->product_code == null) {
											// 	echo '';
											// } else {
											// 	echo $rm->description;
											// 	// $pc = explode(',',$rm->product_code);
											// 	// $pc_whole = '';
											// 	// foreach ($pc as $key => $prod_code) {
											// 	// 	$pc_whole .= $prod_code."<br/>";
											// 	// }

											// 	// echo $pc_whole;
											// } 
										?>
									</td>
									<td width="4.3%">
										<?php
											if (count((array)$items) > 0) {
												$needed_qty = 0;
												foreach ($items as $key => $item) {
													$needed_qty += (double)$item->needed_qty;
												}

												echo $needed_qty;
											}
										?>
									</td>
								</tr>
							@endforeach
							@while ($row <= 20)
								<tr>
									<td width="8.3%" height="15"></td>
									<td width="8.3%"></td>
									<td width="8.3%"></td>
									<td width="8.3%"></td>
									<td width="8.3%"></td>
									<td width="8.3%"></td>
									<td width="8.3%"></td>
									<td width="8.3%"></td>
									<th width="0.01%"></th>
									<td width="8.3%" class="left-double-border"></td>
									<td width="8.3%"></td>
									<td width="12.3%"></td>
									<td width="4.3%"></td>
								</tr>
							<?php $row++; ?>
							@endwhile
						</tbody>
						<tfoot>
							<tr height="80px">
								<td colspan="6">
									<p class="left"><strong>Prepared By:</strong></p>
									<p style="border-bottom: 1px solid" align="center">{{ $prepared_by }}</p>
									<p align="center"><strong>PPC Staff</strong></p>
								</td>
								<td colspan="7">
									<p class="left"><strong>Received By:</strong></p>
									<p style="border-bottom: 1px solid" align="center">{{ $received_by }}</p>
									<p align="center"><strong>LINE LEADER/SUPERVISOR</strong></p>
								</td>
							</tr>
							<tr>
								<td colspan="13" style="text-align: right">{{ $iso_code }}</td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	@else

		<div class="container">
			<h5 style="text-align: center;">RAW MATERIAL WITHDRAWAL SLIP</h5>
			<div class="row">
				<div class="col-xs-12 text-center" style="font-size:10px;text-indent: 185px;">
					<div style="float:right;text-indent: 400px;">
						Withdrawal Slip #: {{ $trans_no }}
					</div>
					Date: {{ $date }}
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<table class="table table-bordered table-sm" style="font-size:10px;width:100%;">
						<thead>
							<tr>
								<th width="10%" rowspan="2">ALLOY</th>
								<th width="10%" rowspan="2">DESCRIPTION</th>
								<th width="10%" rowspan="2">SIZE/SCHED</th>
								<th colspan="3">QUANTITY</th>
								<th width="10%" rowspan="2">PROD. HEAT NO.</th>
								<th width="10%" rowspan="2">MAT. HEAT NO.</th>
								<th width="20%" rowspan="2">SC NO.</th>
								<th width="15%" rowspan="2">REMARKS</th>
							</tr>
							<tr>
								<th width="5%">ISSUED</th>
								<th width="5%">NEEDED</th>
								<th width="5%">RETURNED</th>
							</tr>
						</thead>
						<tbody>
							<?php $row = 0; ?>
							@foreach ($raw_materials as $key => $rm)
								<tr>
									<td width="10%">{{ $rm->alloy }}</td>
									<td width="10%">{{ $rm->item }}</td>
									<td width="10%">{{ $rm->size }}</td>
									<td width="5%"></td>
									<td width="5%">{{ '('.$rm->issued_qty.') '.$rm->needed_qty }}</td>
									<td width="5%"></td>
									<td width="10%">{{ $rm->lot_no }}</td>
									<td width="10%">{{ $rm->material_heat_no }}</td>
									<td width="20%">{{ $rm->sc_no }}</td>
									<td width="15%">{{ $rm->remarks }}</td>
								</tr>
							@endforeach
							@while ($row <= 20)
								<tr>
									<td width="10%" height="15"></td>
									<td width="10%"></td>
									<td width="10%"></td>
									<td width="5%"></td>
									<td width="5%"></td>
									<td width="5%"></td>
									<td width="10%"></td>
									<td width="10%"></td>
									<td width="20%"></td>
									<td width="15%"></td>
								</tr>
							<?php $row++; ?>
							@endwhile
						</tbody>
						<tfoot>
							<tr height="80px">
								<td colspan="5">
									<p class="left"><strong>Prepared By:</strong></p>
									<p style="border-bottom: 1px solid" align="center">{{ $prepared_by }}</p>
									<p align="center"><strong>PPC Staff</strong></p>
								</td>
								<td colspan="5">
									<p class="left"><strong>Received By:</strong></p>
									<p style="border-bottom: 1px solid" align="center">{{ $received_by }}</p>
									<p align="center"><strong>LINE LEADER/SUPERVISOR</strong></p>
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	@endif
	
@endsection