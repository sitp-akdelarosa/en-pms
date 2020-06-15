@extends('layouts.app')

@section('content')
 <?php
$exist = 0;
foreach ($user_accesses as $user_access){
        if($user_access->code == "A0004" ){
            $exist++;
        }
}
    if($exist == 0){
        echo  '<script>window.history.back()</script>';
        exit;
    }
?>
<section class="content-header">
    <h1>Audit Trail</h1>
</section>

<section class="content">
    <div class="box">
        <div class="box-body">
        	<table class="table table-sm table-hover table-striped dt-responsive nowrap" id="tbl_audit" style="width: 100%">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Module / Form</th>
                        <th>Action</th>
                        <th>User</th>
                        <th>Date / Time</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</section>
@endsection

@push('scripts')
    <script type="text/javascript">
        var token = $('meta[name="csrf-token"]').attr('content');
        var code_permission = "A0004";
    </script>
    <script type="text/javascript" src="{{ mix('/js/pages/admin/audit-trail/audit-trail.js') }}"></script>
@endpush

