@extends('pdf.layout')

@section('title')
	Travel Sheet
@endsection


@push('css')
	<style>
        .header, .footer {
            width: 100%;
            text-align: center;
            position: fixed;
        }
        .header {
            top: 0px;
        }
        .footer {
            bottom: 0px;
        }
        .pagenum:before {
            content: counter(page);
        }
        .fontArial {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
        }
        thead{display: table-header-group;}
		tfoot {display: table-row-group;}
		tr {page-break-inside: avoid;}
		.remarks {
			padding-top: -50px;
		}
    </style>
    <style type="text/css">
	    .page
	    {
	    	overflow: hidden;
	        page-break-after: always;
	        page-break-inside: avoid;
	    }
	</style>
@endpush

@section('body')
	<?php
		foreach ($data as $key => $pages) {
			foreach ($pages['header'] as $key => $header) {
				if ($pages['id'] == $header->id) {
	?>
					<div class="container page">
						<div class="row">
							<div class="col-xs-6" style="padding-right: 20px">
								<img src="{{ asset($header->iso_photo) }}" alt="" height="80px" width="200px" >
							</div>
							<div class="col-xs-5 text-center" style="width: 500px;">
								<h2 style="padding-top:40px;">TRAVEL SHEET</h2>
							</div>
							<div class="col-xs-1" style="width: 217px;">
								<p style="padding-top:50px; margin-bottom: 0px; padding-bottom: 0px;font-size: 13px; text-align:right">Date Released: {{ $pages['date'] }}</p>
								<?php
									$tm = strtotime($header->ship_date);
									$ship_date = date('M. d, Y',$tm);
								?>
								<p style="padding-top:0px; padding-bottom: 0px;font-size: 13px; text-align:right">Est. Ship Date: {{ $ship_date }}</p>
							</div>
							    
						</div>
						<div class="row">
							<div class="col-xs-12" style="width:100%;">
								<table class="table table-bordered table-sm" style="font-size:13px;width:100%;">
									<thead>
										<tr>
											<td class="text-center" rowspan="2" colspan="3"><img src="{{ asset('/barcode.php').'?codetype=CODE128&size=30&text='.$header->jo_sequence.'&print=true' }}" class="img-fluid" alt="{{ $header->jo_sequence }}"></td> 
											<td class="text-center">Type:</td>
											<td class="text-center" colspan="4">{{ $header->type }}</td>
											<td class="text-center" colspan="2">Material Used:</td>
											<td class="text-center" style="font-size:14px;font-weight:900;">{{ $header->bar_size }}</td>
											{{-- <td>{{ $header->bar_size.' x '.$header->cut_weight }}</td> --}}
										</tr>
										<tr>
											{{-- <td colspan="2">Material Used:</td>
											<td>{{ $header->material_used }}</td>
											<td>{{ $header->bar_size.' x '.$header->cut_weight }}</td> --}}
											<td class="text-center">Part Code:</td>
											<td class="text-center" colspan="4">{{ $header->prod_code }}</td>

											<td class="text-center" colspan="2">Material Heat #:</td>
											<td class="text-center" >{{ $header->material_heat_no }}</td>
										</tr>
										<tr>
											<td class="text-center">Job Order Qty.</td>
											<td class="text-center" colspan="2">{{ $header->sched_qty }}</td>
											<td class="text-center">Description</td>  {{-- rowspan="2" --}}
											<td class="text-center" colspan="4" rowspan="2"><strong style="font-size: 16px">{{ $header->description }}</strong></td>{{-- colspan="2" rowspan="2" --}}
											{{-- <td colspan="2">{{ $header->issued_qty }}</td> --}}
											
											<td class="text-center" colspan="2">Supplier Heat #:</td>
											<td class="text-center">{{ $header->supplier_heat_no }}</td>
										</tr>
										<tr>
											<td class="text-center">Product Line</td>
											<td class="text-center" colspan="3">{{ $header->product_line }}</td>
											<td class="text-center">Cut Wt.</td>
											<td class="text-center">Cut Length</td>
											<td class="text-center">Cut Width</td>
										</tr>
										<tr>
											<td class="text-center">S/C #</td>
											<td class="text-center" colspan="2">{{ $header->sc_no }}</td>
											<td class="text-center">Issued Qty.</td>
											<td class="text-center"><strong style="font-size: 16px">{{ $header->issued_qty }}</strong></td>
											<td class="text-center">Lot No.</td>
											<td class="text-center" colspan="2" style="font-size:14px;font-weight:900;">{{ $header->lot_no }}</td>

											<td class="text-center" style="font-size:14px;font-weight:900;">{{ $header->cut_weight }}</td> {{-- cut wt --}}
											<td class="text-center" style="font-size:14px;font-weight:900;">{{ $header->cut_length }}</td> {{-- cut length --}}
											<td class="text-center" style="font-size:14px;font-weight:900;">{{ $header->cut_width}}</td> {{-- cut width --}}
										</tr>
										<tr>
											<td class="text-center" rowspan="2">Process</td>
											<td class="text-center" rowspan="2" colspan="2">Remarks</td>
											<td class="text-center" rowspan="2">Division Name</td>
											<td class="text-center" rowspan="2">Date Process</td>
											<td class="text-center" rowspan="2">Received Qty.</td>
											<td class="text-center" rowspan="2">Output Qty.</td>
											<td class="text-center" rowspan="2">Qty. NC.</td>
											<td class="text-center" colspan="2">Disposition</td>
											{{-- <td class="text-center" rowspan="2">Date Returned</td> --}}
											<td class="text-center" rowspan="2">Operator's Name & Signature</td>
											
										</tr>
										<tr>
											<td>A/B/C</td>
											<td>Process Rework</td>
										</tr>
									</thead>
									<tbody>
										<?php
											foreach ($pages['process'] as $key => $proc) {
												if ($pages['id'] == $proc->pre_travel_sheet_id) {
										?>
													<tr>
														<td>{{ $proc->process_name }}</td>
														<td colspan="2">{{ $proc->remarks }}</td>
														<td>{{ $proc->div_name."(".$proc->div_code.")" }}</td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
													</tr>
													
										<?php
												}
											}
										?>
										
									</tbody>
									<tfoot>
										<tr>
											<td colspan="11">
												<b>Legend:</b> A - for Rework | B - for Scrap | C - Alloy Mix | NC - Non-Conformance
											</td>
											{{-- <td colspan="3">
												Date Released: {{ $pages['date'] }}
											</td> --}}
										</tr>
										<tr>
											<td colspan="11" style="height: 100px">
												<strong>REMARKS</strong>
												<p>{{ $header->remarks }}</p>
											</td>
										</tr>
										
									</tfoot>
								</table>
								
							</div>
						</div>

						<div class="row">
							<div class="col-xs-1" style="width:100%;text-align:right">
								<strong style="font-size: 12px">{{ $header->iso_code }}</strong>
							</div>
						</div>
					</div>
	<?php
				}
			}
		}
	?>
@endsection