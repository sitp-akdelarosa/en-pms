@extends('layouts.app')

@section('title')
	Material Master
@endsection


@section('content')
 <?php
 $exist = 0;
foreach ($user_accesses as $user_access){
		if($user_access->code == "M0004" ){
			$exist++;
		}
}
	if($exist == 0){
		echo  '<script>window.history.back()</script>';
		exit;
	}
?>
<section class="content-header">
	<h1>Material Master</h1>
</section>

<section class="content">
	<div class="nav-tabs-custom">
		<ul class="nav nav-tabs">
			<li><a class="active" href="#material_code_assembly_tab" data-toggle="tab">Material Code Assembly</a></li>
			<li><a href="#material_code_tab" data-toggle="tab">Material Code (16 Code)</a></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="material_code_assembly_tab">

				<div class="row justify-content-center">
					<div class="col-md-8">
						<form method="post" action="{{ route('masters.material-master.save') }}" id="frm_mat_assembly">
							@csrf
							<input type="hidden" name="assembly_id" class="clear" id="assembly_id">
							<div class="row mb-5">
								<div class="col-md-6">

									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">Material Type:</span>
										</div>
										<select class="form-control select-validate clear switch readonly_assembly" name="mat_type" id="mat_type"></select>
										<div id="mat_type_feedback"></div>
									</div>

									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">Character #:</span>
										</div>
										<input type="number" class="form-control validate clear switch readonly_assembly" name="character_num" id="character_num" min="1" max="16">
										<div id="character_num_feedback"></div>
									</div>

								</div>

								<div class="col-md-6">

									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">Character Code:</span>
										</div>
										<input type="text" class="form-control validate clear switch readonly_assembly" name="character_code" id="character_code">
										<div id="character_code_feedback"></div>
									</div>

									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">Description:</span>
										</div>
										<input type="text" class="form-control validate clear switch readonly_assembly" name="description" id="description">
										<div id="description_feedback"></div>
									</div>
								</div>
							</div>

							<div class="row justify-content-center">
								<div class="col-lg-2 col-md-2 col-sm-3 col-xs-3 mb-5" id="div_add">
									<button type="button" id="btn_add_assembly" class="btn bg-green btn-block permission-button switch" data-toggle='popover' 
										data-content='This Button is to add new assembly data.' 
										data-placement='right'>
										<i class="fa fa-plus"></i> Add
									</button>
								</div>
								<div class="col-lg-2 col-md-2 col-sm-3 col-xs-3 mb-5" id="div_save">
									<button type="submit" id="btn_save_assembly" class="btn bg-blue btn-block permission-button switch" data-toggle="popover" 
											data-content="This Button is to save assembly data." 
											data-placement="right">
										<i class="fa fa-floppy-o"></i> Save
									</button>
								</div>
								<div class="col-lg-2 col-md-2 col-sm-3 col-xs-3 mb-5" id="div_clear">
									<button type="button" id="btn_clear_assembly" class="btn bg-gray btn-block switch" data-toggle="popover" 
											data-content="This Button is to clear assembly data in the input fields." 
											data-placement="right">
										<i class="fa fa-refresh"></i> Clear
									</button>
								</div>
								<div class="col-lg-2 col-md-2 col-sm-3 col-xs-3 mb-5" id="div_delete">
									<button type="button" id="btn_delete_assembly" class="btn bg-red btn-block permission-button switch" data-toggle="popover" 
											data-content="This Button is to delete selected assemblies in the table." 
											data-placement="right">
										<i class="fa fa-trash"></i> Delete
									</button>
								</div>
								<div class="col-lg-2 col-md-2 col-sm-3 col-xs-3 mb-5" id="div_cancel">
									<button type="button" id="btn_cancel_assembly" class="btn bg-red btn-block switch" data-toggle="popover" 
											data-content="This Button is to cancel saving assembly data." 
											data-placement="right">
										<i class="fa fa-times"></i> Cancel
									</button>
								</div>
							</div>
						</form>

					</div>
				</div>

				<div class="row justify-content-center">
					<div class="col-md-8">

						<div class="table-responsive">
							<table class="table table-sm table-hover table-striped table-bordered dt-responsive nowrap" id="tbl_matcode_assembly" style="width:100%">
								<thead class="thead-dark">
									<tr>
										<th width="5%">
											<input type="checkbox" class="table-checkbox check_all">
										</th>
										<th width="5%"></th>
										<th>Material Type</th>
										<th>Character #</th>
										<th>Character Code</th>
										<th>Description</th>
										<th>Date Updated</th>
									</tr>
								</thead>
								<tbody id="tbl_matcode_assembly_body"></tbody>
							</table>
						</div>

					</div>
				</div>
			</div>

			<div class="tab-pane" id="material_code_tab">
				<div class="row mb-5">
					<div class="col-md-4">

						<form action="{{ url('/masters/material-master/save-code') }}" id="frm_mat_code">
							@csrf

							<div class="input-group mb-3 input-group-sm">
								<div class="input-group-prepend">
									<span class="input-group-text">Material Type:</span>
								</div>
								<select name="material-type" class="form-control select-validate readonly_code switch_code" id="material-type"></select>
								<div id="material-type_feedback"></div>
							</div>

							<input type="hidden" name="material_type" id="material_type">
							<input type="hidden" name="material_id" id="material_id">

							<div class="input-group mb-3 input-group-sm">
								<div class="input-group-prepend">
									<span class="input-group-text">Material Code:</span>
								</div>
								<input type="text" class="form-control validate readonly_code switch_code" name="material_code" id="material_code" maxlength="16">
								<div id="material_code_feedback"></div>
							</div>

							<div class="input-group mb-3 input-group-sm">
								<div class="input-group-prepend">
									<span class="input-group-text">Description:</span>
								</div>
								<input type="text" class="form-control validate readonly_code switch_code" name="code_description" id="code_description">
								<div id="code_description_feedback"></div>
							</div>

							<hr>

							<div class="row">
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">Item:</span>
										</div>
										<input type="text" class="form-control validate readonly_code switch_code" name="item" id="item" readonly>
										<div id="item_feedback"></div>
									</div>
								</div>

								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">Alloy:</span>
										</div>
										<input type="text" class="form-control validate readonly_code switch_code" name="alloy" id="alloy" readonly>
										<div id="alloy_feedback"></div>
									</div>

								</div>
							</div>

							<div class="row">
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">Schedule:</span>
										</div>
										<input type="text" class="form-control validate readonly_code switch_code" name="schedule" id="schedule" readonly>
										<div id="schedule_feedback"></div>
									</div>
								</div>
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">Size:</span>
										</div>
										<input type="text" class="form-control validate readonly_code switch_code" name="size" id="size" readonly>
										<div id="size_feedback"></div>
									</div>
								</div>

								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">Standard Weight:</span>
										</div>
										<input type="number" class="form-control validate readonly_code switch_code" name="std_weight" id="std_weight" step=".01" readonly>
										<div id="std_weight_feedback"></div>
									</div>
								</div>
							</div>

							<hr>

							<div class="row">
								<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 mb-5">
									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">1st Cod:</span>
										</div>
										<select class="form-control select-code readonly_code switch_code" name="first" id="first">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<select class="form-control select-code readonly_code switch_code" name="first_val" id="first_val">
										<option value=""></option>
									</select>
								</div>
							</div>

							<div class="row">
								<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 mb-5">
									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">2nd Code:</span>
										</div>
										<select class="form-control select-code readonly_code switch_code" name="second" id="second">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<select class="form-control select-code readonly_code switch_code" name="second_val" id="second_val">
										<option value=""></option>
									</select>
								</div>
							</div>

							<div class="row" id="hide_3rd">
								<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 mb-5">
									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">3rd Code:</span>
										</div>
										<select class="form-control select-code readonly_code switch_code" name="third" id="third">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<select class="form-control select-code readonly_code switch_code" name="third_val" id="third_val">
										<option value=""></option>
									</select>
								</div>
							</div>

							<div class="row" id="hide_4th">
								<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 mb-5">
									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">4th Code:</span>
										</div>
										<select class="form-control select-code alloy readonly_code switch_code" name="forth" id="forth">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<select class="form-control select-code alloy readonly_code switch_code" name="forth_val" id="forth_val">
										<option value=""></option>
									</select>
								</div>
							</div>

							<div class="row" id="hide_5th">
								<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 mb-5">
									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">5th Code:</span>
										</div>
										<select class="form-control select-code readonly_code switch_code" name="fifth" id="fifth">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<select class="form-control select-code readonly_code switch_code" name="fifth_val" id="fifth_val">
										<option value=""></option>
									</select>
								</div>
							</div>

							<div class="row">
								<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 mb-5">
									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">7th Code:</span>
										</div>
										<select class="form-control select-code item readonly_code switch_code" name="seventh" id="seventh">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<select class="form-control select-code item readonly_code switch_code" name="seventh_val" id="seventh_val">
										<option value=""></option>
									</select>
								</div>
							</div>

							<div class="row" id="hide_8th">
								<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 mb-5">
									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">8th Code:</span>
										</div>
										<select class="form-control select-code readonly_code switch_code" name="eighth" id="eighth">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<select class="form-control select-code readonly_code switch_code" name="eighth_val" id="eighth_val">
										<option value=""></option>
									</select>
								</div>
							</div>

							<div class="row" id="hide_9th">
								<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 mb-5">
									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">9th Code:</span>
										</div>
										<select class="form-control select-code readonly_code switch_code" name="ninth" id="ninth">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<select class="form-control select-code readonly_code switch_code" name="ninth_val" id="ninth_val">
										<option value=""></option>
									</select>
								</div>
							</div>

							<div class="row">
								<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 mb-5">
									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">11th Code:</span>
										</div>
										<select class="form-control select-code size readonly_code switch_code" name="eleventh" id="eleventh">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<select class="form-control select-code size readonly_code switch_code" name="eleventh_val" id="eleventh_val">
										<option value=""></option>
									</select>
								</div>
							</div>

							<div class="row" id="hide_14th">
								<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 mb-5">
									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">14th Code:</span>
										</div>
										<select class="form-control select-code readonly_code switch_code" name="forteenth" id="forteenth">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<select class="form-control select-code readonly_code switch_code" name="forteenth_val" id="forteenth_val">
										<option value=""></option>
									</select>
								</div>
							</div>

							<div class="row justify-content-center">
								<div class="col-lg-3 col-md-4 mb-5" id="add_code">
									<button type="button" id="btn_add_code" class="btn bg-blue btn-block permission-button switch_code" data-toggle="popover" 
											data-content="This Button is to Add another Material Code." 
											data-placement="right">
										<i class="fa fa-plus"></i> Add new
									</button>
								</div>
								<div class="col-lg-3 col-md-4 mb-5" id="save_code">
									<button type="submit" id="btn_save" class="btn bg-green btn-block permission-button switch_code" data-toggle="popover" 
										data-content="This Button is to save Material Codes details." 
										data-placement="right">
										<i class="fa fa-floppy-o"></i> Save
									</button>
								</div>
								<div class="col-lg-3 col-md-4 mb-5" id="clear_code">
									<button type="button" id="btn_clear_code" class="btn bg-gray btn-block switch_code" data-toggle="popover" 
											data-content="This Button is to Clear Material Code details in input fields." 
											data-placement="right">
										<i class="fa fa-refresh"></i> Clear
									</button>
								</div>
								<div class="col-lg-3 col-md-4 mb-5" id="cancel_code">
									<button type="button" id="btn_cancel" class="btn bg-red btn-block switch_code" data-toggle="popover" 
										data-content="This Button is to cancel saving Material Code details." 
										data-placement="right">
										<i class="fa fa-times"></i> Cancel
									</button>
								</div>
							</div>
						</form>
					</div>

					<div class="col-md-8">
						<div class="table-reponsive">
							<table class="table table-striped table-sm mb-10 table-bordered nowrap" id="tbl_material_code" style="width:100%">
								<thead class="thead-dark">
									<tr>
										<th width="5%">
											<input type="checkbox" class="table-checkbox check_all_material">
										</th>
										<th></th>
										<th>Material Type</th>
										<th>Material Code</th>
										<th>Description</th>
										<th>Created By</th>
										<th>Date Updated</th>
										<th></th>
									</tr>
								</thead>
								<tbody id="tbl_material_code_body"></tbody>
							</table>
						</div>

						<div class="row justify-content-center">
							<div class="col-md-3 mb-5">
								<button type="button" id="btn_delete_material" class="btn bg-red btn-block permission-button" data-toggle="popover" 
										data-content="This Button is to delete selected Material Codes in the table." 
										data-placement="right">
									<i class="fa fa-trash"></i> Delete Material
								</button>
							</div>
							<div class="col-md-3 mb-5">
								<button type="button" id="btn_excel_material" class="btn bg-green btn-block"  data-toggle="popover" 
										data-content="This Button is to initialize Excel download." 
										data-placement="right">
									<i class="fa fa-file-excel-o"></i> Extract to Excel
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

@include('includes.modals.masters.material-master-modals')

@endsection

@push('scripts')
<script type="text/javascript">
	var assemblyListURL = "{{ url('/masters/material-master/material-list') }}";
	var assemblyDeleteURL = "{{ url('/masters/material-master/destroy') }}";
	var materialTypeURL = "{{ url('/masters/material-master/material-type') }}";
	var matCodeListURL = "{{ url('/masters/material-master/get-mat-code-list') }}";
	var showDropdownURL = "{{ url('/masters/material-master/show-dropdowns') }}";
	var showCodeURL = "{{ url('/masters/material-master/show-code') }}";
	var materialCodeDeleteURL = "{{ url('/masters/material-master/destroy-code') }}";
	var getMaterialTypeURL = "{{ url('/masters/material-master/get_dropdown_material_type') }}";
	var disabledURL = "{{ url('/masters/material-master/enable-disabled-material') }}";
	var downloadExcelFileURL = "{{ url('/masters/material-master/download-excel-file') }}";
	var AllMaterialTypeURL = "{{ url('/masters/material-master/all-material-types') }}";
</script>
<script type="text/javascript" src="{{ mix('/js/pages/ppc/masters/material-master/material-master.js') }}"></script>
@endpush
