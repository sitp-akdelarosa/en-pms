@extends('pdf.layout')
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

							<?php $row++;
								  $slash =' '; if ($rm->thickness !== '') { $slash = ' / '; } 
							?>
								<tr>
									<td width="8.3%">{{ $rm->alloy }}</td>
									<td width="8.3%">{{ $rm->material_type }}</td>
									<td width="8.3%">{{ $rm->schedule }}</td>
									<td width="8.3%">{{ $rm->size.$slash.$rm->thickness }}</td>
									<td width="8.3%">{{ $rm->length }}</td>
									<td width="8.3%">{{ $rm->issued_qty }}</td>
									<td width="8.3%">{{ $rm->heat_no }}</td>
									<td width="8.3%">{{ $rm->suplier_heat_no }}</td>
									<th width="0.01%"></th>
									<td width="8.3%" class="left-double-border"></td>
									
									<td width="8.3%"></td>
									<td width="12.3%"></td>
									<td width="4.3%"></td>
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