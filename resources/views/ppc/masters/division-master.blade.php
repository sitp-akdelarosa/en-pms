@extends('layouts.app')

@section('content') 
<?php
 $exist = 0;
foreach ($user_accesses as $user_access){
		if($user_access->code == "M0001" ){
			$exist++;
		}
}
	if($exist == 0){
		echo  '<script>window.history.back()</script>';
		exit;
	}
?>
<section class="content-header">
	<h1>Division Master</h1>
</section>

<section class="content">
	<div class="row justify-content-center">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

			<div class="box">
				<div class="box-body">

					<div class="row justify-content-center">
						<div class="col-lg-10 col-md-12 col-sm-12 col-xs-12">

							<form id="frm_division" method="POST" action="{{ route('masters.division-master.save') }}" role="form">
								@csrf
								<input type="hidden" id="id" class="clear" name="id">

								<div class="row justify-content-center">
									<div class="col-lg-6 col-md-6 col-sm-6 mb-5">

										<div class="input-group mb-3 input-group-sm">
											<div class="input-group-prepend">
												<span class="input-group-text">Division Code:</span>
											</div>
											<input type="text" class="form-control form-control-sm validate clear" id="div_code" name="div_code">
											<div id="div_code_feedback"></div>
										</div>

										<div class="input-group mb-3 input-group-sm">
											<div class="input-group-prepend">
												<span class="input-group-text">Division Name:</span>
											</div>
											<input type="text" class="form-control form-control-sm validate clear" id="div_name" name="div_name">
											<div id="div_name_feedback"></div>
										</div>

										<div class="input-group mb-3 input-group-sm">
											<div class="input-group-prepend">
												<span class="input-group-text">Plant:</span>
											</div>
											<input type="text" class="form-control form-control-sm validate clear" id="plant" name="plant">
											<div id="plant_feedback"></div>
										</div>

										{{-- <div class="form-group row">
											<label for="div_code" class="col-sm-3 control-label">Division Code:</label>
											<div class="col-sm-9">
												<input type="text" class="form-control form-control-sm validate clear" id="div_code" name="div_code">
												<div id="div_code_feedback"></div>
											</div>
										</div>

										<div class="form-group row">
											<label for="div_name" class="col-sm-3 control-label">Division Name:</label>
											<div class="col-sm-9">
												<input type="text" class="form-control form-control-sm validate clear" id="div_name" name="div_name">
												<div id="div_name_feedback"></div>
											</div>
										</div>

										<div class="form-group row">
											<label for="plant" class="col-sm-3 control-label">Plant:</label>
											<div class="col-sm-9">
												<input type="text" class="form-control form-control-sm validate clear" id="plant" name="plant">
												<div id="plant_feedback"></div>
											</div>
										</div> --}}
									</div>

									<div class="col-lg-6 col-md-6 col-sm-6 mb-5">

										<div class="form-group row">
											<div class="col-sm-12 mb-3">
												<select name="leader" class="form-control form-control-sm select-validate clear" style="width:100%" id="leader"></select>
												<div id="leader_feedback"></div>
											</div>
										</div>

										<div class="form-group row">
											{{-- <label for="process" class="col-sm-3 control-label">Process:</label> --}}
											<div class="col-sm-6 mb-3">
												<button type="button" class="btn btn-block btn-sm bg-green" id="btn_process">
													Assign Process
												</button>
											</div>

											<div class="col-sm-6 mb-3">
												<button type="button" class="btn btn-block btn-sm bg-green" id="btn_prodline">
													Assign Product Line
												</button>
											</div>
										</div>

										{{-- <div class="form-group row">
											<label for="process" class="col-sm-3 control-label">Product Line:</label>
											
										</div> --}}

										<input type="hidden" name="user_id" id="user_id">

										{{-- <div class="form-group row">
											<label for="leader" class="col-sm-3 control-label">Leader:</label>
											<div class="col-sm-9">
												<select name="leader" class="form-control form-control-sm select-validate clear" id="leader"></select>
												<input type="hidden" name="user_id" id="user_id">
												<div id="leader_feedback"></div>
											</div>
										</div> --}}
									</div>
								</div>

								<div class="row mb-5 justify-content-center">
									<div class="col-lg-1 col-md-2 col-sm-3 col-xs-3 mb-3" id="btn_save_div">
										<button type="submit" class="btn btn-sm bg-blue btn-block permission-button" id="btn_save">
											<i class="fa fa-floppy-o"></i> Save
										</button>
									</div>
									<div class="col-lg-1 col-md-2 col-sm-3 col-xs-3 mb-3" id="btn_clear_div">
										<button type="button" class="btn btn-sm bg-grey btn-block" id="btn_clear">
											<i class="fa fa-refresh"></i> Clear
										</button>
									</div>
									<div class="col-lg-1 col-md-2 col-sm-3 col-xs-3 mb-3" id="btn_delete_div">
										<button type="button" class="btn btn-sm bg-red btn-block permission-button" id="btn_delete">
											<i class="fa fa-trash"></i> Delete
										</button>
									</div>
									<div class="col-lg-1 col-md-2 col-sm-3 col-xs-3 mb-3" id="btn_cancel_div">
										<button type="button" class="btn btn-sm bg-red btn-block" id="btn_cancel">
											<i class="fa fa-times"></i> Cancel
										</button>
									</div>
								</div>
							</form>

						</div>
					</div>



					<div class="row">
						<div class="col-md-9">
							<table class="table table-sm table-striped" id="tbl_division" style="width:100%">
								<thead class="thead-dark">
									<tr>
										<th>
											<input type="checkbox" class="table-checkbox check_all">
										</th>
										<th></th>
										<th>Division Code</th>
										<th>Division Name</th>
										<th>Plant</th>
										<th>Leader</th>
										<th>Date Created</th>
										<th></th>
									</tr>
								</thead>
								<tbody id="tbl_division_body"></tbody>
							</table>
						</div>
						<div class="col-md-3">
							<div class="mb-40"></div>
							<table class="table table-sm table-striped dt-responsive nowrap" id="tbl_view_process" style="width:100%">
								<thead class="thead-dark">
									<tr>
										<th>Process</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>


				</div>
			</div>

		</div>

	</div>

	

	@include('includes.modals.masters.division-master-modal')
	@include('includes.modals.system-modals')


</section>
@endsection
@push('scripts')
	<script type="text/javascript">
		var token = $('meta[name="csrf-token"]').attr('content');
		var divListURL = "{{ url('masters/division-master/list') }}";
		var disableEnableDivisionURL = "{{ url('masters/division-master/disableEnableDivision') }}";
		var divDeleteURL = "{{ url('masters/division-master/destroy') }}";
		var getuserIDURL = "{{ url('masters/division-master/getuserID') }}";
		var getProcessURL = "{{ url('masters/division-master/get-process') }}";
		var getProductlineURL = "{{ url('masters/division-master/get-productline') }}";
		var getLeaderURL = "{{ url('masters/division-master/get-leader') }}";
		var code_permission = "M0001";
		var dropdownProduct = "{{ url('/admin/assign-production-line/productline-select') }}";
		
	</script>
	<script type="text/javascript" src="{{ asset('/js/pages/ppc/masters/division-master/division-master.js') }}"></script>
@endpush