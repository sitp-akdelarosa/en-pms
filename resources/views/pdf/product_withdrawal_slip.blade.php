@extends('pdf.layout')

@section('title')
	Product Withdrawal Slip
@endsection

@section('body')

    @if ($print_format == 'FINISHED')
    
        <style>
            .underline {
                border-bottom: solid 1px;
                width: 300px;
                display: inline-block;
            }

            .date {
                margin-left: 50px;
            }

            .trans_no {
                margin-left: 60px;
            }

            .released_by {
                margin-left: 100px;
            }
        </style>
		<div class="container">
			<h5 style="text-align: center;">FINISHED GOODS WITHDRAWAL SLIP</h5>
			<div class="row">
				<div class="col-xs-12" style="font-size:13px;">
					
                    <strong>SECTION:</strong> <div class="underline"></div>
                    <strong class="date">DATE: {{ strtoupper($date) }}</strong>
                    <strong class="trans_no">WITHDRAWAL SLIP #: {{ strtoupper($trans_no) }}</strong>
				</div>
            </div>
            
			<div class="row">
				<div class="col-xs-12" style="width:100%">
					<table class="table table-bordered table-sm text-center" style="font-size:13px;width:100%;">
						<thead>
							<tr>
								<th width="10%">No.</th>
								<th width="40%">ITEM DESCRIPTION</th>
								<th width="20%">LOT NO.</th>
								<th width="10%">QUANTITY</th>
								<th width="20%">REMARKS</th>
							</tr>
						</thead>
						<tbody>
							<?php $row = 1; $all_row = 10?>
                            @foreach ($products as $key => $p)
								<tr>
									<td>{{ $row }}</td>
									<td>{{ $p->item_code .':    '. $p->code_description }}</td>
									<td>{{ $p->lot_no }}</td>
									<td>{{ $p->issued_qty }}</td>
									<td>{{ $p->remarks }}</td>
                                </tr>
                                <?php $row++; ?>
							@endforeach
							@while ($row <= $all_row)
								<tr>
									<td>{{ $row }}</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
                                <?php $row++; ?>
							@endwhile
						</tbody>
					</table>
				</div>
            </div>

            <div class="row">
				<div class="col-xs-12" style="font-size:13px;">
					
                    <strong>REQUESTED BY:</strong> <div class="underline"></div>
                    <strong class="released_by">RELEASED BY:</strong> <div class="underline"></div>
				</div>
			</div>
        </div>
        
	@else

        <style>
            .underline {
                border-bottom: solid 1px;
                width: 300px;
                display: inline-block;
            }

            .empty_td {
                height: 20px;
            }

            .date {
                margin-right: 450px;
            }
        </style>
		<div class="container">
			<h5 style="text-align: center;">CRUDE WITHDRAWAL SLIP</h5>
			<div class="row">
				<div class="col-xs-12" style="font-size:13px;">
                    <strong class="date">DATE: {{ strtoupper($date) }}</strong>
                    <strong class="trans_no">WITHDRAWAL SLIP #: {{ strtoupper($trans_no) }}</strong>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<table class="table table-bordered table-sm" style="font-size:12px;width:100%;">
						<thead>
							<tr>
								<th class="text-center" width="10%" rowspan="2">ALLOY</th>
								<th class="text-center" width="10%" rowspan="2">ITEM/DESCRIPTION</th>
                                <th class="text-center" width="10%" rowspan="2">HEAT NO.</th>
                                <th class="text-center" width="10%" rowspan="2">FINISHED MATERIAL DESCRIPTION</th>
								<th class="text-center" width="10%" colspan="2">QUANTITY</th>
								<th class="text-center" width="15%" rowspan="2">REMARKS</th>
							</tr>
							<tr>
                                <th width="5%">NEEDED</th>
								<th width="5%">ISSUED</th>
							</tr>
						</thead>
						<tbody>
							<?php $row = 1; $all_row = 10?>
							@foreach ($products as $key => $p)
								<tr>
									<td width="10%">{{ $p->alloy }}</td>
									<td width="10%">{{ $p->item }}</td>
									<td width="10%">{{ $p->heat_no }}</td>
									<td width="5%">{{ $p->item_code .':    '. $p->code_description }}</td>
									<td width="5%"></td>
									<td width="5%"></td>
									<td width="15%">{{ $p->remarks }}</td>
                                </tr>
                                <?php $row++; ?>
							@endforeach
							@while ($row <= $all_row)
								<tr>
									<td class="empty_td"></td>
									<td class="empty_td"></td>
									<td class="empty_td"></td>
									<td class="empty_td"></td>
                                    <td class="empty_td"></td>
                                    <td class="empty_td"></td>
                                    <td class="empty_td"></td>
								</tr>
                                <?php $row++; ?>
							@endwhile
						</tbody>
						<tfoot>
							<tr height="80px">
								<td colspan="4">
									<p class="left"><strong>Prepared By:</strong></p>
									<p style="border-bottom: 1px solid" align="center">{{ $prepared_by }}</p>
									<p align="center"><strong>PPC Staff</strong></p>
								</td>
								<td colspan="3">
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