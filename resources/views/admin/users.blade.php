@extends('layouts.app')

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

                {{--<button type="button" class="btn btn-sm bg-blue permission-button" id="btn_upd_usertype">
                    <i class="fa fa-user"></i> Upd User Type
                </button>--}}
    		</div>
    	</div>

        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-striped table-sm dt-responsive nowrap" id="tbl_user" style="width:100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>User ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>User Type</th>
                            {{-- <th>Division Code</th> --}}
                            <th>Date Joined</th>
                            <th></th>
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
        var token = $('meta[name="csrf-token"]').attr('content');
        var userDeleteURL = "{{ url('/admin/user-master/destroy') }}";
        var userListURL = "{{ url('/admin/user-master/list') }}";
        var defaultPhoto = "{{ asset('images/default-profile.png') }}";
		var divCodeURL = "{{ url('admin/user-master/div-code') }}";
		var userModuleURL = "{{ url('admin/user-master/user-access') }}";
        var userUpdTypeURL = "{{ url('admin/user-master/update-usertype') }}";
        var code_module = "A0001";
    </script>
    <script type="text/javascript" src="{{ mix('/js/pages/admin/user-master/user-master.js') }}"></script>
@endpush
