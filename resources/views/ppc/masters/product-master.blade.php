@extends('layouts.app')

@section('title')
	Product Master
@endsection

@section('content')
 <?php
 $exist = 0;
foreach ($user_accesses as $user_access){
		if($user_access->code == "M0003" ){
			$exist++;
		}
}
	if($exist == 0){
		echo  '<script>window.history.back()</script>';
		exit;
	}
?>
<section class="content-header">
	<h1>Product Master</h1>
</section>

<section class="content">
	<div class="nav-tabs-custom">
		<ul class="nav nav-tabs">
			<li><a class="active" href="#product_code_assembly_tab" data-toggle="tab">Product Code Assembly</a></li>
			<li><a href="#product_code_tab" data-toggle="tab">Product Code (16 Code)</a></li>
		</ul>
		<div class="tab-content">

			<div class="tab-pane active" id="product_code_assembly_tab">

				<div class="row justify-content-center">
					<div class="col-md-8">

						<form method="post" action="{{ route('masters.product-master.assembly.save') }}" id="frm_code_assembly">
							@csrf
							<input type="hidden" name="assembly_id" class="clear" id="assembly_id">
							<div class="row mb-5">
								<div class="col-md-6">

									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">Product Line:</span>
										</div>
										<select class="form-control select-validate clear readonly_assembly switch" name="prod_type" id="prod_type"></select>
										<div id="prod_type_feedback"></div>
									</div>

									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">Character #:</span>
										</div>
										<input type="number" class="form-control validate clear readonly_assembly switch" name="character_num" id="character_num" min="1" max="16">
										<div id="character_num_feedback"></div>
									</div>
								</div>

								<div class="col-md-6">

									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">Character Code:</span>
										</div>
										<input type="text" class="form-control validate clear readonly_assembly switch" name="character_code" id="character_code">
										<div id="character_code_feedback"></div>
									</div>

									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">Description:</span>
										</div>
										<input type="text" class="form-control validate clear readonly_assembly switch" name="description" id="description">
										<div id="description_feedback"></div>
									</div>

								</div>
							</div>

							<div class="row justify-content-center">
								<div class="col-lg-2 col-md-2 col-sm-3 col-xs-3 mb-5" id="div_add">
									<button type="button" id="btn_add_assembly" class="btn bg-green btn-block permission-button switch" 
										title='This Button is to add new assembly data.'>
										<i class="fa fa-plus"></i> Add
									</button>
								</div>
								<div class="col-lg-2 col-md-2 col-sm-3 col-xs-3 mb-5" id="div_save">
									<button type="submit" id="btn_save_assembly" class="btn bg-blue btn-block permission-button switch" 
										title='This Button is to save this assembly data.'>
										<i class="fa fa-floppy-o"></i> Save
									</button>
								</div>
								<div class="col-lg-2 col-md-2 col-sm-3 col-xs-3 mb-5" id="div_clear">
									<button type="button" id="btn_clear_assembly" class="btn bg-gray btn-block switch" 
										title='This Button is to clear all input fields.'>
										<i class="fa fa-refresh"></i> Clear
									</button>
								</div>
								<div class="col-lg-2 col-md-2 col-sm-3 col-xs-3 mb-5" id="div_delete">
									<button type="button" id="btn_delete_assembly" class="btn bg-red btn-block permission-button switch" 
										title='This Button is to delete selected assemblies in the table.'>
										<i class="fa fa-trash"></i> Delete
									</button>
								</div>
								<div class="col-lg-2 col-md-2 col-sm-3 col-xs-3 mb-5" id="div_cancel">
									<button type="button" id="btn_cancel_assembly" class="btn bg-red btn-block switch" 
										title='This Button is to cancel editing this assembly data.'>
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
							<table class="table table-sm table-striped table-bordered nowrap" id="tbl_prodcode_assembly" style="width:100%">
								<thead class="thead-dark">
									<tr>
										<th width="5%">
											<input type="checkbox" class="table-checkbox check_all">
										</th>
										<th></th>
										<th>Product Line</th>
										<th>Character #</th>
										<th>Character Code</th>
										<th>Description</th>
										<th>Date Updated</th>
									</tr>
								</thead>
								<tbody id="tbl_prodcode_assembly_body"></tbody>
							</table>
						</div>

					</div>
				</div>
			</div>


			<div class="tab-pane" id="product_code_tab">

				<div class="row mb-5">
					<div class="loadingOverlay"></div>

					<div class="col-md-4">

						<div class="input-group mb-3 mt-5 input-group-sm">
							<div class="input-group-prepend">
								<span class="input-group-text">Product Line:</span>
							</div>
							<select class="form-control select-validatselect-codeselect-codee clear switch_code readonly_code" name="product-type" id="product-type"></select>
						</div>

						<form action="{{ url('/masters/product-master/code/product/save') }}" id="frm_prod_code">
							@csrf
							<input type="hidden" name="product_type" id="product_type" class="clear">
							<input type="hidden" name="product_id" id="product_id" class="clear">

							<div class="input-group mb-5 input-group-sm">
								<div class="input-group-prepend">
									<span class="input-group-text">Product Code:</span>
								</div>
								<input type="text" class="form-control validate clear switch_code readonly_code" name="product_code" id="product_code" maxlength="16">
								<div id="product_code_feedback"></div>
							</div>

							<div class="input-group mb-5 input-group-sm">
								<div class="input-group-prepend">
									<span class="input-group-text">Description:</span>
								</div>
								<input type="text" class="form-control validate clear switch_code readonly_code" name="code_description" id="code_description">
								<div id="code_description_feedback"></div>
							</div>

							<div class="row">
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
									<div class="input-group mb-5 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">Alloy:</span>
										</div>
										<input type="text" class="form-control clear switch_code readonly_code" name="alloy" id="alloy">
									</div>
								</div>

								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
									<div class="input-group mb-5 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">Item:</span>
										</div>
										<input type="text" class="form-control clear switch_code readonly_code" name="item" id="item">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
									<div class="input-group mb-5 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">Size:</span>
										</div>
										<input type="text" class="form-control clear switch_code readonly_code" name="size" id="size">
									</div>
								</div>

								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
									<div class="input-group mb-5 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">Class:</span>
										</div>
										<input type="text" class="form-control clear switch_code readonly_code" name="class" id="class">
									</div>
								</div>
							</div>

							<div class="input-group mb-5 input-group-sm">
								<div class="input-group-prepend">
									<span class="input-group-text">Standard Material Used:</span>
								</div>
								<input type="text" class="form-control clear switch_code readonly_code" name="standard_material_used" id="standard_material_used" >
							</div>

							<div class="input-group mb-5 input-group-sm">
								<div class="input-group-prepend">
									<span class="input-group-text">Crude / Finish Weight:</span>
								</div>
								<input type="number" class="form-control clear switch_code readonly_code" name="finish_weight" id="finish_weight" step=".01">
							</div>

							<div class="input-group mb-5 input-group-sm">
								<div class="input-group-prepend">
									<span class="input-group-text">Cut Weight:</span>
								</div>
								<input type="number" class="form-control validate size switch_code readonly_code " name="cut_weight" id="cut_weight" step="any">
								<div id="cut_weight_feedback"></div>
								<select class="form-control select-validate switch_code readonly_code" name="cut_weight_uom" id="cut_weight_uom">
									<option value="N/A">N/A</option>
									<option value="KGS">KGS</option>
									<option value="LBS">LBS</option>
								</select>
								<div id="cut_weight_uom_feedback"></div>
							</div>

							<div class="input-group mb-5 input-group-sm">
								<div class="input-group-prepend">
									<span class="input-group-text">Cut Length:</span>
								</div>
								<input type="number" class="form-control validate size switch_code readonly_code" name="cut_length" id="cut_length" step="any">
								<div id="cut_length_feedback"></div>
								<select class="form-control select-validate switch_code readonly_code" name="cut_length_uom" id="cut_length_uom">
									<option value="N/A">N/A</option>
									<option value="MM">MM</option>
									<option value="CM">CM</option>
									<option value="M">M</option>
								</select>
								<div id="cut_length_uom_feedback"></div>
							</div>

							<div class="input-group mb-5 input-group-sm">
								<div class="input-group-prepend">
									<span class="input-group-text">Cut Width:</span>
								</div>
								<input type="number" class="form-control validate size switch_code readonly_code" name="cut_width" id="cut_width" step="any">
								<div id="cut_width_feedback"></div>
								<select class="form-control select-validate switch_code readonly_code" name="cut_width_uom" id="cut_width_uom">
									<option value="N/A">N/A</option>
									<option value="MM">MM</option>
									<option value="CM">CM</option>
									<option value="M">M</option>
								</select>
								<div id="cut_width_uom_feedback"></div>
							</div>

							<div class="row">
								<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 mb-5">
									<div class="input-group mb-5 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">1st Code:</span>
										</div>
										<select class="form-control select-code switch_code readonly_code" name="first" id="first">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<select class="form-control select-code switch_code readonly_code" name="first_val" id="first_val">
										<option value=""></option>
									</select>
								</div>
							</div>

							<div class="row">
								<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 mb-5">
									<div class="input-group mb-5 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">2nd Code:</span>
										</div>
										<select class="form-control select-code switch_code readonly_code" name="second" id="second">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<select class="form-control select-code switch_code readonly_code" name="second_val" id="second_val">
										<option value=""></option>
									</select>
								</div>
							</div>

							<div class="row" id="hide_3rd">
								<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 mb-5">
									<div class="input-group mb-5 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">3rd Code:</span>
										</div>
										<select class="form-control select-code switch_code readonly_code" name="third" id="third">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<select class="form-control select-code switch_code readonly_code" name="third_val" id="third_val">
										<option value=""></option>
									</select>
								</div>
							</div>

							<div class="row" id="hide_4th">
								<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 mb-5">
									<div class="input-group mb-5 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">4th Code:</span>
										</div>
										<select class="form-control select-code switch_code readonly_code" name="forth" id="forth">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<select class="form-control select-code switch_code readonly_code" name="forth_val" id="forth_val">
										<option value=""></option>
									</select>
								</div>
							</div>

							<div class="row" id="hide_5th">
								<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 mb-5">
									<div class="input-group mb-5 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">5th Code:</span>
										</div>
										<select class="form-control select-code switch_code readonly_code" name="fifth" id="fifth">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<select class="form-control select-code switch_code readonly_code" name="fifth_val" id="fifth_val">
										<option value=""></option>
									</select>
								</div>
							</div>

							<div class="row" id="hide_7th">
								<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 mb-5">
									<div class="input-group mb-5 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">7th Code:</span>
										</div>
										<select class="form-control select-code switch_code readonly_code" name="seventh" id="seventh">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<select class="form-control select-code switch_code readonly_code" name="seventh_val" id="seventh_val">
										<option value=""></option>
									</select>
								</div>
							</div>

							<div class="row" id="hide_8th">
								<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 mb-5">
									<div class="input-group mb-5 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">8th Code:</span>
										</div>
										<select class="form-control select-code switch_code readonly_code" name="eighth" id="eighth">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<select class="form-control select-code switch_code readonly_code" name="eighth_val" id="eighth_val">
										<option value=""></option>
									</select>
								</div>
							</div>

							<div class="row" id="hide_9th">
								<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 mb-5">
									<div class="input-group mb-5 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">9th Code:</span>
										</div>
										<select class="form-control select-code switch_code readonly_code" name="ninth" id="ninth">
										<option value=""></option>
									</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<select class="form-control select-code switch_code readonly_code" name="ninth_val" id="ninth_val">
										<option value=""></option>
									</select>
								</div>
							</div>

							<div class="row" id="hide_11th">
								<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 mb-5">
									<div class="input-group mb-5 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">11th Code:</span>
										</div>
										<select class="form-control select-code switch_code readonly_code" name="eleventh" id="eleventh">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<select class="form-control select-code switch_code readonly_code" name="eleventh_val" id="eleventh_val">
										<option value=""></option>
									</select>
								</div>
							</div>

							<div class="row" id="hide_14th">
								<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 mb-5">
									<div class="input-group mb-5 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">14th Code:</span>
										</div>
										<select class="form-control select-code switch_code readonly_code" name="forteenth" id="forteenth">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 mb-5">
									<select class="form-control select-code switch_code readonly_code" name="forteenth_val" id="forteenth_val">
										<option value=""></option>
									</select>
								</div>
							</div>

							<div class="row justify-content-center">
								<div class="col-lg-3 col-md-4 mb-5" id="add_code">
									<button type="button" id="btn_add_code" class="btn bg-blue btn-block permission-button switch_code"
											title="This Button is to Add another Product Code.">
										<i class="fa fa-plus"></i> Add new
									</button>
								</div>
								<div class="col-lg-3 col-md-4 mb-5" id="save_code">
									<button type="submit" id="btn_save" class="btn bg-green btn-block permission-button switch_code"
											title="This Button is to Save Product Code Details.">
										<i class="fa fa-floppy-o"></i> Save
									</button>
								</div>
								<div class="col-lg-3 col-md-4 mb-5" id="clear_code">
									<button type="button" id="btn_clear_code" class="btn bg-gray btn-block switch_code"
											title="This Button is to Clear Product Codes details in input fields.">
										<i class="fa fa-refresh"></i> Clear
									</button>
								</div>
								<div class="col-lg-3 col-md-4 mb-5" id="cancel_code">
									<button type="button" id="btn_cancel" class="btn bg-red btn-block switch_code"
											title="This Button is to Cancel saving of edit / new Product Codes.">
										<i class="fa fa-times"></i> Cancel
									</button>
								</div>
							</div>
						</form>
					</div>

					<div class="col-md-8">
						<div class="table-reponsive">
							<table class="table table-striped table-sm mb-10 table-bordered display nowrap" id="tbl_product_code" style="width:100%">
								<thead class="thead-dark">
									<tr>
										<th>
											<input type="checkbox" class="table-checkbox check_all_product">
										</th>
										<th></th>
										<th>Product Line</th>
										<th>Product Code</th>
										<th>Description</th>
										<th>Date Updated</th>
										<th></th>
									</tr>
								</thead>
								<tbody id="tbl_product_code_body"></tbody>
							</table>
						</div>

						<div class="row justify-content-center">
							<div class="col-md-3 mb-5">
								<button type="button" id="btn_delete_product" class="btn bg-red btn-block permission-button"
										title="This Button is to delete selected Product Codes.">
									<i class="fa fa-trash"></i> Delete Product
								</button>
							</div>
							<div class="col-md-3 mb-5">
								<button type="button" id="btn_excel_product" class="btn bg-green btn-block"
										title="This Button is to initialize Excel download.">
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
@include('includes.modals.masters.product-master-modals')

