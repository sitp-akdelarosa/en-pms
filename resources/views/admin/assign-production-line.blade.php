@extends('layouts.app')

@section('content')
 <?php
$exist = 0;
foreach ($user_accesses as $user_access){
        if($user_access->code == "A0002" ){
            $exist++;
        }
}
    if($exist == 0){
        echo  '<script>window.history.back()</script>';
        exit;
    }
?>


<section class="content-header">
    <h1>Assign Product Line</h1>
</section>

<section class="content">
    <div class="row justify-content-center">
        <div class="col-lg-12">

                <div class="box">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <form id="frm_assign_productline" role="form" method="POST" action="{{ route('admin.assign-production-line.save') }}">
                                    @csrf
                                    <input type="hidden" name="id" id="id">

                                    <div class="row mb-5">
                                        <div class="col-md-5">
                                            <div class="form-group row">
                                                <label for="user_id" class="col-sm-3 control-label">User ID:</label>
                                                <div class="col-sm-9">
                                                    <select class="form-control form-control-sm select-validate" name="user_id[]" id="user_id" multiple style="height: 200px">
                                                        <option value=""></option>
                                                    </select>
                                                    <div id="user_id_feedback"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-7">
                                            
                                            <div class="form-group row mb-10">
                                                <label for="product_line" class="col-sm-3 control-label">Product Line:</label>
                                                <div class="col-sm-9">
                                                    <select class="form-control form-control-sm select-validate" name="product_line[]" id="product_line" multiple style="height: 200px">
                                                        <option value=""></option>
                                                    </select>
                                                    <div id="product_line_feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    

                                    

                                    <div class="form-group row justify-content-center">
                                        <div class="col-md-3 mb-5">
                                            <button type="submit" id="btn_save" class="btn bg-blue btn-block permission-button">
                                                <i class="fa fa-floppy-o"></i> Save
                                            </button>
                                        </div>
                                        <div class="col-md-3 mb-5">
                                            <button type="button" id="btn_clear" class="btn bg-grey btn-block">
                                                <i class="fa fa-refresh"></i> Clear
                                            </button>
                                        </div>
                                        <div class="col-md-3 mb-5">
                                            <button type="button" id="btn_delete" class="btn bg-red btn-block permission-button">
                                                <i class="fa fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </div>


                                </form>
                            </div>

                            <div class="col-md-6">
                                <table class="table table-striped table-sm dt-responsive" id="tbl_assign_productline" style="width:100%">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th width="5%">
                                                <input type="checkbox" class="table-checkbox check_all">
                                            </th>
                                            {{-- <th width="5%"></th> --}}
                                            {{-- <th>User</th> --}}
                                            <th>Product Line</th>
                                            <th>Assign Date</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbl_assign_productline_body"></tbody>
                                </table>
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
        var getUserURL = "{{ url('/admin/assign-production-line/users') }}";
        var prodLineListURL = "{{ url('/admin/assign-production-line/list') }}";
        var prodLineDeleteURL = "{{ url('/admin/assign-production-line/destroy') }}";
        var dropdownProduct = "{{ url('/admin/assign-production-line/dropdownProduct') }}";
        var code_permission = "A0002";

    </script>
    <script type="text/javascript" src="{{ asset('/js/pages/admin/assign-production-line/assign-production-line.js') }}"></script>
@endpush
