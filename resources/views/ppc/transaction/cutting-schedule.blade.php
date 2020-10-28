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
                    <div class="col-md-6">

                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">J.O. #:</span>
                                    </div>
                                    <input type="text" class="form-control validate clear" name="trans_no" id="trans_no"
                                        maxlength="16">
                                    <div id="trans_no_feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-sm table-bordered dt-responsive nowrap mb-5"
                                    id="tbl_cut_sched">
                                    <thead>
                                        <th>
                                            <input type="checkbox" class="table-checkbox check_all_items">
                                        </th>
                                        <th>Alloy</th>
                                        <th>Size</th>
                                        <th>Item</th>
                                        <th>Class</th>
                                        <th>SC No.</th>
                                        <th>Order Qty.</th>
                                        <th>Qty. Needed</th>
                                        <th>Qty. Cut</th>
                                        <th>Material Description</th>
                                    </thead>
                                    <tbody id="tbl_cut_sched_body">
                                        <tr>
                                            <td colspan="11" class="text-center">No data displayed.</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <hr>

                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-md-3 col-xs-12 col-sm-10">
                                <button class="btn btn-sm btn-block bg-blue" id="btn_add">
                                    <i class="fa fa-plus"></i> Add Details
                                </button>
                            </div>
                        </div>

                    </div>



                    <div class="col-md-6">

                        <form id="frm_cut_sched">                            

                            <div class="form-group row mb-10">
                                <div class="col-md-6">
                                    <div class="input-group input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Withdrawal Slip #:</span>
                                        </div>
                                        <input type="text" class="form-control validate clear" name="withdrawal_slip" id="withdrawal_slip" maxlength="16">
                                        <div id="withdrawal_slip_feedback"></div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="input-group input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Date Issued:</span>
                                        </div>
                                        <input type="date" class="form-control validate" name="date_issued"
                                            id="date_issued">
                                        <div id="date_issued_feedback"></div>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-sm table-bordered dt-responsive nowrap mb-10"
                                        id="tbl_cut_details">
                                        <thead>
                                            <th>No.</th>
                                            <th>Alloy</th>
                                            <th>Size</th>
                                            <th>Item</th>
                                            <th>Class</th>
                                            <th>SC No.</th>
                                            <th>Order Qty.</th>
                                            <th>Qty. Needed</th>
                                            <th>Qty. Cut</th>
                                            <th>Material Description</th>
                                            <th></th>
                                        </thead>
                                        <tbody id="tbl_cut_details_body">
                                            <tr>
                                                <td colspan="11" class="text-center">No data displayed.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="input-group input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Prepared By:</span>
                                        </div>
                                        <input type="text" class="form-control validate" name="prepared_by"
                                            id="prepared_by"
                                            value="{{ Auth::user()->firstname.' '.Auth::user()->lastname }}">
                                        <div id="prepared_by_feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <select type="text" class="form-control select-validate clear" style="width:100%" name="leader"
                                        id="leader"></select>
                                    <div id="leader_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="input-group input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">ISO No. For:</span>
                                        </div>
                                        <select class="form-control select-validate clear" name="iso_control_no"
                                            id="iso_control_no"></select>
                                        <div id="iso_control_no_feedback"></div>
                                    </div>

                                    <hr>

                                </div>
                            </div>

                            <div class="row justify-content-center">
                                <div class="col-md-3 col-xs-4 col-sm-10">
                                    <button type="button" class="btn bg-green btn-block" id="btn_save">
                                        <i class="fa fa-floppy-o"></i> Save
                                    </button>
                                </div>
                                <div class="col-md-3 col-xs-4 col-sm-10">
                                    <button type="button" class="btn bg-red btn-block" id="btn_cancel">
                                        <i class="fa fa-close"></i> Cancel
                                    </button>
                                </div>
                                <div class="col-md-3 col-xs-4 col-sm-10">
                                    <button type="button" class="btn bg-purple btn-block" id="btn_print_preview">
                                        <i class="fa fa-eye"></i> Print Preview
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div id="cutting_sched_reprint" class="tab-pane">
                <div class="row justify-content-center">
                    <div class="col-sm-10">
                        <table class="table table-sm table-hover table-striped table-bordered nowrap mb-5" width="100%" id="tbl_cut_sched_reprint">
                            <thead class="thead-dark">
                                <th width="3.11%"></th>
                                <th width="13.11%">J.O Number/s</th>
                                <th width="13.11%">Withdrawal Slip #</th>
                                <th width="11.11%">ISO Ctrl #</th>
                                <th width="11.11%">Date Issued</th>
                                <th width="11.11%">Machine #</th>
                                <th width="13.11%">Leader</th>
                                <th width="13.11%">Prepared By</th>
                                <th width="11.11%">Date Created</th>
                            </thead>
                            <tbody id="tbl_cut_sched_reprint_body">
                                <tr>
                                    <td colspan="11" class="text-center">No data displayed.</td>
                                </tr>
                            </tbody>
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
var token = $('meta[name="csrf-token"]').attr('content');
var code_permission = "T0005";
var materialCuttingSchedURL = "{{ url('/transaction/cutting-schedule/materials') }}";
var getProdLineURL = "{{ url('/transaction/cutting-schedule/prodline') }}";
var saveCutSchedURL = "{{ url('/transaction/cutting-schedule/save') }}";
var pdfCuttingScheduleURL = "{{ url('/pdf/cutting-schedule') }}";
var getAllOperatorsURL = "{{ url('/helpers/getall-operators') }}";
var getCutSchedDetailsURL = "{{ url('/transaction/cutting-schedule/cut-sched-details') }}";
var pdfCuttingScheduleReprintURL = "{{ url('/pdf/cutting-schedule-reprint') }}";
var getLeaderURL = "{{ url('/transaction/cutting-schedule/cut-sched-leader') }}";
</script>
<script type="text/javascript" src="{{ asset('/js/pages/ppc/transactions/cutting-schedule/cutting-schedule.js') }}">
</script>
@endpush