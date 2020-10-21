@extends('layouts.app')

@section('title')
	Operator's Output
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
                    <form id="frm_operator" role="form" method="POST" action="{{ url('prod/reports/operators-output/search_operator') }}">
                        @csrf
                        <input type="hidden" name="id" id="id">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Operator ID:</span>
                                    </div>
                                    <input class="form-control input-sm validate clear" name="search_operator" id="search_operator">
                                    <div id="search_operator_feedback"></div>
                                </div>
                            </div>
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

                        </div>
                    </form>
		            <table class="table table-striped table-sm dt-responsive" id="tbl_operator" width="100%">
		                <thead class="thead-dark">
		                    <tr>
		                        <th>J.O. No.</th>
		                        <th>Product Code</th>
		                        <th>Total Qty</th>
		                        <th>Previous Process</th>
		                        <th>Current Process</th>
		                        <th>Unprocessed</th>
		                        <th>Good</th>
		                        <th>Reworked</th>
		                        <th>Scrap</th>
		                        <th>Date</th>
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
    <script type="text/javascript" src="{{ asset('/js/pages/production/reports/operators-output.js') }}"></script>
@endpush