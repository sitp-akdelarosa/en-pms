@extends('layouts.app')

@section('title')
	Dropdown Master
@endsection


@section('content')
 <?php
 $exist = 0;
foreach ($user_accesses as $user_access){
        if($user_access->code == "M0002" ){
            $exist++;
        }
}
    if($exist == 0){
        echo  '<script>window.history.back()</script>';
        exit;
    }
?>
<section class="content-header">
    <h1>Dropdown Master</h1>
</section>

<section class="content">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">

                <div class="box">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped dt-responsive nowrap" id="tbl_dropdown" style="width:100%">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th width="10%"></th>
                                                <th width="90%">Dropdown Name</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbl_dropdown_body"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

        </div>

    </div>
</section>
@include('includes.modals.masters.dropdown-master-modals')
@include('includes.modals.system-modals')

@endsection

@push('scripts')
    <script type="text/javascript">
        var dropdownListURL = "{{ url('masters/dropdown-master/dropdown-list') }}";
        var dropdownNamesURL = "{{ url('masters/dropdown-master/names') }}"
        var getDropdownItemURL = "{{ url('masters/dropdown-master/items') }}";


        var dropdownNamesDeleteURL = "{{ url('masters/dropdown-master/destroy/names') }}";
        var dropdownItemsDeleteURL = "{{ url('masters/dropdown-master/destroy/items') }}";
        var checkItemExistURL = "{{ url('masters/dropdown-master/check-items') }}";
        
    </script>
    <script type="text/javascript" src="{{ asset('/js/pages/ppc/masters/dropdown-master/dropdown-master.js') }}"></script>
@endpush