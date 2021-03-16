@extends('layouts.app')

@section('title')
	Production Schedule
@endsection

@section('content')
 <?php
 $exist = 0;
foreach ($user_accesses as $user_access){
        if($user_access->code == "T0004" ){
            $exist++;
        }
}
    if($exist == 0){
        echo  '<script>window.history.back()</script>';
        exit;
    }
?>
<section class="content-header">
    <h1>Production Schedule</h1>
</section>
<section class="content">

    <div class="loadingOverlay"></div>

    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li><a class="active" href="#production_summary" data-toggle="tab">Production Summary</a></li>
            <li><a href="#jo_details" data-toggle="tab">JO Details Preparation</a></li>
            <li><a href="#travel_sheet" data-toggle="tab">J.O. Details List</a></li>
        </ul>
        <div class="tab-content">

            <div class="tab-pane active" id="production_summary">
                
                <div class="row justify-content-center">
                    <div class="col-md-2">
                        <button type="button" class="btn btn-block btn-lg bg-blue" id="btn_filter">
                            <i class="fa fa-search"></i> Search / Filter
                        </button>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover table-striped table-bordered nowrap" style="width: 100%" id="tbl_orders">
                                <thead class="thead-dark">
                                    <tr>
                                        {{-- <th width="3%"></th> --}}
                                        <th>SC No.</th>
                                        <th>Product Code</th>
                                        <th>Description</th>
                                        <th>Order Qty</th>
                                        <th>Sched Qty</th>
                                        <th>P.O. No.</th>
                                        <th>Status</th>
                                        <th>Upload Date</th>
                                    </tr>
                                </thead>
                                <tbody id="tbl_orders_body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>   
            </div>

            <div class="tab-pane" id="jo_details">
                <div class="row">

                    <div class="col-md-3">
                        <div class="input-group input-group-sm mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Withdrawal Slip No.</span>
                            </div>
                            <input type="text" class="form-control clear" name="rmw_no" id="rmw_no">
                            <div class="input-group-append">
                                <button class="btn btn-sm bg-blue" id="btn_search_withdrawal">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row justify-content-center mb-15">
                    <div class="col-md-12">
                        <div class="table-reponsive">
                            <table class="table table-sm table-bordered table-striped nowrap" style="width:100%" id="tbl_materials">
                                <thead class="thead-dark">
                                    <th></th>
                                    <th>Material Code</th>
                                    <th>Description</th>
                                    <th>Heat #</th>
                                    <th>Withdrawal Qty.(PCS)</th>
                                    <th>Assign Qty.</th>
                                    <th>Size</th>
                                    <th>Length</th>
                                    <th>Weight</th>
                                    <th>width</th>
                                    <th>Material Type</th>
                                </thead>
                                <tbody id="tbl_materials_body"></tbody>
                            </table>
                        </div>
                    </div>

                    <input type="hidden" id="total_withdrawal">
                    <input type="hidden" id="total_assign">
                </div>

                <form id="formbaba">
                    <div class="row justify-content-center mb-15">
                        {{-- <div class="col-md-4">

                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <p><input type="checkbox" class="table-checkbox" id="is_same" checked> Same In:Material Heat No. , Material Used and Lot No.</p>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Total Schedule Quantity:</span>
                                        </div>
                                        <input type="number" step="0.01" class="form-control clear" name="total_sched_qty" id="total_sched_qty" readonly value="0">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Total Heat No. Qty.:</span>
                                        </div>
                                        <input type="number" class="form-control clear" name="total_heat_qty" id="total_heat_qty" readonly value="0">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Job Order No.:</span>
                                        </div>
                                        <input list="jo_no" name="jo_no" class="form-control clear" id="jono" readonly>
                                        <datalist id="jo_no"></datalist>
                                    </div>
                                </div>
                            </div>
                        </div> --}}

                        <div class="col-md-4">
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Created By:</span>
                                        </div>
                                        <input type="text" class="form-control clear" name="created_by" id="created_by" value="{{Auth::user()->user_id}}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Created Date:</span>
                                        </div>
                                        <input type="text" class="form-control clear" name="created_date" id="created_date" value="{{date('m/d/Y')}}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Updated By:</span>
                                        </div>
                                        <input type="text" class="form-control clear" name="updated_by" id="updated_by" value="{{Auth::user()->user_id}}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Updated Date:</span>
                                        </div>
                                        <input type="text" class="form-control clear" name="updated_date" id="updated_date" value="{{date('m/d/Y')}}" readonly>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-md-1 col-sm-2 mb-5" id="btn_save_div">
                            <button type="button" id="btn_save" class="btn bg-green btn-block permission-button">
                                <i class="fa fa-floppy-o"></i> Save
                            </button>
                        </div>

                        {{-- <div class="col-md-1 col-sm-2 mb-5" id="btn_edit_div">
                            <button type="button" id="btn_edit" class="btn bg-blue btn-block permission-button">
                                <i class="fa fa-edit"></i> Edit
                            </button>
                        </div> --}}
                        <div class="col-md-1 col-sm-2 mb-5" id="btn_cancel_div">
                            <button type="button" id="btn_cancel" class="btn bg-red btn-block">
                                <i class="fa fa-times"></i> Cancel
                            </button>
                        </div>
                    </div>

                </form>
            </div>

            <div class="tab-pane" id="travel_sheet">
                <div class="row justify-content-center">
                    <div class="col-md-2">
                        <button type="button" class="btn btn-block btn-lg bg-blue" id="btn_jo_filter">
                            <i class="fa fa-search"></i> Search / Filter
                        </button>
                    </div>
                </div>
                <div class="row justify-content-center mb-15">
                    <div class="col-md-12">
                        <div class="table-reponsive">
                            <table class="table table-sm table-striped table-bordered nowrap" style="width:100%" id="tbl_travel_sheet">
                                <thead class="thead-dark">
                                    <tr>
                                        <th></th>
                                        <th>Job Order No.</th>
                                        <th>SC No.</th>
                                        <th>Product Code</th>
                                        <th>Description</th>
                                        <th>Order Qty.</th>
                                        <th>Sched Qty.</th>
                                        <th>Issued Qty.</th>
                                        <th>Withdrawal No.</th>
                                        <th>Material Used</th>
                                        <th>Material Heat No.</th>
                                        <th>Lot No.</th>
                                        <th>Ship Date</th>
                                        <th>Status</th>
                                        <th>Update Date</th>
                                    </tr>
                                </thead>
                                <tbody id="tbl_travel_sheet_body"></tbody>
                            </table>
                        </div>      
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@include('includes.modals.transactions.production-schedule-modals')

