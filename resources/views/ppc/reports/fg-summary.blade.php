@extends('layouts.app')

@section('content')
<?php
 $exist = 0;
foreach ($user_accesses as $user_access){
        if($user_access->code == "R0006" ){
            $exist++;
        }
}
    if($exist == 0){
        echo  '<script>window.history.back()</script>';
        exit;
    }
?>
<section class="content-header">
    <h1>FG Summary</h1>
</section>
<section class="content">
	<div class="row justify-content-center">
        <div class="col-lg-12 col-md-10 col-sm-12 col-xs-12">
            <div class="box">
                <div class="box-body">
                	 <div class="row">
	                    <div class="col-md-2 pull-right">
	                        <div class="input-group input-group-sm mb-3">
	                            <div class="input-group-prepend">
	                                <span class="input-group-text">Status</span>
	                            </div>
	                            <select class="form-control" name="status" id="status">
	                                <option value="0">OPEN</option>
	                                <option value="1">CLOSED</option>
	                            </select>
	                        </div>
	                    </div>
	                </div>

		            <table class="table table-striped table-sm dt-responsive" id="tbl_fg_summary" width="100%">
		                <thead class="thead-dark">
		                    <tr>
		                        <th></th>
		                        <th>SC #</th>
		                        <th>Product Code</th>
		                        <th>Description</th>
		                        <th>Order Qty</th>
		                        <th>Qty</th>
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
		var token = $('meta[name="csrf-token"]').attr('content');
        var code_permission = "R0006";
        var getFGURL = "{{ url('reports/fg-summary/get-FG') }}";
        var getSc_noURL = "{{ url('reports/fg-summary/get-sc-no') }}";
	</script>
	<script type="text/javascript" src="{{ asset('/js/pages/ppc/reports/fg-summary.js') }}"></script>
@endpush
