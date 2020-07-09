@extends('layouts.app')

@push('styles')
    <style type="text/css">
        .dataTables_scrollHeadInner{
            width:100% !important;
        }
        .dataTables_scrollHeadInner table{
            width:100% !important;
        }
        .modal-backdrop {
            z-index: -1;
        }
    </style>
@endpush
@section('content')
 <?php
 $exist = 0;
foreach ($user_accesses as $user_access){
        if($user_access->code == "T0006" ){
            $exist++;
        }
}
    if($exist == 0){
         echo  '<script>window.history.back()</script>';
        exit;
    }
?>
<section class="content-header">
    <h1>Travel Sheet</h1>
</section>

<section class="content">
	<div class="row justify-content-center mb-5">
        <div class="col-md-10 col-xs-12 col-sm-12">
            <div class="form-group row">
                <div class="col-md-3">
                    <div class="input-group input-group-sm mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">From</span>
                        </div>
                        <input type="text" class="form-control" name="from" id="from">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="input-group input-group-sm mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">To</span>
                        </div>
                        <input type="text" class="form-control" name="to" id="to">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="input-group input-group-sm mb-5">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Status</span>
                        </div>
                        <select class="form-control" name="status" id="status">
                            <option value="null">All</option>
                            <option value="1">No quantity issued</option>
                            <option value="2">Ready for printing</option>
                            <option value="3">On Production</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <button type="button" class="btn btn-block btn-sm bg-blue" id="searchPS">
                        <i class="fa fa-search"></i> Search
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="box">
                <div class="box-body">
            		<div class="table-responsive">
            			<table class="table table-sm table-hover table-striped dt-responsive nowrap" style="width: 100%" id="tbl_jo_details">
            				<thead class="thead-dark">
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>Job Order No.</th>
                                    <th>Product Code</th>
                                    <th>Description</th>
                                    <th>Order Qty.</th>
                                    <th>Sched Qty.</th>
                                    <th>Issued Qty.</th>
                                    <th>Material Used</th>
                                    <th>Material Heat No.</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="tbl_jo_details_body"></tbody>
            			</table>
            		</div>
                </div>
            </div>
    	</div>
    </div>

    <div class="row justify-content-center">
{{--     	<div class="col-sm-2 mb-12">
    		<button class="btn btn-lg bg-blue btn-block mb-3" id="btn_cutting_schedule">
    			<i class="fa fa-cut"></i> Cutting Schedule
    		</button>
    	</div> --}}

    	<div class="col-sm-2 mb-12">
    		<button class="btn btn-lg bg-green btn-block mb-3" id="btn_travel_sheet_all_print_preview">
    			<i class="fa fa-file-text-o"></i> Travel Sheet
    		</button>
    	</div>
    </div>

</section>
@include('includes.modals.transactions.travel-sheet-modals')

@endsection

@push('scripts')
    <script type="text/javascript">
        var token = $('meta[name="csrf-token"]').attr('content');
        var joDetailsListURL = "{{ url('/transaction/travel-sheet/set-up/jo-list') }}";
        var getBomURL = "{{ url('/transaction/travel-sheet/set-up/process') }}";
        var getSc_noURL = "{{ url('transaction/travel-sheet/get-Sc_no') }}";

        var preparedByURL = "{{ url('/transaction/cutting-schedule/preparedby') }}";
        var leadersURL = "{{ url('/transaction/cutting-schedule/leader') }}";
        
        var getSetURL = "{{ url('masters/process-master/get-set') }}";

        var auth_user = "{{ Auth::user()->firstname.' '.Auth::user()->lastname }}";
        var pdfTravelSheetURL = "{{ url('/pdf/travel-sheet') }}";
        var getPreTravelSheetDataURL = "{{ url('/transaction/travel-sheet/pre-travel-sheet-data') }}";
        var code_permission = 'T0006';
        
    </script>
    <script type="text/javascript" src="{{ asset('/js/pages/ppc/transactions/travel-sheet/travel-sheet.js') }}"></script>
@endpush