@endsection
@push('scripts')
    <script type="text/javascript">
        var ordersURL = "{{ url('/transaction/production-schedule/get-orders') }}";
        var getMaterialsURL = "{{ url('/transaction/production-schedule/get-materials') }}";
        var getProductsURL = "{{ url('/transaction/production-schedule/get-products') }}";
        var saveItemMaterialsURL = "{{ url('/transaction/production-schedule/save-item-materials') }}";
        var getItemMaterialsURL = "{{ url('/transaction/production-schedule/get-item-materials') }}";
        var saveJODetailsURL = "{{ url('/transaction/production-schedule/save-jo-details') }}";
        var getTravelSheetURL = "{{ url('/transaction/production-schedule/get-travel-sheet') }}";
        var cancelTravelSheetURL = "{{ url('/transaction/production-schedule/cancel-travel-sheet') }}";
        // var getMaterialUsedURL = "{{ url('/transaction/production-schedule/get-material-used') }}";
        // var getStandardMaterialUsedURL = "{{ url('/transaction/production-schedule/get-standard-material-used') }}";
        // var getMaterialHeatNoURL = "{{ url('/transaction/production-schedule/get-material-heat-no') }}";
        // var savejodetailsURL = "{{ url('/transaction/production-schedule/SaveJODetails') }}";
        // var getjosuggest = "{{ url('/transaction/production-schedule/JOsuggest') }}";
        // var getjotables = "{{ url('/transaction/production-schedule/getjotables') }}";
        // var getjotablesALL = "{{ url('/transaction/production-schedule/getjoALL') }}";
        // var getTravelSheetURL = "{{ url('/transaction/production-schedule/getTravelSheet') }}";
        // var OverIssuanceURL = "{{ url('/transaction/production-schedule/over-issuance') }}";
        // var SaveMaterialsURL = "{{ url('/transaction/production-schedule/save-materials') }}";
        // var getMaterialsURL = "{{ url('/transaction/production-schedule/get-materials') }}";
        var getJODetailsURL = "{{ url('/transaction/production-schedule/get-jo-details') }}";
        var deleteJoDetailItemURL = "{{ url('/transaction/production-schedule/delete-jo-detail-item') }}";
        var editJoDetailItemURL = "{{ url('/transaction/production-schedule/edit-jo-detail-item') }}";        
    </script>
    <script type="text/javascript" src="{{ asset('/js/pages/ppc/transactions/production-schedule/production-schedule.js') }}"></script>
@endpush
