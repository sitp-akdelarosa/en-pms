@extends('layouts.app')

@section('content')
 <?php
 $exist = 0;
foreach ($user_accesses as $user_access){
        if($user_access->code == "T0008" ){
            $exist++;
        }
}
    if($exist == 0){
        echo  '<script>window.history.back()</script>';
        exit;
    }
?>
<section class="content-header">
    <h1>Transfer Item</h1>
</section>

<section class="content">
	<div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li><a class="active" href="#transfer_entry" data-toggle="tab">Transfer Entry</a></li>
            <li><a href="#received_items" data-toggle="tab">Receive Items</a></li>
        </ul>
        <div class="tab-content">

            <div class="tab-pane active" id="transfer_entry">

                <div class="row justify-content-center mb-10">
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sm btn-block bg-green"  id="btn_add">
                            <i class="fa fa-plus"></i> Add
                        </button>
                    </div>
                    <div class="col-md-2">
                         <button type="submit" class="btn btn-sm btn-block bg-red permission-button"  id="btn_delete_set">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </div>
                </div>

                <table class="table table-sm table-hover table-striped dt-responsive nowrap" id="tbl_transfer_entry" style="width: 100%">
                    <thead class="thead-dark">
                        <tr>
                            <th width="5%">
                                <input type="checkbox" class="table-checkbox check_all_transfer_item">
                            </th>
                            <th>Job Order No.</th>
                            <th>Product Code</th>
                            <th>Process</th>
                            <th>Transfer To</th>
                            <th>To Process</th>
                            <th>Qty.</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            <th>Date</th>
                            <th>Item Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="tbl_transfer_entry_body"></tbody>
                </table>

                
            </div>


            <div class="tab-pane" id="received_items">
                <table class="table table-sm table-hover table-striped dt-responsive nowrap" id="tbl_received_items" style="width: 100%">
                    <thead class="thead-dark">
                        <tr>
                             <th width="5%">
                                <input type="checkbox" class="table-checkbox check_all_receive_item">
                            </th>
                            <th>Job Order No.</th>
                            <th>Product Code</th>
                            <th>From Division Code</th>
                            <th>From process</th>
                            <th>To Division Code</th>
                            <th>To Process</th>
                            <th>Qty.</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="tbl_received_items_body"></tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@include('includes.modals.production.transfer-items-modal')
@endsection

@push('scripts')
    <script type="text/javascript">
        var token = $('meta[name="csrf-token"]').attr('content');
        var code_permission = 'T0008';
        var userDivCode = "{{ Auth::user()->div_code }}";
        var getJOdetailsURL = "{{ url('/prod/transfer-item/get-jo') }}";
        var getTransferEntryURL = "{{ url('/prod/transfer-item/get-transfer-entry') }}";

        var transfer_entry = "{{ url('/prod/transfer-item/get-output') }}";
        var getReceiveItemsURL = "{{ url('/prod/transfer-item/received_items') }}";
        var deleteTransferItem = "{{ url('/prod/transfer-item/destroy') }}";

        var getDivisionCode = "{{ url('/prod/transfer-item/getDivisionCode') }}";
        var getDivCodeProcessURL = "{{ url('/prod/transfer-item/div-code-process') }}";
        var unprocessedItem = "{{ url('/prod/transfer-item/get-unprocessed') }}";


    </script>
    <script type="text/javascript" src="{{ asset('/js/pages/production/transactions/transfer-items/transfer-items.js') }}"></script>
@endpush
