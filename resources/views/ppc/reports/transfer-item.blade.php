@extends('layouts.app')

@section('title')
	Transfer Item Report
@endsection

@section('content')
<?php
 $exist = 0;
foreach ($user_accesses as $user_access){
        if($user_access->code == "R0002" ){
            $exist++;
        }
}
    if($exist == 0){
        echo  '<script>window.history.back()</script>';
        exit;
    }
?>
<section class="content-header">
    <h1>Transfer Item</h1>
</section>
<section class="content">
	<div class="row justify-content-center">
        <div class="col-lg-12 col-md-10 col-sm-12 col-xs-12">
            <div class="box">
                <div class="box-body">
		            <table class="table table-striped table-sm table-bordered dt-responsive" id="tbl_transfer_item" width="100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>Job Order No.</th>
                            <th>SC No.</th>
                            <th>Product Code</th>
                            <th>From Division Code</th>
                            <th>From process</th>
                            <th>To Division Code</th>
                            <th>To Process</th>
                            <th>Qty.</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            <th>Item Status</th>
                            <th>Receive Date</th>
                            <th>Receive Qty</th>
                            <th>Receive Remarks</th>
                        </tr>
                    </thead>
		                <tbody id="tbl_fg_summary_body"></tbody>
		            </table>
                </div>
            </div>
        </div>
    </div>
</section>
@include('includes.modals.reports.fg-summary-modal')
@endsection

@push('scripts')
	<script type="text/javascript">
        var getTransferEntryURL = "{{ url('reports/transfer-item/get-TransferEntry') }}";
	</script>
	<script type="text/javascript" src="{{ asset('/js/pages/ppc/reports/transfer-item-report.js') }}"></script>
@endpush
