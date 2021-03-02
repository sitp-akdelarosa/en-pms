@extends('layouts.app')

@section('title')
	User Type
@endsection



@section('content')
 <?php
$exist = 0;
foreach ($user_accesses as $user_access){
		if($user_access->code == "A0003" ){
			$exist++;
		}
}
	if($exist == 0){
		echo  '<script>window.history.back()</script>';
		exit;
	}
?>
<section class="content-header">
	<h1>User Type</h1>
</section>

<section class="content">
	<div class="row justify-content-center">
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

			<div class="box">
				<div class="box-body">

					<form role="form" action="{{ route('admin.user-type.save') }}" method="post" id="frm_user_type">
						@csrf
						<input type="hidden" id="id" name="id" class="clear">

						<div class="input-group mb-3 input-group-sm">
							<div class="input-group-prepend">
								<span class="input-group-text">User Type:</span>
							</div>
							<input type="text" class="form-control form-control-sm clear validate" name="description" id="description">
							<div id="description_feedback"></div>
						</div>

						<div class="input-group mb-3 input-group-sm">
							<div class="input-group-prepend">
								<span class="input-group-text">Category:</span>
							</div>
							<select class="form-control form-control-sm clear select-validate" name="category" id="category">
									<option value=""></option>
								<option value="OFFICE">OFFICE</option>
								<option value="PRODUCTION">PRODUCTION</option>
								<option value="ALL">ALL</option>
							</select>
							<div class="input-group-append">
								<button type="button" class="btn btn-sm bg-purple btn-block" id="btn_modules">
									<i class="fa fa-laptop"></i> Select Pages
								</button>
							</div>
							<div id="category_feedback"></div>
						</div>

						<div class="row">
							<div class="col-md-12 text-center">
								<button type="button" class="btn btn-sm bg-blue permission-button" id="btn_save">
									<i class="fa fa-floppy-o"></i> Save
								</button>
								<button type="button" class="btn btn-sm bg-yellow" id="btn_cancel">
									<i class="fa fa-times"></i> Cancel
								</button>
								<button type="button" class="btn btn-sm bg-red permission-button" id="btn_delete">
									<i class="fa fa-trash"></i> Delete
								</button>
							</div>
						</div>

						<div class="row">
							<div class="col-12">
								<div class="table-responsive">
									<table class="table table-sm table-bordered table-striped" id="tbl_type" style="width:100%">
										<thead class="thead-dark">
											<tr>
												<th width="5%">
													<input type="checkbox" class="table-checkbox check_all">
												</th>
												<th width="5%"></th>
												<th width="35%">User Type</th>
												<th width="30%">Category</th>
												<th width="25%">Date Added</th>
											</tr>
										</thead>
										<tbody id="tbl_type_body"></tbody>
									</table>
								</div>
							</div>
						</div>

						
					</form>

				</div>
			</div>

		</div>

	</div>
</section>
@include('includes.modals.admin.user-type-modules-modal')
@endsection

@push('scripts')
	<script type="text/javascript">
		var typeDeleteURL = "{{ url('/admin/user-type/destroy') }}";
		var typeListURL = "{{ url('/admin/user-type/list') }}";
		var moduleListURL = "{{ url('/admin/user-type/module-list') }}";
		var saveUrl = "{{ route('admin.user-type.save') }}";
	</script>
	<script type="text/javascript" src="{{ mix('/js/pages/admin/user-type/user-type.js') }}"></script>
@endpush


