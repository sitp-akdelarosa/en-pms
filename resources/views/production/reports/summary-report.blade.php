@extends('layouts.app')

@section('title')
	Production Summary Report
@endsection

@section('content')
<?php
 $exist = 0;
foreach ($user_accesses as $user_access){
        if($user_access->code == "R0004" ){
            $exist++;
        }
}
    if($exist == 0){
        echo  '<script>window.history.back()</script>';
        exit;
    }
?>
<section class="content-header">
    <h1>Operators Output</h1>
</section>
<section class="content">
	<div class="row justify-content-center">
        <div class="col-lg-12 col-md-10 col-sm-12 col-xs-12">
            <div class="box">
                <div class="box-body">
                    <form id="frm_summary" role="form" method="POST" action="{{ url('prod/reports/summary-report/search_summart_report') }}">
                        @csrf
                        <input type="hidden" name="id" id="id">
                        <div class="form-group row">
				            <div class="col-md-2.5">
				                <div class="input-group input-group-sm mb-3">
				                    <div class="input-group-prepend">
				                        <span class="input-group-text">Date From</span>
				                    </div>
				                    <input type="date" class="form-control" name="date_from" id="date_from" >
				                </div>
				            </div>

				            <div class="col-md-2">
				                <div class="input-group input-group-sm mb-3">
				                    <div class="input-group-prepend">
				                        <span class="input-group-text">Date To</span>
				                    </div>
				                    <input type="date" class="form-control" name="date_to" id="date_to">
				                </div>
				            </div>

							<div class="col-md-1">
                            	<button type="submit" class="btn btn-block bg-blue   permission-button">Search</button>
                            </div>
							<div class="col-md-1">
                            	<button type="button" id="btnDownload" class="btn btn-block bg-green  permission-button" disabled>Download</button>
                            </div>

                        </div>
                    </form>
		            <table class="table table-striped table-bordered table-sm dt-responsive" id="tbl_summary" width="100%">
		                <thead class="thead-dark">
                        <tr>
		                        <th rowspan="2">DATE</th>
		                        <th rowspan="2">M/C</th>
		                        <th rowspan="2">CODE</th>
		                        <th rowspan="2">ITEM</th>
		                        <th rowspan="2">ALLOY</th>
		                        <th rowspan="2">SIZE</th>
		                        <th rowspan="2">CLASS</th>
		                        <th rowspan="2">HEATNO</th>
		                        <th colspan="4">OUTPUT (QTY)</th>
		                        <th colspan="4">WEIGHT</th>
		                        <th colspan="2">REJECTION RATE</th>
		                        <th rowspan="2">JOB ORDER NO.</th>
	                        </tr>
	                        <tr>
		                        <th>TOTAL</th>
		                        <th>GOOD</th>
		                        <th>REWORK</th>
		                        <th>SCRAP</th>		                        
		                        <th>CRUDE WT.</th>
		                        <th>GOOD</th>
		                        <th>REWORK</th>
		                        <th>SCRAP</th>
		                        <th>REWORK</th>
		                        <th>SCRAP</th>
	                        </tr>
							

		                </thead>
		                <tbody id="tbl_operator_body"></tbody>
		            </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('scripts')
    <script type="text/javascript">
        var token = $('meta[name="csrf-token"]').attr('content');
        var code_permission = 'R0004';
    </script>	
	<script type="text/javascript">
        var token = $('meta[name="csrf-token"]').attr('content');
        var code_permission = 'R0004';
        var downloadExcel = "{{ url('/prod/reports/summary-report/downloadExcel') }}";
    </script>
    <script type="text/javascript" src="{{ asset('/js/pages/production/reports/summary-report.js') }}"></script>

@endpush
