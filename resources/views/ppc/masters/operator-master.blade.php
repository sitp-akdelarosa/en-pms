@extends('layouts.app')

@section('title')
	Operator Master
@endsection

@section('content')
<section class="content-header">
    <h1>Operator Master</h1>
</section>

<section class="content">
	<div class="row justify-content-center">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <form method="post" action="{{ url('/masters/operator-master/save') }}" id="frm_operator">
                                @csrf
                                <input type="hidden" id="id" name="id" class="clear">

                                <div class="form-group row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Operator's ID:</span>
                                            </div>
                                            <input type="text" class="form-control input-sm validate clear readonly_op switch" name="operator_id" id="operator_id">
                                            <div id="operator_id_feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">First Name:</span>
                                            </div>
                                            <input type="text" class="form-control input-sm validate clear readonly_op switch" name="firstname" id="firstname">
                                            <div id="firstname_feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Last Name:</span>
                                            </div>
                                            <input type="text" class="form-control input-sm validate clear readonly_op switch" name="lastname" id="lastname">
                                            <div id="lastname_feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Nick Name:</span>
                                            </div>
                                            <input type="text" class="form-control input-sm validate clear readonly_op switch" name="nickname" id="nickname">
                                            <div id="nickname_feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Position:</span>
                                            </div>
                                            <input type="text" class="form-control input-sm validate clear readonly_op switch" name="position" id="position">
                                            <div id="position_feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <hr />

                                <div class="row justify-content-center">
                                    <div class="col-lg-3 col-md-3 col-sm-2 col-xs-2" id="div_add">
                                        <button type="button" class="btn btn-block btn-sm bg-green permission-button switch" id="btn_add">
                                            <i class="fa fa-plus"></i> Add New
                                        </button>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-2 col-xs-2" id="div_save">
                                        <button type="submit" class="btn btn-block btn-sm bg-blue permission-button switch" id="btn_save">
                                            <i class="fa fa-floppy-o"></i> Save
                                        </button>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-2 col-xs-2" id="div_clear">
                                        <button type="button" class="btn btn-block btn-sm bg-grey permission-button switch" id="btn_clear">
                                            <i class="fa fa-refresh"></i> Clear
                                        </button>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-2 col-xs-2" id="div_cancel">
                                        <button type="button" class="btn btn-block btn-sm bg-red permission-button switch" id="btn_cancel">
                                            <i class="fa fa-times"></i> Cancel
                                        </button>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-2 col-xs-2" id="div_delete">
                                        <button type="button" class="btn btn-block btn-sm bg-red permission-button switch" id="btn_delete">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="col-md-8">
                            {{-- <div class="table-responsive"> --}}
                                <table class="table table-sm table-striped table-bordered nowrap" id="tbl_operator" style="width:100%">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>
                                                <input type="checkbox" class="table-checkbox check_all">
                                            </th>
                                            <th></th>
                                            <th>Operator's ID</th>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Nick Name</th>
                                            <th>Position</th>
                                            <th>Date Created</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbl_operator_body"></tbody>
                                </table>
                            {{-- </div> --}}
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
        var getOperatorsURL = "{{ url('/masters/operator-master/get-operators') }}";
        var deleteOM = "{{ url('/masters/operator-master/destroy') }}";
        var disabledURL = "{{ url('/masters/operator-master/enable-disabled-operator') }}";
	</script>
	<script type="text/javascript" src="{{ asset('/js/pages/ppc/masters/operator-master/operator-master.js') }}"></script>
@endpush
