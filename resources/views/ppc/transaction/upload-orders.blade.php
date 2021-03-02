@extends('layouts.app')

@section('title')
	Upload Orders
@endsection

@section('content')
 <?php
 $exist = 0;
foreach ($user_accesses as $user_access){
        if($user_access->code == "T0002" ){
            $exist++;
        }
}
    if($exist == 0){
        echo  '<script>window.history.back()</script>';
        exit;
    }
?>
<section class="content-header">
    <h1>Upload Orders</h1>
</section>

<section class="content">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-sm-12 col-xs-12">

            <form id="frm_upload_order" role="form" method="post" files="true" enctype="multipart/form-data" action="{{ url('/transaction/upload-orders/upload-up') }}" >
                @csrf
                <div class="form-group row">
                    <div class="custom-file col-md-9 mb-3" id="customFile" lang="es">
                        <input type="file" class="custom-file-input" name="fileupload" id="fileupload" aria-describedby="fileHelp">
                        <label class="custom-file-label" for="exampleInputFile" id="filenamess">
                           Select file...
                        </label>
                    </div>

                    <div class="col-md-3 mb-3">
                        <button class="btn btn-block btn-lg bg-blue permission-button">
                            <i class="fa fa-upload"></i> Upload
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
            <div class="box">
                <div class="box-body">
                    
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped table-bordered nowrap mb-10" id="tbl_Upload" style="width: 100%">
                            <thead class="thead-dark">
                                <tr>
                                    {{-- <th>
                                        <input type="checkbox" class="table-checkbox check_all">
                                        ID
                                    </th> --}}
                                    <th>SC #</th>
                                    <th>Product Code</th>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>PO Number</th>
                                    <th>Uploader</th>
                                    <th>Date Uploaded</th>
                                </tr>
                            </thead>
                            <tbody id="tbl_Uploadbody">
                                {{-- <tr>
                                    <td colspan="6" class="text-center">
                                        No data uploaded.
                                    </td>
                                </tr> --}}
                            </tbody>
                        </table>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-md-3">
                            <button id="btn_check_unregistered" class="btn btn-lg btn-block bg-blue">
                                <i class="fa fa-check"></i> Check Unregistered Products
                            </button>
                        </div>
                        <div class="col-md-2">
                            <button id="btn_filter_search" class="btn btn-lg btn-block bg-teal">
                                <i class="fa fa-search"></i> Search / Filter
                            </button>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>


</section>
@include('includes.modals.transactions.upload-orders-modal')

@endsection

@push('scripts')
    <script type="text/javascript">
        var checkfile = "{{ url('/transaction/upload-orders/CheckFile') }}";
        var datatableUpload = "{{ url('/transaction/upload-orders/DatatableUpload') }}";
        var deleteselected = "{{ url('/transaction/upload-orders/deletefromtemp') }}";
        var overwriteURL = "{{ url('/transaction/upload-orders/overwrite') }}";
        var downloadNonexistingURL = "{{ url('/transaction/upload-orders/download-unregistered-products') }}";
        var getNonexistingURL = "{{ url('/transaction/upload-orders/get-unregistered-products') }}";
        var excelSearchFilterURL = "{{ url('/transaction/upload-orders/search-orders-excel') }}";
    </script>
    <script type="text/javascript" src="{{ mix('/js/pages/ppc/transactions/upload-orders/upload-orders.js') }}"></script>
@endpush
