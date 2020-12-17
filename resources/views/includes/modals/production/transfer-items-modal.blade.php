<div id="modal_transfer_entry" class="modal fade " data-backdrop="static">
    <div class="modal-dialog " role="document">
        <form id="frm_transfer_items" role="form" method="POST"  action="{{ url('/prod/transfer-item/save') }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Transfer Entry</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body table-responsive">
                    <div class="loadingOverlay-modal"></div>

                    <h5>FROM</h5>
                    <hr/>
                    <div class="row mb-10">
                        <div class="col-md-7">
							@csrf
                            <input type="hidden" id="id" name="id" class="clear">
                            <input type="hidden" id="userDivCode" name="userDivCode" class="clear">
                            <input type="hidden" id="UnprocessTransfer" name="UnprocessTransfer" class="clear" value='0'>

                            
				            <div class="form-group row">
			                    <div class="col-sm-10">
			                        <div class="input-group input-group-sm">
			                            <div class="input-group-prepend">
			                                <span class="input-group-text">J.O. #:</span>
			                            </div>
			                            <input type="text" class="form-control validate clear" name="jo_no" id="jo_no">
			                            <span id="jo_no_feedback"></span>
			                        </div>
			                    </div>
			                </div>

			                <div class="form-group row">
			                    <div class="col-sm-10">
			                        <div class="input-group input-group-sm">
			                            <div class="input-group-prepend">
			                                <span class="input-group-text">Production Order #:</span>
			                            </div>
			                            <input type="text" class="form-control validate clear" name="prod_order_no" id="prod_order_no" readonly>
			                            <span id="prod_order_no_feedback"></span>
			                        </div>
			                    </div>
			                </div>

			                <div class="form-group row">
			                    <div class="col-sm-12">
			                        <div class="input-group input-group-sm">
			                            <div class="input-group-prepend">
			                                <span class="input-group-text">Product Code:</span>
			                            </div>
			                            <input type="text" class="form-control validate clear" name="prod_code" id="prod_code" readonly>
			                            <span id="prod_code_feedback"></span>
			                        </div>
			                    </div>
			                </div>

			                <div class="form-group row">
			                    <div class="col-sm-12">
			                        <div class="input-group input-group-sm">
			                            <div class="input-group-prepend">
			                                <span class="input-group-text">Description:</span>
			                            </div>
			                            <input type="text" class="form-control validate clear" name="description" id="description" readonly>
			                            <span id="description_feedback"></span>
			                        </div>
			                    </div>
			                </div>

			                <div class="form-group row">
			                    <div class="col-sm-12">
			                        <div class="input-group input-group-sm">
			                            <div class="input-group-prepend">
			                                <span class="input-group-text">Current Process:</span>
			                            </div>
			                            <select class="form-control select-validate clear" name="curr_process" id="curr_process">
			                            	<option value=""></option>
			                            </select>
			                            <span id="curr_process_feedback"></span>
			                        </div>
			                    </div>
			                </div>
			            	<div class="form-group row">
			                    <div class="col-sm-12">
			                        <div class="input-group input-group-sm">
			                            <div class="input-group-prepend">
			                                <span class="input-group-text"># of Item to transfer:</span>
			                            </div>
			                            <input type="text" class="form-control select-validate clear" name="unprocessed" id="unprocessed" value='0' readonly>
			                            <span id="curr_process_feedback"></span>
			                        </div>
			                    </div>
			                </div>
			            </div>

			            <div class="col-md-5">
							<div id="hide_create">
	                            <div class="form-group row">
	                                <div class="col-sm-12">
	                                    <div class="input-group input-group-sm">
	                                        <div class="input-group-prepend">
	                                            <span class="input-group-text">Created By:</span>
	                                        </div>
	                                        <input type="text" class="form-control" name="create_user" id="create_user" value="{{Auth::user()->user_id}}" readonly>
	                                    </div>
	                                </div>
	                            </div>

	                            <div class="form-group row">
	                                <div class="col-sm-12">
	                                    <div class="input-group input-group-sm">
	                                        <div class="input-group-prepend">
	                                            <span class="input-group-text">Created Date:</span>
	                                        </div>
	                                        <input type="text" class="form-control" name="created_date" id="created_date" value="{{date('m/d/Y')}}" readonly>
	                                    </div>
	                                </div>
	                            </div>
							</div>
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Updated By:</span>
                                        </div>
                                        <input type="text" class="form-control" name="update_user" id="update_user" value="{{Auth::user()->user_id}}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Updated Date:</span>
                                        </div>
                                        <input type="text" class="form-control" name="updated_date" id="updated_date" value="{{date('m/d/Y')}}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5>TO</h5>
                    <hr/>

                    <div class="row">
                    	<div class="col-md-6">
                    		<div class="form-group row">
			                    <div class="col-sm-9">
									<div class="input-group input-group-sm">
			                            <div class="input-group-prepend">
			                                <span class="input-group-text">Process:</span>
			                            </div>
			                            <select class="form-control select-validate clear" name="process" id="process" >
			                            	<option value=""></option>
			                            </select>
			                            <span id="process_feedback"></span>
			                        </div>
			                    </div>
			                </div>

                    		<div class="form-group row">
			                    <div class="col-sm-12">
			                   		<div class="input-group input-group-sm">
			                            <div class="input-group-prepend">
			                                <span class="input-group-text">Division Code:</span>
			                            </div>
			                            <select class="form-control select-validate clear" name="div_code" id="div_code">
			                            	<option value=""></option>
			                            </select>
			                            <span id="div_code_feedback"></span>
			                        </div>
			                    </div>
			                </div>

			                <div class="form-group row">
			                    <div class="col-sm-6">
			                        <div class="input-group input-group-sm">
			                            <div class="input-group-prepend">
			                                <span class="input-group-text">Qty:</span>
			                            </div>
			                            <input type="number" step='1' class="form-control validate clear" name="qty" id="qty">
			                            <span id="qty_feedback"></span>
			                        </div>
			                    </div>
			                </div>

			                <div class="form-group row">
			                    <div class="col-sm-10">
			                        <div class="input-group input-group-sm">
			                            <div class="input-group-prepend">
			                                <span class="input-group-text">Status:</span>
			                            </div>
			                            <select class="form-control select-validate clear" name="status" id="status">
	                            			<option style="display:none"></option>
	                            			<option value="GOOD">GOOD</option>
	                            			<option value="REWORK">REWORK</option>
	                            			<option value="SCRAP">SCRAP</option>
	                            			<option value="CONVERT">CONVERT</option>
			                            </select>			
			                            <span id="status_feedback"></span>
			                        </div>
			                    </div>
			                </div>

                    	</div>
                    	<div class="col-md-6">
                    		<div class="form-group row">
			                    <div class="col-sm-12">
			                        <textarea class="form-control clear input" name="remarks" id="remarks" style="resize: none" placeholder="Remarks"></textarea>
			                    </div>
				            </div>
                    	</div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-red " data-dismiss="modal">
                    	<i class="fa fa-times"></i> Close
                    </button>
                    <button type="submit" class="btn bg-blue float-right permission-button">
                    	<i class="fa fa-floppy-o"></i> Save
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

