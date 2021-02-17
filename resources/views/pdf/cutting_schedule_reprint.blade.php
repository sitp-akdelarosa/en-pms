@extends('pdf.layout')

@section('title')
	Cutting Schedule
@endsection

@push('css')
<style type="text/css">
	.left {
		text-align: left;
	}
	.right {
		text-align: right;
	}
	.center {
		text-align: center;
	}
</style>
@endpush

@section('body')
<?php
$page = sizeof($cut_data)/ 5;
$page = ceil($page); 
$countrows = 0;
for ($x = 1; $x <= $page; $x++) {
?>
	<div class="container text-center">
		<div class="row center">
			<div class="col-xs-3" style="font-size:10px;">
				<h5 class="center">CUTTING SCHEDULE</h5>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12">
				<table class="table table-sm"style="font-size:10px;width:100%;">
					<tr>
						<td width="5%">Date:</td>
						<td width="15%" style="border-bottom: 1px solid">{{ $date_issued }}</td>
						<td width="60%"></td>
						<td width="5%">Machine:</td>
						<td width="15%" style="border-bottom: 1px solid">{{ $machine_no }}</td>
					</tr>
				</table>
			</div>
		</div>

		<br>

		<div class="row">
			<div class="col-xs-12">
				<table class="table table-bordered table-sm" style="font-size:10px;width:100%;">
					<thead>
						<tr>
							<th width="12.5%">JO NO.</th>
							<th width="5.5%">ALLOY</th>
							<th width="5.5%">SIZE</th>
							<th width="10.5%">ITEM</th>
							<th width="8.5%">CLASS</th>
							<th width="18.5%">PRODUCTION ORDER NO.</th>
							<th width="2.5%">ORDER QTY.</th>
							<th width="2.5%">QTY. NEEDED</th>
							<th width="4.5%">QTY. CUT</th>
							<th width="22%" colspan="2">MATERIAL DESCRIPTION</th>
						</tr>
					</thead>
					<tbody>
						<?php $cnt = 1; ?>

						@foreach (array_slice($raw_materials,$countrows) as $key => $rm)
							<?php
								$mat_heat_no = '';

								if (isset($rm->material_heat_no)) {
									$mat_heat_no = $rm->material_heat_no;
								}

								if ($type == 'RMW') { 
							?>
									<tr>
										<td rowspan="2">{{ $rm->jo_no }}</td>
										<td rowspan="2">{{ $rm->p_alloy }}</td>				
										<td rowspan="2">{{ $rm->p_size }}</td>
										<td rowspan="2">{{ $rm->p_item }}</td>
										<td rowspan="2">{{ $rm->p_class }}</td>
										<td rowspan="2">{{ $rm->sc_no }}</td>
										<td rowspan="2">{{ $rm->order_qty }}</td>
										<td>{{ $rm->issued_qty }}</td>
										<td rowspan="2"></td>
										<td style="border-right: none;">{{ $rm->item }}</td>
										<td style="border-left: none;">
											<?php 
												$slash =  ' ';
												if($rm->schedule !== '') { 
													$slash = ' / '; 
												}
												echo $rm->size.$slash.$rm->schedule;
											?>
										</td>
									</tr>
									<tr>
										<td>{{ $rm->needed_qty }}</td>
										<td style="border-right: none;">{{ $mat_heat_no }}</td>
										<td style="border-left: none;">{{ $rm->lot_no }}</td>
									</tr>
							<?php 
								} else {

										$cut = $rm->cut_weight;

										if ($rm->cut_weight == '0.00N/A'){
											$cut = $rm->cut_length;
										}
							?>
									<tr>
										<td rowspan="2">{{ $rm->jo_no }}</td>
										<td rowspan="2">{{ $rm->p_alloy }}</td>				
										<td rowspan="2">{{ $rm->p_size }}</td>
										<td rowspan="2">{{ $rm->p_item }}</td>
										<td rowspan="2">{{ $rm->p_class }}</td>
										<td rowspan="2">{{ $rm->sc_no }}</td>
										<td rowspan="2">{{ $rm->order_qty }}</td>
										<td rowspan="2">{{ $rm->needed_qty }}</td>
										<td rowspan="2"></td>
										<td colspan="2"">{{ $rm->size.' x '.$cut}}</td>
									</tr>
									<tr>
										<td width="11%" style="border-right: none;">{{ $mat_heat_no }}</td>
										<td width="11%"style="border-left: none;">{{ $rm->lot_no }}</td>
									</tr>
							<?php
								}
								$cnt++;
								if($cnt == 6){
									$countrows += 5;
									break;
								}
							?>
						@endforeach

						@while ($cnt <= 5)
							<?php
								if ($type == 'RMW') { 
							?>
									<tr>
										<td rowspan="2" height="15"></td>
										<td rowspan="2"></td>				
										<td rowspan="2"></td>
										<td rowspan="2"></td>
										<td rowspan="2"></td>
										<td rowspan="2"></td>
										<td rowspan="2"></td>
										<td></td>
										<td rowspan="2"></td>
										<td style="border-right: none;" height="15"></td>
										<td style="border-left: none;"></td>
									</tr>
									<tr>
										<td></td>
										<td style="border-right: none;" height="15"></td>
										<td style="border-left: none;"></td>
									</tr>
							<?php 
								} else {
							?>
									<tr>
										<td rowspan="2" height="15"></td>
										<td rowspan="2"></td>				
										<td rowspan="2"></td>
										<td rowspan="2"></td>
										<td rowspan="2"></td>
										<td rowspan="2"></td>
										<td rowspan="2"></td>
										<td rowspan="2"></td>
										<td rowspan="2"></td>
										<td colspan="2" height="15"></td>
									</tr>
									<tr>
										<td style="border-right: none;" height="15"></td>
										<td style="border-left: none;" height="15"></td>
									</tr>
							<?php 
								}
								$cnt++;
							?>
						@endwhile

					</tbody>
					<tfoot>
						<tr height="50px">
							<td colspan="5">
								<p class="left"><strong>Prepared By:</strong></p>
								<p style="border-bottom: 1px solid">{{ $prepared_by }}</p>
								<p><strong>PPC Staff</strong></p>
							</td>
							<?php

								if ($withdrawal_slip != '') {
							?>
									<td colspan="6">
										<p class="left"><strong>Leader:</strong></p>
										<p style="border-bottom: 1px solid">{{ $leader }}</p>
										<p>{{$withdrawal_slip}}</p>
									</td>
							<?php
								} else {
							?>
									<td colspan="6">
										<p class="left"><strong>Leader:</strong></p>
										<p>{{ $leader }}</p>
									</td>
							<?php
								}


							?>
							
						</tr>

						<tr>
							<td colspan="11" style="text-align: right;">
								{{ $iso_control_no }}
							</td>
						</tr>

					</tfoot>
				</table>
			</div>
		</div>
	</div>
	@if($x%2 == 0)
	<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
	@else
	<br/><br/><br/><br/><br/>
	@endif
<?php } ?>
@endsection
