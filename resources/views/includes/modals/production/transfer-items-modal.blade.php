<div id="modal_transfer_entry" class="modal fade " data-backdrop="static">
    <div class="modal-dialog " role="document">
        <form id="frm_transfer_items" role="form" method="POST"  action="{{ url('/prod/transfer-item/save') }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Transfer Entry</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body table-responsive">
                    <div class="loadingOverlay-modal"></div>

                    <h6>FROM</h6>
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
										<input type="hidden" class="form-control clear" name="travel_sheet_id" id="travel_sheet_id">
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
			                                <span class="input-group-text">Output Status:</span>
			                            </div>
			                            <select class="form-control select-validate clear" name="ostatus" id="ostatus" title="Where will you get the quantity to transfer?">
	                            			<option style="display:none"></option>
	                            			<option value="good">GOOD</option>
	                            			<option value="rework">REWORK</option>
	                            			<option value="scrap">SCRAP</option>
											<option value="convert">CONVERT</option>
											<option value="alloy_mix">ALLOY MIX</option>
											<option value="nc">N/C</option>
			                            </select>			
			                            <span id="status_feedback"></span>
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
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Transfer Date:</span>
                                        </div>
                                        <input type="date" class="form-control" name="transfer_date" id="transfer_date">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Transfer Time:</span>
                                        </div>
                                        <input type="time" class="form-control" name="transfer_time" id="transfer_time">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h6>TO</h6>
                    <hr/>

                    <div class="row">
                    	<div class="col-md-6">
                    		<div class="form-group row">
			                    <div class="col-sm-9">
									<div class="input-group input-group-sm">
			                            <div class="input-group-prepend">
			                                <span class="input-group-text">Process:</span>
			                            </div>
			                            <select class="form-control select-validate clear" name="process" id="process" data-div_code="">
			                            	<option value=""></option>
										</select>
										<input type="hidden" class="form-control validate clear" name="process_id" id="process_id">
			                            <span id="process_feedback"></span>
			                        </div>
			                    </div>
								<div class="col-sm-2">
									<button type="button" class="btn btn-sm btn-block bg-green" id="btn_process">
										<i class="fa fa-plus"></i>
									</button>
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
                            <input type="hidden" id="user_div_code_r" name="user_div_code" class="clear">
                            <input type="hidden" id="current_div_code_r" name="current_div_code" class="clear">
                            <input type="hidden" id="current_process_name_r" name="current_process_name" class="clear">
							<input type="hidden" id="status_r" name="status" class="clear">
							<input type="hidden" id="ostatus_r" name="ostatus" class="clear">
							<input type="hidden" id="process_id_r" name="process_id" class="clear">
							
							<div class="form-group row">
								<div class="col-sm-1">NOTE:</div>
								<div class="col-sm-12">
									<textarea class="form-control clear input" id="note" style="resize: none" placeholder="Note" disabled></textarea>
								</div>
							</div>

							<div class="form-group row">
			                    <div class="col-sm-10">
			                        <div class="input-group input-group-sm">
			                            <div class="input-group-prepend">
			                                <span class="input-group-text">Transaferred Qty:</span>
			                            </div>
			                            <input type="number" step='1' class="form-control validate clear" name="transferred_qty_r" id="transferred_qty_r" readonly>
			                            <span id="transferred_qty_r_feedback"></span>
			                        </div>
			                    </div>
			                </div>

							<div class="form-group row">
			                    <div class="col-sm-10">
			                        <div class="input-group input-group-sm">
			                            <div class="input-group-prepend">
			                                <span class="input-group-text">Received Qty:</span>
			                            </div>
			                            <input type="number" step='1' class="form-control validate clear" name="receive_qty" id="receive_qty" readonly>
			                            <span id="receive_qty_feedback"></span>
			                        </div>
			                    </div>
							</div>
							
							<div class="form-group row">
			                    <div class="col-sm-10">
			                        <div class="input-group input-group-sm">
			                            <div class="input-group-prepend">
			                                <span class="input-group-text">Remaining Qty:</span>
			                            </div>
			                            <input type="number" step='1' class="form-control validate clear" name="remaining_qty" id="remaining_qty" readonly>
			                            <span id="remaining_qty_feedback"></span>
			                        </div>
			                    </div>
							</div>
							
							
							
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

<div id="modal_process" class="modal fade " data-backdrop="static">
    <div class="modal-dialog modal-sm" role="document">
        <form>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Extra Processes</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="form-group row">
						<label for="pDivision" class="col-sm-3 control-label mt-5">Division:</label>
                        <div class="col-sm-9">
                            <select class="form-control form-control-sm select-validate clear" id="pDivision" name="division" style="width:100%">
                                <option value=""></option>
                            </select>
                            <div id="pDivision_feedback"></div>
                        </div>
                    </div>
					<div class="form-group row">
						<label for="pProcess" class="col-sm-3 control-label mt-5">Process:</label>
                        <div class="col-sm-7">
                            <select class="form-control form-control-sm select-validate clear" id="pProcess" name="process" style="width:100%">
                                <option value=""></option>
                            </select>
                            <div id="pProcess_feedback"></div>
                        </div>

                        <div class="col-sm-2">
                            <button type="button" class="btn btn-sm btn-block bg-green" id="btn_add_process">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-sm table-striped table-bordered dt-responsive nowrap" id="tbl_process" style="width:100%">
                                <thead class="thead-dark">
                                    <th>#</th>
                                    <th>Process</th>
                                    <th></th>
                                </thead>
                                <tbody id="tbl_process_body">
                                    <tr>
                                        <td colspan="3">No data displayed.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-red" data-dismiss="modal">Close</button>
                    <button type="button" class="btn bg-blue float-right" id="btnSaveNewProcess" >
                        <i class="fa fa-floppy-o"></i> Save
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="modal_moved_data" class="modal fade " data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <form>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">These Items were already processed and cannot be deleted.</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-sm table-striped table-bordered dt-responsive nowrap" id="tbl_moved_data" style="width:100%">
                                <thead class="thead-dark">
                                    <th>J.O. No.</th>
                                    <th>Product Code</th>
                                    <th>Description</th>
									<th>From Div Code</th>
									<th>From Process</th>
									<th>To Div Code</th>
									<th>To Process</th>
									<th>Receive Qty</th>
                                </thead>
                                <tbody id="tbl_process_body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-red" data-dismiss="modal">Close</button>
                    <button type="button" class="btn bg-blue float-right" id="btnSaveNewProcess" >
                        <i class="fa fa-floppy-o"></i> Save
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>