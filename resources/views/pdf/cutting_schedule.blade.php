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
.th {
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
	$_date_issued = date("m/d/Y", strtotime($date_issued));
?>
<div class="container text-center">
    <div class="row center" style="margin-bottom:0px">
        <div class="col-xs-3" style="font-size:10px;margin-bottom:0px">
            <h4 class="center" style="font-weight:900;margin-bottom:0px">CUTTING SCHEDULE</h4>
        </div>
    </div>
    <br>

    <div class="row" style=" padding-bottom:5px;">
        <div class="col-xs-12" style="width:100%;">
            <table class="table table-sm" style="font-size:12px;width:100%;">
                <tr>
                    <td width="5%"><strong>Date:</strong></td>
                    <td width="15%" style="border-bottom: 1px solid"><strong>{{ $_date_issued }}</strong></td>
                    <td width="40%"></td>
                    <td width="15%"><strong>Withdrawal Slip:</strong></td>
                    <td width="15%" style="border-bottom: 1px solid"><strong>{{$withdrawal_slip}}</strong></td>
                </tr>
            </table>
        </div>
    </div>

   

    <div class="row">
        <div class="col-xs-12" style="width:100%;">
            <table class="table table-bordered table-sm" style="font-size:12px;width:100%;">
                <thead>
                    <tr>
                        <th class="th" width="15.88%">JO NO.</th>
                        <th class="th" width="5.88%">ALLOY</th>
                        <th class="th" width="5.88%">SIZE</th>
                        <th class="th" width="5.88%">ITEM</th>
                        <th class="th" width="5.88%">CLASS</th>
                        <th class="th" width="5.88%">LOT #</th>
                        <th class="th" width="5.88%">S/C #</th>
                        <th class="th" width="5.88%">J.O. QTY.</th>
                        <th class="th" width="3.88%">CUT WT.</th>
                        <th class="th" width="3.88%">CUT LENGTH</th>
                        <th class="th" width="3.88%">CUT WIDTH</th>
                        <th class="th" width="10.88%">MATERIAL USED</th>
                        <th class="th" width="5.88%">RM HEAT #</th>
                        <th class="th" width="1.88%">SUPPLIER HEAT #</th>
                        <th class="th" width="5.88%">QTY. NEEDED</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $cnt = 1; ?>
                    <?php $row = 0; ?>


                    @foreach (array_slice($cut_data,$countrows) as $key => $rm)
                        <tr>
                            <td height="40px">{{ $rm->jo_no }}</td>
                            <td>{{ $rm->alloy }}</td>
                            <td>{{ $rm->size }}</td>
                            <td>{{ $rm->item }}</td>
                            <td>{{ $rm->class }}</td>
                            <td>{{ $rm->lot_no }}</td>
                            <td>
                                <?php
                                    $sc = '';
                                    if (strpos($rm->sc_no, ',') !== false) {
                                        $sc = str_replace(',','</br>',$rm->sc_no);
                                        echo $sc;
                                    } else {
                                        echo $rm->sc_no;
                                    }
                                ?>
                            </td>
                            <td>{{ $rm->jo_qty }}</td>
                            <td>{{ $rm->cut_weight }}</td>
                            <td>{{ $rm->cut_length }}</td>
                            <td>{{ $rm->cut_width }}</td>
                            <td>{{ $rm->material_used }}</td>
                            <td>{{ $rm->material_heat_no }}</td>
                            <td>{{ $rm->supplier_heat_no }}</td>
                            <td>{{ $rm->qty_needed }}</td>
                        </tr>
                            
                    <?php
                        $cnt++;
                        
						if($cnt == 6){
							$countrows += 5;
							break;
						}
					?>
                    @endforeach

                    @while ($cnt <= 5) 
                        <tr>
                            <td height="40px"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <?php 
								$cnt++;
							?>
                        @endwhile

                </tbody>
                <tfoot>
                    <tr height="40px">
                        <td colspan="8">
                            <p class="left"><strong>Prepared By:</strong></p>
                            <p style="border-bottom: 1px solid; font-weight:700;font-size:14px">{{ $prepared_by }}</p>
                            <p><strong style="font-size:11px">PPC STAFF</strong></p>
                        </td>
                        <td colspan="9">
                            <p class="left"><strong>Received By:</strong></p>
                            <p style="border-bottom: 1px solid; font-weight:700;font-size:14px">{{ $leader }}</p>
                            <p><strong style="font-size:11px">LEADER</strong></p>
                        </td>

                    </tr>

                    <tr>
                        <td colspan="17" style="text-align: right;">
                            {{ $iso_control_no }}
                        </td>
                    </tr>

                </tfoot>
            </table>
        </div>
    </div>
</div>
@if($x%2 == 0)
<br />
@else
<hr />
@endif
<?php } ?>
@endsection