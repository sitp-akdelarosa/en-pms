@extends('layouts.app')

@section('title')
	Users
@endsection


@push('styles')
@endpush

@section('content')
 <?php
$exist = 0;
foreach ($user_accesses as $user_access){
		if($user_access->code == "A0001" ){
			$exist++;
		}
}
	if($exist == 0){
		echo  '<script>window.history.back()</script>';
		exit;
	}
?>
<section class="content-header">
	<h1>Users</h1>
</section>

<section class="content">

	<div class="box">

		<div class="box-header with-border">
			<h3 class="box-title">
				User List
			</h3>
			<div class="box-tools pull-right">
				<button type="button" class="btn btn-sm bg-green permission-button" id="btn_add_user">
					<i class="fa fa-user-plus"></i> Add User
				</button>

				<button type="button" class="btn btn-sm bg-red permission-button" id="btn_delete">
					<i class="fa fa-trash"></i> Delete
				</button>
			</div>
		</div>

		<div class="box-body">
			<div class="table-responsive">
				<table class="table table-striped table-sm table-bordered dt-responsive nowrap" id="tbl_user" style="width:100%">
					<thead class="thead-dark">
						<tr>
							<th>
								<input type="checkbox" class="table-checkbox check_all_users" />
							</th>
							<th></th>
							<th>User ID</th>
							<th>First Name</th>
							<th>Nick Name</th>
							<th>Last Name</th>
							<th>Email</th>
							<th>Division</th>
							<th>User Type</th>
							<th>Password</th>
							<th>Date Joined</th>
						</tr>
					</thead>
					<tbody id="tbl_user_body"></tbody>
				</table>
			</div>
		</div>

	</div>
</section>
@include('includes.modals.admin.user-access-modal')
@endsection

@push('scripts')
	<script type="text/javascript">
		var userDeleteURL = "{{ url('/admin/user-master/destroy') }}";
		var userListURL = "{{ url('/admin/user-master/list') }}";
		var defaultPhoto = "{{ asset('images/default-profile.png') }}";
		var divCodeURL = "{{ url('admin/user-master/div-code') }}";
		var userModuleURL = "{{ url('admin/user-master/user-access') }}";
		var UserTypeURL = "{{ route('admin.users-type-users') }}";
		var DivCodeURL = "{{ route('admin.div-code-users') }}";
	</script>
	<script type="text/javascript" src="{{ mix('/js/pages/admin/user-master/user-master.js') }}"></script>
@endpush