@endsection
@push('scripts')
	<script type="text/javascript">
		var assemblyListURL = "{{ url('/masters/product-master/assembly/list') }}";
		var assemblyDeleteURL = "{{ url('/masters/product-master/assembly/destroy') }}";
		var prodTypeURL = "{{ url('/masters/product-master/code/product-type') }}";
		var showDropdownURL = "{{ url('/masters/product-master/code/show-dropdowns') }}";
		var divProcessURL = "{{ url('/masters/product-master/code/get-process-div') }}";
		var prodProcessListURL = "{{ url('/masters/product-master/code/get-prod-process-list') }}";
		var prodCodeListURL = "{{ url('/masters/product-master/code/get-prod-code-list') }}";
		var productDeleteURL = "{{ url('/masters/product-master/product/destroy') }}";
		var processDeleteURL = "{{ url('/masters/product-master/process/destroy') }}";
		var productTypeURL = "{{ url('/masters/product-master/code/product-type') }}";
		var getProductLineURL = "{{ url('/masters/product-master/get-product-line') }}";
		var getProcessURL = "{{ url('/masters/product-master/getProcessURL') }}";
		var getdropdownproduct = "{{ url('/masters/product-master/get_dropdown_product') }}";
		var getStandardMaterialURL = "{{ url('/masters/product-master/get-standard-material') }}";
		var downloadExcelFileURL = "{{ url('/masters/product-master/download-excel-file') }}";
		var AllProductLineURL = "{{ url('/masters/product-master/all-product-lines') }}";
		var disabledURL = "{{ url('/masters/product-master/enable-disabled-product') }}";

		var getSetURL = "{{ url('masters/process-master/get-set') }}";
	</script>
	<script type="text/javascript" src="{{ asset('/js/pages/ppc/masters/product-master/product-master.js') }}"></script>
@endpush