<div id="modal_receive_item" class="modal fade " data-backdrop="static">
    <div class="modal-dialog col-md-3" role="document">
        <form id="frm_receive_item" role="form" method="POST"  action="{{ url('/prod/transfer-item/receive-process') }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Receive Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body table-responsive">
                    <div class="loadingOverlay-modal"></div>

                    <div class="row mb-10">
                        <div class="col-md-12">
							@csrf
                            <input type="hidden" id="id_r" name="id" class="clear">
                            <input type="hidden" id="jo_no_r" name="jo_no" class="clear">
                            <input type="hidden" id="prod_code_r" name="prod_code" class="clear">
                            <input type="hidden" id="process_r" name="process" class="clear">
                            <input type="hidden" id="current_process_r" name="current_process" class="clear">
                            <input type="hidden" id="div_code_code_r" name="div_code_code" class="clear">
                            <input type="hidden" id="current_div_code_r" name="current_div_code" class="clear">
                            <input type="hidden" id="current_process_name_r" name="current_process_name" class="clear">
                            <input type="hidden" id="status_r" name="status" class="clear">
				            <div class="form-group row">
			                    <div class="col-sm-6">
			                        <div class="input-group input-group-sm">
			                            <div class="input-group-prepend">
			                                <span class="input-group-text">Qty:</span>
			                            </div>
			                            <input type="number" step='1' class="form-control validate clear" name="qty" id="qty_r">
			                            <span id="qty_r_feedback"></span>
			                        </div>
			                    </div>
			                </div>

			            	<div class="form-group row">
			                    <div class="col-md-12">
		                    		<div class="form-group row">
					                    <div class="col-sm-12">
					                        <textarea class="form-control clear input" name="remarks" id="remarks_r" style="resize: none" placeholder="Remarks"></textarea>
					                    </div>
						            </div>
		                    	</div>
			                </div>

			            </div>
                	</div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-red " data-dismiss="modal">
                    	<i class="fa fa-times"></i> Close
                    </button>
                    <button type="submit" class="btn bg-blue float-right permission-button">
                    	<i class="fa fa-floppy-o"></i> Save
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>