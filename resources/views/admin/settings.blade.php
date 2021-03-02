@extends('layouts.app')

@section('title')
	Settings
@endsection


@push('styles')
	<style>
	</style>
@endpush

@section('content')
 <?php
$exist = 0;
foreach ($user_accesses as $user_access){
		if($user_access->code == "A0005" ){
			$exist++;
		}
}
	if($exist == 0){
		echo  '<script>window.history.back()</script>';
		exit;
	}
?>
<section class="content-header">
	<h1>Settings</h1>
</section>

<section class="content">
	<div class="row justify-content-center">
		<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">

			<div class="box">
				<div class="box-body">

					<div class="row">

						<div class="col-md-6">
							<form role="form" method="POST" enctype="multipart/form-data" action="{{ url('/admin/settings/save') }}" id="frm_iso">
								@csrf
								<input type="hidden" id="id" name="id" class="clear">

								<div class="form-group row justify-content-center">
									<div class="col-sm-5">
										<img src="{{ asset('images/default_upload_photo.jpg') }}" height="200px" width="200px" class="photo" id="whs_photo">
									</div>
								</div>

								<div class="form-group row justify-content-center mb-5">
									<div class="col-sm-5">
										<div class="custom-file mb-3" id="customFile" lang="es">
											<input type="file" class="custom-file-input validate clear" id="photo" name="photo" aria-describedby="fileHelp">
											<label class="custom-file-label" id="photo_label" for="photo">Select a photo...</label>
											<div id="photo_feedback"></div>
										</div>
									</div>
								</div>

								<div class="form-group row">
									<div class="col-sm-12">
										<div class="input-group input-group-sm">
											<div class="input-group-prepend">
												<span class="input-group-text">Name:</span>
											</div>
											<input type="text" class="form-control input-sm validate clear" name="iso_name" id="iso_name">
											<div id="iso_name_feedback"></div>
										</div>
									</div>
								</div>

								<div class="form-group row">
									<div class="col-sm-12">
										<div class="input-group input-group-sm">
											<div class="input-group-prepend">
												<span class="input-group-text">ISO Code:</span>
											</div>
											<input type="text" class="form-control input-sm validate clear" name="iso_code" id="iso_code">
											<div id="iso_code_feedback"></div>
										</div>
									</div>
								</div>

								<hr>

								<div class="form-group row">
									<div class="col-md-4">
										<button type="submit" class="btn btn-block bg-blue permission-button" id="btn_save">
											<i class="fa fa-floppy-o"></i> Save
										</button>
									</div>
									<div class="col-md-4">
										<button type="button" class="btn btn-block bg-grey permission-button" id="btn_clear">
											<i class="fa fa-refresh"></i> Clear
										</button>
									</div>
									<div class="col-md-4">
										<button type="button" class="btn btn-block bg-red permission-button" id="btn_delete">
											<i class="fa fa-trash"></i> Delete
										</button>
									</div>
								</div>
							</form>
						</div>

						<div class="col-md-6">
							<div class="table-responsive">
								<table class="table table-sm table-striped table-bordered dt-responsive nowrap" id="tbl_iso" style="width:100%">
									<thead class="thead-dark">
										<tr>
											<th width="5%">
												<input type="checkbox" class="table-checkbox check_all">
											</th>
											<th width="5%"></th>
											<th>Name</th>
											<th>ISO Code</th>
										</tr>
									</thead>
									<tbody id="tbl_iso_body"></tbody>
								</table>
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
		var getISOTable = "{{ url('/admin/settings/getISOTable') }}";
		var deleteISO = "{{ url('/admin/settings/destroy') }}";
		var defultphoto = "{{ asset('images/default_upload_photo.jpg') }}";
	</script>
	<script src="{{ mix('/js/pages/admin/settings/settings.js') }}"></script>
@endpush
