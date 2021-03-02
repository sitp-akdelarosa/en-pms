@extends('layouts.app')

@section('title')
	Cutting Schedule
@endsection

@section('content')
<?php
$exist = 0;
foreach ($user_accesses as $user_access) {
    if ($user_access->code == "T0005") {
        $exist++;
    }
}
if ($exist == 0) {
    echo '<script>window.history.back()</script>';
    exit;
}
?>
<section class="content-header">
    <h1>Cutting Schedule</h1>
</section>

<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li><a href="#cutting_sched_entry" data-toggle="tab" class="active show">Cutting Schedule Entry</a></li>
            <li><a href="#cutting_sched_reprint" data-toggle="tab" class="show">Cutting Schedule Reprint</a></li>
        </ul>
        <div class="tab-content">
            <div id="cutting_sched_entry" class="tab-pane active">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12">
                        <div class="form-group row">
                            <div class="col-lg-4 col-md-4">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Withdrawal Slip #:</span>
                                    </div>
                                    <input type="text" class="form-control validate clear" name="withdrawal_slip_no" id="withdrawal_slip_no" maxlength="16">
                                    <div class="input-group-append">
                                        <button class="btn btn-sm bg-blue" id="btn_search_withdrawal">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                    <div id="withdrawal_slip_no_feedback"></div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Date Issued:</span>
                                    </div>
                                    <input type="date" class="form-control validate" name="date_issued" id="date_issued" value="{{ date('Y-m-d') }}">
                                    <div id="date_issued_feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12">
                        <div class="table-reponsive">
                            <table class="table table-sm table-striped table-bordered nowrap" style="width:100%" id="tbl_jo">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>
                                            <input type="checkbox" class="table-checkbox chk_all_jo"/>
                                        </th>
                                        <th>J.O. #</th>
                                        <th>Alloy</th>
                                        <th>Size</th>
                                        <th>Item</th>
                                        <th>Class</th>
                                        <th>Lot #</th>
                                        <th>SC_#</th>
                                        <th>J.O. Qty.</th>
                                        <th>Cut Weight</th>
                                        <th>Cut Length</th>
                                        <th>Cut Width</th>
                                        <th>Material Used</th>
                                        <th>Material Heat #</th>
                                        <th>Supplier Heat #</th>
                                        <th>Needed Qty</th>
                                        <th>status</th>
                                    </tr>
                                </thead>
                                <tbody id="tbl_jo_body"></tbody>
                            </table>
                        </div>  
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-lg-4 col-md-4">
                        <div class="input-group input-group-sm mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Prepared By:</span>
                            </div>
                            <input type="text" class="form-control validate" name="prepared_by" id="prepared_by" value="{{ Auth::user()->firstname.' '.Auth::user()->lastname }}">
                            <div id="prepared_by_feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 mb-3">
                        <select type="text" class="form-control select-validate clear" style="width:100%" name="leader" id="leader"></select>
                        <div id="leader_feedback"></div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-4">
                        <div class="input-group input-group-sm mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">ISO No. For:</span>
                            </div>
                            <select class="form-control select-validate clear" name="iso_control_no" id="iso_control_no"></select>
                            <div id="iso_control_no_feedback"></div>
                        </div>

                        <hr>

                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-2 col-xs-4 col-sm-10">
                        <button type="button" class="btn bg-green btn-block" id="btn_save">
                            <i class="fa fa-floppy-o"></i> Save
                        </button>
                    </div>
                    <div class="col-md-2 col-xs-4 col-sm-10">
                        <button type="button" class="btn bg-red btn-block" id="btn_cancel">
                            <i class="fa fa-close"></i> Cancel
                        </button>
                    </div>
                    <div class="col-md-2 col-xs-4 col-sm-10">
                        <button type="button" class="btn bg-purple btn-block" id="btn_print_preview">
                            <i class="fa fa-eye"></i> Print Preview
                        </button>
                    </div>
                </div>
            </div>


            <div id="cutting_sched_reprint" class="tab-pane">
                <div class="row justify-content-center">
                    <div class="col-sm-10">
                        <table class="table table-sm table-hover table-striped table-bordered nowrap mb-5" width="100%" id="tbl_cut_sched">
                            <thead class="thead-dark">
                                <th></th>
                                <th>J.O Number/s</th>
                                <th>Withdrawal Slip #</th>
                                <th>ISO Ctrl #</th>
                                <th>Date Issued</th>
                                <th>Leader</th>
                                <th>Prepared By</th>
                                <th>Date Created</th>
                            </thead>
                            <tbody id="tbl_cut_sched_body"></tbody>
                        </table>
                    </div>
                </div>
            </div>


        </div>
    </div>
    @include('includes.modals.system-modals')
</section>
@endsection
@push('scripts')
    <script type="text/javascript">
        var JOListURL = "{{ url('/transaction/cutting-schedule/jo-list') }}";
        var getLeaderURL = "{{ url('/transaction/cutting-schedule/cut-sched-leader') }}";
        var saveCuttSchedURL = "{{ url('/transaction/cutting-schedule/save-cutt-sched') }}";
        var CuttSchedListURL = "{{ url('/transaction/cutting-schedule/cut-sched-list') }}";
        // var getProdLineURL = "{{ url('/transaction/cutting-schedule/prodline') }}";
        // var saveCutSchedURL = "{{ url('/transaction/cutting-schedule/save') }}";
        var pdfCuttingScheduleURL = "{{ url('/pdf/cutting-schedule') }}";
        // var getAllOperatorsURL = "{{ url('/helpers/getall-operators') }}";
        // var getCutSchedDetailsURL = "{{ url('/transaction/cutting-schedule/cut-sched-details') }}";
        var pdfCuttingScheduleReprintURL = "{{ url('/pdf/cutting-schedule-reprint') }}";
        
    </script>
    <script type="text/javascript" src="{{ asset('/js/pages/ppc/transactions/cutting-schedule/cutting-schedule.js') }}"></script>
@endpush