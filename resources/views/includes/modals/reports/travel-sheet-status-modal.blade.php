<div id="modal_travel_sheet_status" class="modal fade " data-backdrop="static">
    <div class="modal-dialog modal-full" role="document">
        <form method="GET" action="" id="frm_search">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add to Stocks</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="loadingOverlay-modal"></div>

                    @csrf

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group row">
                                <label for="as_jo_sequence" class="col-sm-4 control-label mt-5">JO No.:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control validate srch-clear" name="as_jo_sequence" id="as_jo_sequence">
                                    <div id="as_jo_sequence_feedback"></div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="as_sc_no" class="col-sm-4 control-label mt-5">SC No.:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control validate srch-clear" name="as_sc_no" id="as_sc_no">
                                    <div id="as_sc_no_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="as_prod_code" class="col-sm-4 control-label mt-5">Product Code:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control validate srch-clear" name="as_prod_code" id="as_prod_code">
                                    <div id="as_prod_code_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="as_description" class="col-sm-4 control-label mt-5">Description:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control validate srch-clear" name="as_description" id="as_description">
                                    <div id="as_description_feedback"></div>
                                </div>
                            </div>
                            
                        </div>

                        <div class="col-md-4">
                            <div class="form-group row">
                                <label for="as_based_qty" class="col-sm-3 control-label mt-5">Based Qty:</label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control validate srch-clear" name="as_based_qty" id="as_based_qty" step="0.1">
                                    <div id="as_based_qty_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="as_prod_output_qty" class="col-sm-3 control-label mt-5">Prod. Output Qty:</label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control validate srch-clear" name="as_prod_output_qty" id="as_prod_output_qty" step="0.1">
                                    <div id="as_prod_output_qty_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="as_curr_process" class="col-sm-3 control-label mt-5">Current Process:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate srch-clear" name="as_curr_process" id="as_curr_process">
                                    <div id="as_curr_process_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="as_status" class="col-sm-3 control-label mt-5">Status:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate srch-clear" name="as_status" id="as_status">
                                    <div id="as_status_feedback"></div>
                                </div>
                            </div>
                             
                        </div>

                        <div class="col-md-4">
                            <div class="form-group row">
                                <label for="srch_material_heat_no" class="col-sm-3 control-label mt-5">Push to Stocks:</label>
                            </div>
                            <div class="form-group row">
                                <label for="as_fg_qty" class="col-sm-3 control-label mt-5">FG Qty:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate srch-clear" name="as_fg_qty" id="as_fg_qty">
                                    <div id="as_fg_qty_feedback"></div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="as_crude_qty" class="col-sm-3 control-label mt-5">CRUDE Qty:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate srch-clear" name="as_crude_qty" id="as_crude_qty">
                                    <div id="as_crude_qty_feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group row">
                                <label for="srch_material_heat_no" class="col-sm-3 control-label mt-5">Merge to Mother JO:</label>
                            </div>
                            <div class="form-group row">
                                <label for="as_jo_no" class="col-sm-3 control-label mt-5">To JO:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate srch-clear" name="as_jo_no" id="as_jo_no">
                                    <div id="as_jo_no_feedback"></div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="as_ch_qty" class="col-sm-3 control-label mt-5">Child Qty:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate srch-clear" name="as_ch_qty" id="as_ch_qty">
                                    <div id="as_ch_qty_feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-red" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-blue float-right permission-button">Save</button>
                </div>
                
            </div>
        </form>
    </div>
</div>