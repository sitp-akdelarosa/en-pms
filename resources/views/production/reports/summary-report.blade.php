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
    <h1>Production Summary Report</h1>
</section>
<section class="content">
	<div class="row justify-content-center">
        <div class="col-lg-12 col-md-10 col-sm-12 col-xs-12">
            <div class="box">
                <div class="box-body">
					<div class="row">
						<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
							<button type="button" id="btn_filter" class="btn btn-block bg-blue permission-button">Search / Filter</button>
						</div>
						<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
							<button type="button" id="btn_download" class="btn btn-block bg-green permission-button">Download</button>
						</div>

						<input type="hidden" id="dl_date_from" />
						<input type="hidden" id="dl_date_to" />
						<input type="hidden" id="dl_jo_no" />
						<input type="hidden" id="dl_prod_code" />
						<input type="hidden" id="dl_code_description" />
						<input type="hidden" id="dl_div_code" />
						<input type="hidden" id="dl_process_name" />
					</div>
		            <table class="table table-striped table-bordered table-sm nowrap" id="tbl_summary" width="100%">
		                <thead class="thead-dark">
                        	<tr>
		                        <th rowspan="2">DATE</th>
		                        <th rowspan="2">M/C</th>
		                        <th rowspan="2">CODE</th>
								<th rowspan="2">DESCRIPTION</th>
		                        <th rowspan="2">ITEM</th>
		                        <th rowspan="2">ALLOY</th>
		                        <th rowspan="2">SIZE</th>
		                        <th rowspan="2">CLASS</th>
		                        <th rowspan="2">HEAT NO.</th>
								<th rowspan="2">LOT NO.</th>
								<th rowspan="2">DIV. NO.</th>
								<th rowspan="2">PROCESS</th>
		                        <th colspan="6">OUTPUT (QTY)</th>
		                        <th colspan="6">WEIGHT</th>
		                        <th colspan="2">REJECTION RATE</th>
		                        <th rowspan="2">JOB ORDER NO.</th>
	                        </tr>
	                        <tr>
		                        <th>TOTAL</th>
		                        <th>GOOD</th>
		                        <th>REWORK</th>
		                        <th>SCRAP</th>
								<th>ALLOY MIX</th>
								<th>CONVERT</th>
		                        <th>UNIT WT.</th>
		                        <th>GOOD</th>
		                        <th>REWORK</th>
		                        <th>SCRAP</th>
								<th>ALLOY MIX</th>
								<th>CONVERT</th>
		                        <th>REWORK</th>
		                        <th>SCRAP</th>
	                        </tr>
		                </thead>
		                <tbody></tbody>
		            </table>
                </div>
            </div>
        </div>
    </div>
</section>
@include('includes.modals.reports.production-summary-report')
@endsection
@push('scripts')
    <script type="text/javascript">
        var downloadExcel = "{{ url('/prod/reports/summary-report/downloadExcel') }}";
    </script>
    <script type="text/javascript" src="{{ asset('/js/pages/production/reports/summary-report.js') }}"></script>

@endpush
