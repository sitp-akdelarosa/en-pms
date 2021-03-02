@extends('layouts.app')

@section('title')
	Process Master
@endsection

@section('content')
 <?php
 $exist = 0;
foreach ($user_accesses as $user_access){
        if($user_access->code == "M0005" ){
            $exist++;
        }
}
    if($exist == 0){
        echo  '<script>window.history.back()</script>';
        exit;
    }
?>
<section class="content-header">
    <h1>Process Master</h1>
</section>

<section class="content">
	<div class="row justify-content-center">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="box">
                <div class="box-body">
                    <div class="row mb-5">

                        <div class="col-md-4">
                            <h4>Add a Set</h4>
                            <form method="post" action="{{ url('/masters/process-master/save-set') }}" id="frm_add_set">
                                @csrf
                                <input type="hidden" class="form-control validate" id="set_id" name="set_id">

                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <div class="input-group mb-3 input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Set:</span>
                                            </div>
                                            <input type="text" class="form-control validate" id="set" name="set">
                                            <div id="set_feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <select class="form-control select-validate" multiple="multiple" id="product_line" name="product_line[]">
                                        </select>
                                        <div id="product_line_feedback"></div>
                                    </div>
                                </div>

                                <div class="form-group row justify-content-center" >
                                    <div class="col-md-3 col-sm-4 col-xs-5" id="cancel">
                                        <button type="button" class="btn btn-block btn-secondary" title="Cancel Add Set" id="btn_cancel_set">
                                            <i class="fa fa-refresh"></i> Cancel
                                        </button>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-5" id="add">
                                        <button type="submit" class="btn btn-block bg-green" title="Add new Set" id="btn_add_set">
                                            <i class="fa fa-plus"></i> Add set
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <div class="row mb-10">
                                {{-- <div class="col-md-12" id="set_list" style="width: 100%;"></div> --}}

                                <table class="table table-sm table-hover table-bordered table-striped nowrap" id="tbl_added_sets" style="width:100%">
                                    <thead class="thead-dark">
                                        <th width="5%">
                                            <input type="checkbox" class="table-checkbox check_all_sets">
                                        </th>
                                        <th width="90%">Set Name</th>
                                        <th width="5%"></th>
                                    </thead>
                                    <tbody id="tbl_added_sets_body"></tbody>
                                </table>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-block btn-sm bg-red permission-button" id="btn_delete_set">
                                        <i class="fa fa-trash"></i> Delete Set
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <h4>Choose Processes</h4>

                            <table class="table table-sm table-hover table-striped table-bordered dt-responsive nowrap" id="tbl_select_process" width="100%">
                                <thead class="thead-dark">
                                    <th width="5%">
                                        <input type="checkbox" class="table-checkbox check_all">
                                    </th>
                                    <th width="95%">Process</th>
                                </thead>
                                <tbody id="tbl_select_process_body"></tbody>
                            </table>

                            <div class="row justify-content-center">
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-sm btn-block bg-green" id="btn_add_process">
                                        <i class="fa fa-plus"></i> Add to Set
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="loadingOverlay"></div>
                            <h4>Selected Process</h4>

                            <div class="row mb-5">
                                <div class="col-sm-12">
                                    <div class="form-group row">
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <select class="form-control select2 select-validate" multiple="multiple" id="selected_set" style="width: 100%;" data-toggle="popover" title="Reminder!" data-content="If one of the selected set has saved processes, the saved processes will be overight with selected processes below." data-placement="top">
                                            </select>
                                            <div id="selected_set_feedback"></div>
                                            {{-- <div class="input-group mb-3 input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Select a Set:</span>
                                                </div>
                                            </div> --}}
                                        </div>

                                        <input type="hidden" id="state">

                                        {{-- <label for="" class="control-label col-sm-2 mt-5">Set:</label>
                                        <div class="col-sm-10">
                                            <select class="form-control select-validate" id="selected_set" name="selected_set"></select>
                                            <div id="selected_set_feedback"></div>
                                        </div> --}}
                                    </div>

                                </div>
                            </div>


                            <div class="row mb-10">
                                <div class="col-sm-12" id="sortable_process"></div>
                            </div>

                            <div class="row justify-content-center">
                                <div class="col-sm-3">
                                    <button type="submit" id="btn_save_process" class="btn bg-blue btn-block permission-button">
                                        <i class="fa fa-floppy-o"></i> Save
                                    </button>
                                </div>
                                <div class="col-sm-3">
                                    <button type="button" id="btn_cancel" class="btn bg-red btn-block permission-button">
                                        <i class="fa fa-times"></i> Cancel
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
		
    </div>
</section>
@endsection
@push('scripts')
	<script type="text/javascript">
		var processListURL = "{{ url('masters/process-master/process-list') }}";
		var saveProcessURL = "{{ url('masters/process-master/save') }}";
		var selectedProcessListURL = "{{ url('masters/process-master/selected-process-list') }}";
		var getSetURL = "{{ url('masters/process-master/get-set') }}";
        var deleteSetURL = "{{ url('masters/process-master/delete-set') }}";
        var productLineURL = "{{ url('masters/process-master/get-product-line') }}";
        var selectedProductLineURL = "{{ url('masters/process-master/selected-product-line') }}";
    </script>
	<script type="text/javascript" src="{{ asset('/js/pages/ppc/masters/process-master/process-master.js') }}"></script>
@endpush
