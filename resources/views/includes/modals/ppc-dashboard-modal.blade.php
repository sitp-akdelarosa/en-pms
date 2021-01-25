<div id="modal_ppc_dashboard" class="modal fade " data-backdrop="static">
    <div class="modal-dialog modal-full" role="document">
        <form method="GET" action="{{ url('/dashboard/dashboard-search-filter') }}" id="frm_search">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Search / Filter Inventory</h5>
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
                                <label for="srch_date" class="col-sm-4 control-label mt-5">Date:</label>
                                <div class="col-sm-8">
                                    <div class="input-group input-group-sm">
                                        <input type="date" class="form-control validate srch-clear" name="srch_date_from" id="srch_date_from">
                                        
                                        <div class="input-group-append">
                                            <span class="input-group-text">-</span>
                                        </div>
                                        <input type="date" class="form-control validate srch-clear" name="srch_date_to" id="srch_date_to">
                                        
                                    </div>
                                    <div id="srch_date_from_feedback"></div>
                                    <div id="srch_date_to_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_jo_sequence" class="col-sm-4 control-label mt-5">J.O. No.:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control validate srch-clear" name="srch_jo_sequence" id="srch_jo_sequence">
                                    <div id="srch_jo_sequence_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_prod_code" class="col-sm-4 control-label mt-5">Item Code:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control validate srch-clear" name="srch_prod_code" id="srch_prod_code">
                                    <div id="srch_prod_code_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_description" class="col-sm-4 control-label mt-5">Description:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control validate srch-clear" name="srch_description" id="srch_description">
                                    <div id="srch_description_feedback"></div>
                                </div>
                            </div>
                            
                        </div>

                        <div class="col-md-4">
                            <div class="form-group row">
                                <label for="srch_div_code" class="col-sm-3 control-label mt-5">Division:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate srch-clear" name="srch_div_code" id="srch_div_code">
                                    <div id="srch_div_code_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_plant" class="col-sm-3 control-label mt-5">Plant:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate srch-clear" name="srch_plant" id="srch_plant">
                                    <div id="srch_plant_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_process" class="col-sm-3 control-label mt-5">Process:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate srch-clear" name="srch_process" id="srch_process">
                                    <div id="srch_process_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_material_used" class="col-sm-3 control-label mt-5">Material:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate srch-clear" name="srch_material_used" id="srch_material_used">
                                    <div id="srch_material_used_feedback"></div>
                                </div>
                            </div>
                             
                        </div>

                        <div class="col-md-4">
                            <div class="form-group row">
                                <label for="srch_material_heat_no" class="col-sm-3 control-label mt-5">Heat No.:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate srch-clear" name="srch_material_heat_no" id="srch_material_heat_no">
                                    <div id="srch_material_heat_no_feedback"></div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="srch_lot_no" class="col-sm-3 control-label mt-5">Lot No.:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate srch-clear" name="srch_lot_no" id="srch_lot_no">
                                    <div id="srch_lot_no_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_status" class="col-sm-3 control-label mt-5">Status:</label>
                                <div class="col-sm-9">
                                    <select class="form-control validate srch-clear" name="srch_status[]" id="srch_status" multiple="multiple">
                                        <option value="0">WAITING</option>
                                        <option value="1">DONE PROCESS</option>
                                        <option value="2">ON-GOING</option>
                                        <option value="3">CANCELLED</option>
                                        <option value="5">ALL PROCESS DONE</option>
                                        <option value="7">RECEIVED</option>
                                    </select>
                                    <div id="srch_status_feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-red" data-dismiss="modal">Close</button>
                    
                    <button type="submit" class="btn bg-blue float-right permission-button">Filter</button>
                    <button type="button" id="btn_search_excel" class="btn bg-green float-right">Download Excel</button>
                    <button type="button" id="btn_search_clear" class="btn btn-secondary float-right">Clear</button>
                </div>
                
            </div>
        </form>
    </div>
</div>