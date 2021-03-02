@extends('layouts.app')

@section('title')
	Product Withdrawal
@endsection

@section('content')
 <?php
 $exist = 0;
foreach ($user_accesses as $user_access){
        if($user_access->code == "T0009" ){
            $exist++;
        }
}
    if($exist == 0){
        echo  '<script>window.history.back()</script>';
        exit;
    }
?>
<section class="content-header">
    <h1>Product Withdrawal</h1>
</section>

<section class="content">
    <div class="box">
        <div class="box-body">
            <form id="frm_product" role="form" method="POST" action="{{ url('/transaction/product-withdrawal/save') }}">
                @csrf
                <input type="hidden" class="" name="id" id="id">
                <input type="hidden" class="clear" id="item_id">
                <input type="hidden" class="clear" id="item_count">
                <input type="hidden" class="clear input" id="inv_id">
                <input type="hidden" class="clear input" id="old_issued_qty">
                <input type="hidden" class="clear input" id="total_withdraw_qty">

                <div class="row mb-10">
                	<div class="col-md-6">
                		<div class="form-group row">
		                    <label for="trans_no" class="col-sm-2 control-label mt-5">Transaction No:</label>
		                    <div class="col-sm-8">
		                        <div class="input-group input-group-sm">
		                            <input type="text" class="form-control form-control-sm validate" name="trans_no" id="trans_no">
		                            <div id="trans_no_feedback"></div>
		                            <div class="input-group-append">
		                            	<button type="button" class="btn btn-sm bg-blue btn_navigation" id="btn_first" style="width: 30px; border-color: #6c757d">
		                            		<i class="fa fa-angle-double-left"></i>
		                            	</button>
		                            	<button type="button" class="btn btn-sm bg-blue btn_navigation" id="btn_prev" style="width: 30px; border-color: #6c757d">
		                            		<i class="fa fa-angle-left"></i>
		                            	</button>
		                            	<button type="button" class="btn btn-sm bg-blue btn_navigation" id="btn_next" style="width: 30px; border-color: #6c757d">
		                            		<i class="fa fa-angle-right"></i>
		                            	</button>
		                            	<button type="button" class="btn btn-sm bg-blue btn_navigation" id="btn_last" style="width: 30px; border-color: #6c757d">
		                            		<i class="fa fa-angle-double-right"></i>
		                            	</button>
		                            	
		                            </div>
		                        </div>
		                    </div>
		                </div>
                    </div>
                    
                    <div class="col-md-4 col-sm-12">
                		<div class="form-group row">
		                    <label for="status" class="col-md-2 col-sm-3 control-label mt-5">Status:</label>
		                    <div class="col-md-6 col-sm-9">
		                        <div class="input-group input-group-sm">
		                            <input type="text" class="form-control form-control-sm validate" name="status" id="status" readonly>
		                        </div>
		                    </div>
		                </div>
                	</div>
                </div>

                <div class="row mb-10">
                    
                	<div class="col-md-3">
                        <div class="form-group row">
                            <label for="item_class" class="col-sm-4 control-label mt-5">Item Class:</label>
                            <div class="col-sm-8">
                                <select class="form-control select-validate clear" id="item_class">
                                    <option value=""></option>
                                    <option value="CRUDE">CRUDE</option>
                                    <option value="FINISHED">FINISHED</option>
                                </select>
                                <div id="item_class_feedback"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="item_code" class="col-sm-4 control-label mt-5">Item Code</label>
                            <div class="col-sm-8">
                                <div class="input-group mb-3 input-group-sm">
                                    <input type="text" class="form-control form-control-sm validate clear input" id="item_code" maxlength="16">
                                    
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-sm bg-blue" id="btn_search_item_code">
		                            		<i class="fa fa-search"></i>
		                            	</button>
                                    </div>
                                    <div id="item_code_feedback"></div>
                                </div>                                
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="jo_no" class="col-sm-4 control-label mt-5">J.O. #:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm validate clear input" id="jo_no" readonly>
		                        <div id="jo_no_feedback"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="lot_no" class="col-sm-4 control-label mt-5">Lot #:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm validate clear input" id="lot_no" readonly>
		                        <div id="lot_no_feedback"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="heat_no" class="col-sm-4 control-label mt-5">Heat #:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm validate clear input" id="heat_no" readonly>
		                        <div id="heat_no_feedback"></div>
                            </div>
                        </div>

                        

                	</div>

                	<div class="col-md-3">
                        <div class="form-group row">
                            <label for="alloy" class="col-sm-3 control-label mt-5">Alloy:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control form-control-sm validate clear input" id="alloy" maxlength="20" readonly>
		                        <div id="alloy_feedback"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="item" class="col-sm-3 control-label mt-5">Item:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control form-control-sm validate clear input" id="item" maxlength="20" readonly>
		                        <div id="item_feedback"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="size" class="col-sm-3 control-label mt-5">Size:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control form-control-sm validate clear input" id="size" maxlength="10" readonly>
		                        <div id="size_feedback"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="schedule" class="col-sm-3 control-label mt-5">Schedule/Class:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control form-control-sm validate clear input" id="schedule" maxlength="15">
		                        <div id="schedule_feedback"></div>
                            </div>
                        </div>

			            <div class="form-group row">
		                    <div class="col-sm-12">
		                        <textarea class="form-control clear input" id="remarks" style="resize: none" placeholder="Remarks"></textarea>
		                    </div>
			            </div>

                	</div>

                	<div class="col-md-3">

                        <div class="form-group row">
                            <label for="sc_no" class="col-sm-3 control-label mt-5">SC #:</label>
                            <div class="col-sm-9">
                                <div class="input-group mb-3 input-group-sm">
                                    <input type="text" class="form-control validate clear " id="sc_no" >
                                    <div id="sc_no_feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="qty_weight" class="col-sm-3 control-label mt-5">Qty/Weight:</label>
                            <div class="col-sm-9">
                                <div class="input-group mb-3 input-group-sm">
                                    <input type="number" class="form-control validate clear " id="qty_weight" step=".01" readonly>
                                    <div class="input-group-append">
                                        <span class="input-group-text">KGS</span>
                                    </div>
                                    <div id="qty_weight_feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="qty_pcs" class="col-sm-3 control-label mt-5">Qty/Pcs:</label>
                            <div class="col-sm-9">
                                <div class="input-group mb-3 input-group-sm">
                                    <input type="number" class="form-control validate clear " id="inv_qty" readonly>
                                    <div class="input-group-append">
                                        <span class="input-group-text">PCS</span>
                                    </div>
                                    <div id="qty_pcs_feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="issued_qty" class="col-sm-3 control-label mt-5">Issued Qty:</label>
                            <div class="col-sm-9">
                                <div class="input-group mb-3 input-group-sm">
                                    <input type="number" step='1' class="form-control form-control-sm validate clear input" id="issued_qty" maxlength="5" readonly>
                                    <div class="input-group-append">
                                        <span class="input-group-text">PCS</span>
                                    </div>
                                    <div id="issued_qty_feedback"></div>
                                </div>
		                        
                            </div>
                        </div>

                	</div>

                	<div class="col-md-3">
                		<div class="form-group row">
                            <label for="create_user" class="col-sm-3 control-label mt-5">Created By:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control form-control-sm clear" id="create_user" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="created_at" class="col-sm-3 control-label mt-5">Create Date:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control form-control-sm clear" id="created_at" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="update_user" class="col-sm-3 control-label mt-5">Updated By:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control form-control-sm clear" id="update_user" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="updated_at" class="col-sm-3 control-label mt-5">Update Date:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control form-control-sm clear" id="updated_at" readonly>
                            </div>
                        </div>

                	</div>
                </div>
                
                <div class="form-group row justify-content-center" id="controls">
                    <div class="col-lg-1 col-md-1 sol-sm-2 mb-5">
                        <button type="button" id="btn_add" class="btn bg-green btn-block">
                            <i class="fa fa-plus"></i> Add
                        </button>
                    </div>
                    <div class="col-lg-1 col-md-1 sol-sm-2 mb-5">
                        <button type="button" id="btn_clear" class="btn bg-gray btn-block">
                            <i class="fa fa-refresh"></i> Clear
                        </button>
                    </div>
                </div>

	            <div class="row">
	                <div class="col-12">
	                    <table class="table table-sm table-hover table-striped table-bordered nowrap" id="tbl_product" style="width:100%">
	                        <thead class="thead-dark">
	                            <tr>
                                    <th width="3.14%"></th>
                                    <th width="3.14%"></th>
                                    <th width="7.14%">Item Class</th>
                                    <th width="11.14%">J.O. #</th>
                                    <th width="11.14%">Item Code</th>
                                    <th width="7.14%">Lot #</th>
                                    <th width="7.14%">Heat #</th>
                                    <th width="7.14%">SC #</th>
	                                <th width="7.14%">Alloy</th>
	                                <th width="7.14%">Item</th>
                                    <th width="7.14%">Size</th>
                                    <th width="7.14%">Class</th>
	                                <th width="7.14%">Issued Qty</th>
	                                <th width="7.14%">Remarks</th>
	                            </tr>
	                        </thead>
	                        <tbody id="tbl_product_body"></tbody>
	                    </table>
	                </div>
	            </div>

            	<div class="form-group row justify-content-center">
            		<div class="col-lg-2 col-md-1 col-sm-2 mb-5" id="add_new">
                        <button type="button" id="btn_new" class="btn bg-green btn-block permission-button">
                            <i class="fa fa-pencil"></i> Add New
                        </button>
                    </div>
                    <div class="col-lg-2 col-md-1 col-sm-2 mb-5" id="edit">
                        <button type="button" id="btn_edit" class="btn bg-purple btn-block permission-button">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                    </div>
                    <div class="col-lg-2 col-md-1 col-sm-2 mb-5" id="confirm">
                        <button type="button" id="btn_confirm" class="btn btn-success btn-block permission-button">
                            <i class="fa fa-check"></i> Confirm Withdrawal
                        </button>
                    </div>
                    <div class="col-lg-2 col-md-1 col-sm-2 mb-5" id="save">
                        <button type="submit" id="btn_save" class="btn bg-blue btn-block permission-button">
                            <i class="fa fa-floppy-o"></i> Save
                        </button>
                    </div>
                    <div class="col-lg-2 col-md-1 col-sm-2 mb-5" id="delete">
                        <button type="button" id="btn_delete" class="btn bg-red btn-block permission-button">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </div>
                    <div class="col-lg-2 col-md-1 col-sm-2 mb-5" id="cancel">
                        <button type="button" id="btn_cancel" class="btn bg-red btn-block">
                            <i class="fa fa-times"></i> Cancel
                        </button>
                    </div>
                    <div class="col-lg-2 col-md-1 col-sm-2 mb-5" id="print">
                        <button type="button" id="btn_prepare_print" class="btn bg-navy btn-block">
                            <i class="fa fa-print"></i> Print Slip
                        </button>
                    </div>
                    <div class="col-lg-2 col-md-1 col-sm-2 mb-5" id="search">
                        <button type="button" id="btn_search_filter" class="btn bg-teal btn-block">
                            <i class="fa fa-search"></i> Search / Filter
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</section>
@include('includes.modals.transactions.product-withdrawal-modals')

@endsection
@push('scripts')
    <script type="text/javascript">
        var getWithdrawalTransactionURL = "{{ url('/transaction/product-withdrawal/get-withdrawal-transaction') }}";
        var getInventoryURL = "{{ url('/transaction/product-withdrawal/get-product-inventory') }}";
        var ProductWithdrawalSlipPrintURL = "{{ url('/pdf/product-withdrawal-slip') }}";
        var excelSearchProductWithdrawalURL = "{{ url('/transaction/product-withdrawal/search-product-withdrawal-excel') }}";
        var deleteWithdrawalURL = "{{ url('/transaction/product-withdrawal/delete-product-withdrawal') }}";
        var confirmWithdrawalURL = "{{ url('/transaction/product-withdrawal/confirm-product-withdrawal') }}";
        var checkWithdrawalCancellationURL = "{{ url('/transaction/product-withdrawal/check-withdrawal-cancellation') }}";
        
    </script>
    <script type="text/javascript" src="{{ mix('/js/pages/ppc/transactions/product-withdrawal/product-withdrawal.js') }}"></script>
@endpush