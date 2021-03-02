@extends('layouts.app')

@section('title')
	Assign Warehouse
@endsection


@section('content')
 <?php
$exist = 0;
foreach ($user_accesses as $user_access){
		if($user_access->code == "A0006" ){
			$exist++;
		}
}
	if($exist == 0){
		echo  '<script>window.history.back()</script>';
		exit;
	}
?>


<section class="content-header">
	<h1>Assign Warehouse</h1>
</section>

<section class="content">
	<div class="row justify-content-center">
		<div class="col-lg-12">

				<div class="box">
					<div class="box-body">
						<div class="row mb-5">
							<div class="col-md-4">
								<h5>Choose User</h5>
								<table class="table table-sm table-hover table-bordered table-striped dt-responsive nowrap" id="tbl_users" style="width:100%">
									<thead class="thead-dark">
										<tr>
											<th width="5%">
												<input type="checkbox" class="table-checkbox check_all_users">
											</th>
											<th width="40%">User ID</th>
											<th width="50%">Name</th>
											<th width="5%"></th>
										</tr>
									</thead>
									<tbody id="tbl_users_body"></tbody>
								</table>
							</div>

							<div class="col-md-3">
								<h5>Assign user to Warehouse</h5>
								<table class="table table-striped table-sm table-bordered dt-responsive" id="tbl_warehouse" style="width:100%">
									<thead class="thead-dark">
										<tr>
											<th>
												<input type="checkbox" class="table-checkbox check_all_whs">
											</th>
											<th>Warehouse</th>
											<th>Type</th>
										</tr>
									</thead>
									<tbody id="tbl_warehouse_body"></tbody>
								</table>
							</div>

							<div class="col-md-5">
								<h5>Assiged Warehouse</h5>
								<table class="table table-striped table-sm table-bordered dt-responsive" id="tbl_assign_warehouse" style="width:100%">
									<thead class="thead-dark">
										<tr>
											<th>
												<input type="checkbox" class="table-checkbox check_all">
											</th>
											<th>Warehouse</th>
											<th>Assigned To</th>
											<th>Assigned Date</th>
										</tr>
									</thead>
									<tbody id="tbl_assign_warehouse_body"></tbody>
								</table>
							</div>
						</div>

						<div class="row justify-content-center">
							<div class="col-md-1 mb-5">
								<button type="button" id="btn_save" class="btn bg-blue btn-block permission-button btn-sm">
									<i class="fa fa-floppy-o"></i> Save
								</button>
							</div>
							<div class="col-md-1 mb-5">
								<button type="button" id="btn_clear" class="btn bg-grey btn-block btn-sm">
									<i class="fa fa-refresh"></i> Clear
								</button>
							</div>
							<div class="col-md-1 mb-5">
								<button type="button" id="btn_delete" class="btn bg-red btn-block permission-button btn-sm">
									<i class="fa fa-trash"></i> Delete
								</button>
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
		var getUserURL = "{{ url('/admin/assign-warehouse/users') }}";
		var WarehouseListURL = "{{ url('/admin/assign-warehouse/list') }}";
		var WarehouseDeleteURL = "{{ url('/admin/assign-warehouse/destroy') }}";
		var dropdownWarehouse = "{{ url('/admin/assign-warehouse/warehouse-select') }}";
		var SaveURL = "{{ route('admin.assign-warehouse.save') }}";
	</script>
	<script type="text/javascript" src="{{ asset('/js/pages/admin/assign-warehouse/assign-warehouse.js') }}"></script>
@endpush
