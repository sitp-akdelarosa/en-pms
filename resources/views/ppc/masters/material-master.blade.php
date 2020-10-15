@extends('layouts.app')

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
										<select class="form-control select-validate clear switch" name="mat_type" id="mat_type"></select>
										<div id="mat_type_feedback"></div>
									</div>

									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">Character #:</span>
										</div>
										<input type="number" class="form-control validate clear switch" name="character_num" id="character_num" min="1" max="16">
										<div id="character_num_feedback"></div>
									</div>

									{{-- <div class="form-group row">
										<label for="mat_type" class="col-sm-3 control-label">Material Type:</label>
										<div class="col-sm-9">
											<select class="form-control select-validate clear switch" name="mat_type" id="mat_type"></select>
											<div id="mat_type_feedback"></div>
										</div>
									</div>

									<div class="form-group row">
										<label for="character_num" class="col-sm-3 control-label">Character #:</label>
										<div class="col-sm-9">
											<input type="number" class="form-control validate clear switch" name="character_num" id="character_num" min="1" max="16">
											<div id="character_num_feedback"></div>
										</div>
									</div> --}}

								</div>

								<div class="col-md-6">

									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">Character Code:</span>
										</div>
										<input type="text" class="form-control validate clear switch" name="character_code" id="character_code">
										<div id="character_code_feedback"></div>
									</div>

									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">Description:</span>
										</div>
										<input type="text" class="form-control validate clear switch" name="description" id="description">
										<div id="description_feedback"></div>
									</div>

									{{-- <div class="form-group row">
										<label for="character_code" class="col-sm-3 control-label">Character Code:</label>
										<div class="col-sm-9">
											<input type="text" class="form-control validate clear switch" name="character_code" id="character_code">
											<div id="character_code_feedback"></div>
										</div>
									</div>

									<div class="form-group row">
										<label for="description" class="col-sm-3 control-label">Description:</label>
										<div class="col-sm-9">
											<input type="text" class="form-control validate clear switch" name="description" id="description">
											<div id="description_feedback"></div>
										</div>
									</div> --}}

								</div>
							</div>

							<div class="row justify-content-center">
								<div class="col-md-2 mb-5">
									<button type="submit" id="btn_save_assembly" class="btn bg-blue btn-block permission-button">
										<i class="fa fa-floppy-o"></i> Save
									</button>
								</div>
								<div class="col-md-2 mb-5" id="div_clear">
									<button type="button" id="btn_clear_assembly" class="btn bg-gray btn-block">
										<i class="fa fa-refresh"></i> Clear
									</button>
								</div>
								<div class="col-md-2 mb-5" id="div_delete">
									<button type="button" id="btn_delete_assembly" class="btn bg-red btn-block permission-button">
										<i class="fa fa-trash"></i> Delete
									</button>
								</div>
								<div class="col-md-2 mb-5" id="div_cancel">
									<button type="button" id="btn_cancel_assembly" class="btn bg-red btn-block">
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
							<table class="table table-sm table-hover table-striped dt-responsive nowrap" id="tbl_matcode_assembly" style="width:100%">
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
										<th>Date Created</th>
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
								<select name="material-type" class="form-control select-validate" id="material-type"></select>
								<div id="material-type_feedback"></div>
							</div>

							{{-- <div class="form-group row mb-10">
								<label for="" class="control-label col-sm-3 mt-5">Material Type:</label>
								<div class="col-sm-9">
									<select name="material-type" class="form-control select-validate" id="material-type"></select>
									<div id="material-type_feedback"></div>
								</div>
							</div> --}}
							<input type="hidden" name="material_type" id="material_type">
							<input type="hidden" name="material_id" id="material_id">

							<div class="input-group mb-3 input-group-sm">
								<div class="input-group-prepend">
									<span class="input-group-text">Material Code:</span>
								</div>
								<input type="text" class="form-control validate" name="material_code" id="material_code" maxlength="16">
								<div id="material_code_feedback"></div>
							</div>

							<div class="input-group mb-3 input-group-sm">
								<div class="input-group-prepend">
									<span class="input-group-text">Description:</span>
								</div>
								<input type="text" class="form-control validate" name="code_description" id="code_description">
								<div id="code_description_feedback"></div>
							</div>

							{{-- <div class="form-group row">
								<label for="" class="control-label col-sm-3 mt-5">Material Code:</label>
								<div class="col-sm-9">
									<input type="text" class="form-control validate" name="material_code" id="material_code" maxlength="16">
									<div id="material_code_feedback"></div>
								</div>
							</div>

							<div class="form-group row mb-10">
								<label for="" class="control-label col-sm-3 mt-5">Description:</label>
								<div class="col-sm-9">
									<input type="text" class="form-control validate" name="code_description" id="code_description">
									<div id="code_description_feedback"></div>
								</div>
							</div> --}}

							<hr>

							<div class="row">
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">Item:</span>
										</div>
										<input type="text" class="form-control validate" name="item" id="item" readonly>
										<div id="item_feedback"></div>
									</div>
								</div>

								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">Alloy:</span>
										</div>
										<input type="text" class="form-control validate" name="alloy" id="alloy" readonly>
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
										<input type="text" class="form-control validate" name="schedule" id="schedule" readonly>
										<div id="schedule_feedback"></div>
									</div>
								</div>
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">Size:</span>
										</div>
										<input type="text" class="form-control validate" name="size" id="size" readonly>
										<div id="size_feedback"></div>
									</div>
								</div>

								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">Standard Weight:</span>
										</div>
										<input type="number" class="form-control validate" name="std_weight" id="std_weight" step=".01" readonly>
										<div id="std_weight_feedback"></div>
									</div>
								</div>
							</div>

							{{-- <div class="form-group row">
								<label for="" class="control-label col-sm-3 mt-5">Item:</label>
								<div class="col-sm-9">
									<input type="text" class="form-control validate" name="item" id="item" readonly>
									<div id="item_feedback"></div>
								</div>
							</div>

							<div class="form-group row">
								<label for="" class="control-label col-sm-3 mt-5">Alloy:</label>
								<div class="col-sm-9">
									<input type="text" class="form-control validate" name="alloy" id="alloy" readonly>
									<div id="alloy_feedback"></div>
								</div>
							</div> --}}

							{{-- <div class="form-group row">
								<label for="" class="control-label col-sm-3 mt-5">Schedule:</label>
								<div class="col-sm-9">
									<input type="text" class="form-control validate" name="schedule" id="schedule" readonly>
									<div id="schedule_feedback"></div>
								</div>
							</div>

							<div class="form-group row">
								<label for="" class="control-label col-sm-3 mt-5">Size:</label>
								<div class="col-sm-9">
									<input type="text" class="form-control validate" name="size" id="size" readonly>
									<div id="size_feedback"></div>
								</div>
							</div> --}}

							<hr>

							<div class="row">
								<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 mb-5">
									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">1st Cod:</span>
										</div>
										<select class="form-control select-code" name="first" id="first">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<select class="form-control select-code" name="first_val" id="first_val">
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
										<select class="form-control select-code" name="second" id="second">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<select class="form-control select-code" name="second_val" id="second_val">
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
										<select class="form-control select-code" name="third" id="third">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<select class="form-control select-code" name="third_val" id="third_val">
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
										<select class="form-control select-code alloy" name="forth" id="forth">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<select class="form-control select-code alloy" name="forth_val" id="forth_val">
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
										<select class="form-control select-code" name="fifth" id="fifth">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<select class="form-control select-code" name="fifth_val" id="fifth_val">
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
										<select class="form-control select-code item" name="seventh" id="seventh">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<select class="form-control select-code item" name="seventh_val" id="seventh_val">
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
										<select class="form-control select-code" name="eighth" id="eighth">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<select class="form-control select-code" name="eighth_val" id="eighth_val">
										<option value=""></option>
									</select>
								</div>
							</div>

							{{-- <div class="form-group row">
								<label for="first" class="col-sm-3 control-label mt-5">1st Code:</label>
								<div class="col-sm-6">
									<select class="form-control select-code mb-3" name="first" id="first">
										<option value=""></option>
									</select>
								</div>
								<div class="col-sm-3">
									<select class="form-control select-code mb-3" name="first_val" id="first_val">
										<option value=""></option>
									</select>
								</div>
							</div> --}}

							{{-- <div class="form-group row">
								<label for="second" class="col-sm-3 control-label mt-5">2nd Code:</label>
								<div class="col-sm-6">
									<select class="form-control select-code mb-3" name="second" id="second">
										<option value=""></option>
									</select>
								</div>
								<div class="col-sm-3">
									<select class="form-control select-code mb-3" name="second_val" id="second_val">
										<option value=""></option>
									</select>
								</div>
							</div> --}}

							{{-- <div class="form-group row" id="hide_3rd">
								<label for="third" class="col-sm-3 control-label mt-5">3rd Code:</label>
								<div class="col-sm-6">
									<select class="form-control select-code mb-3" name="third" id="third">
										<option value=""></option>
									</select>
								</div>
								<div class="col-sm-3">
									<select class="form-control select-code mb-3" name="third_val" id="third_val">
										<option value=""></option>
									</select>
								</div>
							</div> --}}

							{{-- <div class="form-group row" id="hide_4th">
								<label for="forth" class="col-sm-3 control-label mt-5">4th Code:</label>
								<div class="col-sm-6">
									<select class="form-control select-code mb-3 alloy" name="forth" id="forth">
										<option value=""></option>
									</select>
								</div>
								<div class="col-sm-3">
									<select class="form-control select-code mb-3 alloy" name="forth_val" id="forth_val">
										<option value=""></option>
									</select>
								</div>
							</div> --}}

							{{-- <div class="form-group row" id="hide_5th">
								<label for="fifth" class="col-sm-3 control-label mt-5">5th Code:</label>
								<div class="col-sm-6">
									<select class="form-control select-code mb-3" name="fifth" id="fifth">
										<option value=""></option>
									</select>
								</div>
								<div class="col-sm-3">
									<select class="form-control select-code mb-3" name="fifth_val" id="fifth_val">
										<option value=""></option>
									</select>
								</div>
							</div> --}}

							{{-- <div class="form-group row">
								<label for="seventh" class="col-sm-3 control-label mt-5">7th Code:</label>
								<div class="col-sm-6">
									<select class="form-control select-code mb-3 item" name="seventh" id="seventh">
										<option value=""></option>
									</select>
								</div>
								<div class="col-sm-3">
									<select class="form-control select-code mb-3 item" name="seventh_val" id="seventh_val">
										<option value=""></option>
									</select>
								</div>
							</div> --}}

							{{-- <div class="form-group row" id="hide_8th">
								<label for="eighth" class="col-sm-3 control-label mt-5">8th Code:</label>
								<div class="col-sm-6">
									<select class="form-control select-code mb-3" name="eighth" id="eighth">
										<option value=""></option>
									</select>
								</div>
								<div class="col-sm-3">
									<select class="form-control select-code mb-3" name="eighth_val" id="eighth_val">
										<option value=""></option>
									</select>
								</div>
							</div> --}}

							<div class="row" id="hide_9th">
								<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 mb-5">
									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">9th Code:</span>
										</div>
										<select class="form-control select-code" name="ninth" id="ninth">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<select class="form-control select-code" name="ninth_val" id="ninth_val">
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
										<select class="form-control select-code size" name="eleventh" id="eleventh">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<select class="form-control select-code size" name="eleventh_val" id="eleventh_val">
										<option value=""></option>
									</select>
								</div>
							</div>

							{{-- <div class="form-group row" id="hide_9th">
								<label for="ninth" class="col-sm-3 control-label mt-5">9th Code:</label>
								<div class="col-sm-6">
									<select class="form-control select-code mb-3" name="ninth" id="ninth">
										<option value=""></option>
									</select>
								</div>
								<div class="col-sm-3">
									<select class="form-control select-code mb-3" name="ninth_val" id="ninth_val">
										<option value=""></option>
									</select>
								</div>
							</div> --}}

							{{-- <div class="form-group row">
								<label for="eleventh" class="col-sm-3 control-label mt-5">11th Code:</label>
								<div class="col-sm-6">
									<select class="form-control select-code mb-3 size" name="eleventh" id="eleventh">
										<option value=""></option>
									</select>
								</div>
								<div class="col-sm-3">
									<select class="form-control select-code mb-3 size" name="eleventh_val" id="eleventh_val">
										<option value=""></option>
									</select>
								</div>
							</div> --}}

							<div class="row" id="hide_14th">
								<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 mb-5">
									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">14th Code:</span>
										</div>
										<select class="form-control select-code" name="forteenth" id="forteenth">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<<select class="form-control select-code" name="forteenth_val" id="forteenth_val">
										<option value=""></option>
									</select>
								</div>
							</div>

							{{-- <div class="form-group row mb-10" id="hide_14th">
								<label for="forteenth" class="col-sm-3 control-label mt-5">14th Code:</label>
								<div class="col-sm-6">
									<select class="form-control select-code mb-3" name="forteenth" id="forteenth">
										<option value=""></option>
									</select>
								</div>
								<div class="col-sm-3">
									<select class="form-control select-code mb-3" name="forteenth_val" id="forteenth_val">
										<option value=""></option>
									</select>
								</div>
							</div> --}}

							<div class="row justify-content-center">
								<div class="col-md-3 mb-5">
									<button type="submit" id="btn_save" class="btn bg-green btn-blockv permission-button">
										<i class="fa fa-floppy-o"></i> Save
									</button>
								</div>
								<div class="col-md-3 mb-5">
									<button type="button" id="btn_cancel" class="btn bg-red btn-block">
										<i class="fa fa-times"></i> Cancel
									</button>
								</div>
							</div>
						</form>
					</div>

					<div class="col-md-8">
						<div class="table-reponsive">
							<table class="table table-striped table-sm mb-10 dt-responsive nowrap" id="tbl_material_code" style="width:100%">
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
										<th>Date Created</th>
									</tr>
								</thead>
								<tbody id="tbl_material_code_body"></tbody>
							</table>
						</div>

						<div class="row justify-content-center">
							<div class="col-md-3 mb-5">
								<button type="button" id="btn_delete_material" class="btn bg-red btn-blockv permission-button">
									<i class="fa fa-trash"></i> Delete Material
								</button>
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
	var token = $('meta[name="csrf-token"]').attr('content');
	var assemblyListURL = "{{ url('/masters/material-master/material-list') }}";
	var assemblyDeleteURL = "{{ url('/masters/material-master/destroy') }}";
	var materialTypeURL = "{{ url('/masters/material-master/material-type') }}";
	var matCodeListURL = "{{ url('/masters/material-master/get-mat-code-list') }}";
	var showDropdownURL = "{{ url('/masters/material-master/show-dropdowns') }}";
	var showCodeURL = "{{ url('/masters/material-master/show-code') }}";
	var materialCodeDeleteURL = "{{ url('/masters/material-master/destroy-code') }}";
	var getMaterialTypeURL = "{{ url('/masters/material-master/get_dropdown_material_type') }}";
	var code_permission = "M0004";
</script>
<script type="text/javascript" src="{{ mix('/js/pages/ppc/masters/material-master/material-master.js') }}"></script>
@endpush
