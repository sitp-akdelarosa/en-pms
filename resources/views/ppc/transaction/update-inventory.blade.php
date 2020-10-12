@extends('layouts.app')

@section('content')
 <?php
 $exist = 0;
foreach ($user_accesses as $user_access){
        if($user_access->code == "T0001" ){
            $exist++;
        }
}
    if($exist == 0){
        echo  '<script>window.history.back()</script>';
        exit;
    }
?>
<section class="content-header">
    <h1>Update Material Inventory</h1>
</section>

<section class="content">
    
	<div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-sm-12 col-xs-12">

            <form id="frm_update_inventory" role="form" method="post" files="true" enctype="multipart/form-data" action="">
                @csrf
                <div class="form-group row">
                    <div class="custom-file col-md-9 mb-3" id="customFile" lang="es">
                        <input type="file" class="custom-file-input" name="file_inventory" id="file_inventory" aria-describedby="fileHelp">
                        <label class="custom-file-label" for="file_inventory" id="file_inventory_label">
                           Select file...
                        </label>
                    </div>

                    <div class="col-md-2 mb-3">
                        <button class="btn btn-block bg-blue">
                            <i class="fa fa-upload"></i> Upload
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="box">
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped nowrap mb-10" id="tbl_materials" style="width: 100%">
                            <thead class="thead-dark">
                                <tr>
                                    <th></th>
                                    <th>Material Type</th>
                                    <th>Material Code</th>
                                    <th>Description</th>
                                    <th>Item</th>
                                    <th>Alloy</th>
                                    <th>Schedule</th>
                                    <th>Size</th>
                                    <th>Width</th>
                                    <th>Length</th>
                                    <th>Qty</th>
                                    <th>Current Stock</th>
                                    <th>UOM</th>
                                    <th>Heat No.</th>
                                    <th>Invoice No.</th>
                                    <th>Received Date</th>
                                    <th>Supplier</th>
                                    <th>Supplier Heat No.</th>
                                </tr>
                            </thead>
                            <tbody id="tbl_materials_body"></tbody>
                        </table>
                    </div>
                    
                    <div class="row justify-content-center mb-5">
                        <div class="col-md-2">
                            <button id="btn_zero" class="btn btn-sm btn-block bg-blue mb-3">Include 0 quantity</button>
                        </div>

                        <div class="col-md-2">
                            <button id="btn_add" class="btn btn-sm btn-block bg-green mb-3 permission-button">
                                <i class="fa fa-plus"></i> Add Materials
                            </button>
                        </div>

                        <div class="col-md-3">
                            <button id="btn_check_unregistered" class="btn btn-sm btn-block bg-purple mb-3 permission-button">
                                <i class="fa fa-check"></i> Check Unregistered Materials
                            </button>
                        </div>

                        <div class="col-md-2">
                            <button id="btn_download_format" class="btn btn-sm btn-block bg-navy mb-3">
                                <i class="fa fa-file-excel-o"></i> Download Format
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@include('includes.modals.transactions.update-inventory-modals')

@endsection
@push('scripts')
    <script type="text/javascript">
        var token = $('meta[name="csrf-token"]').attr('content');
        var checkfile = "{{ url('/transaction/update-inventory/CheckFileUpdateInventory') }}";
        var uploadInventory = "{{ url('/transaction/update-inventory/UploadInventory') }}";
        var materialDataTable = "{{ url('/transaction/update-inventory/materials') }}";
        var AddManual = "{{ url('/transaction/update-inventory/AddManual') }}";
        var materialTypeURL = "{{ url('/transaction/update-inventory/material-type') }}";
        var GetMaterialCode = "{{ url('/transaction/update-inventory/GetMaterialCode') }}";
        var GetMaterialCodeDetailsurl = "{{ url('/transaction/update-inventory/GetMaterialCodeDetails') }}";
        var code_permission = 'T0001';
        var downloadNonexistingURL = "{{ url('/transaction/update-inventory/download-unregistered-materials') }}";
        var getNonexistingURL = "{{ url('/transaction/update-inventory/get-unregistered-materials') }}";
        var downloadFormatURL = "{{ url('/transaction/update-inventory/download-update-inventory-format') }}";
    </script>
    <script type="text/javascript" src="{{ mix('/js/pages/ppc/transactions/update-inventory/update-inventory.js') }}"></script>

@endpush
