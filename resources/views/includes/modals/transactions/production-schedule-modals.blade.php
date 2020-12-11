<div id="modal_order_search" class="modal fade " data-backdrop="static">
    <div class="modal-dialog modal-md" role="document">
        <form method="GET" action="{{ url('/transaction/production-schedule/search-filter-orders') }}" id="frm_search">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Search / Filter Orders</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="loadingOverlay-modal"></div>

                    @csrf

                    <div class="row">
                        <div class="col-md-12">

                            <div class="form-group row">
                                <label for="srch_date_upload" class="col-sm-2 control-label mt-5">Upload Date:</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-sm">
                                        <input type="date" class="form-control validate srch-clear" name="srch_date_upload_from" id="srch_date_upload_from">
                                        
                                        <div class="input-group-append">
                                            <span class="input-group-text">-</span>
                                        </div>
                                        <input type="date" class="form-control validate srch-clear" name="srch_date_upload_to" id="srch_date_upload_to">
                                        
                                    </div>
                                    <div id="srch_date_upload_from_feedback"></div>
                                    <div id="srch_date_upload_to_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_sc_no" class="col-sm-2 control-label mt-5">SC No.:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control validate srch-clear" name="srch_sc_no" id="srch_sc_no">
                                    <div id="srch_sc_no_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_prod_code" class="col-sm-2 control-label mt-5">Product Code:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control validate srch-clear" name="srch_prod_code" id="srch_prod_code">
                                    <div id="srch_prod_code_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_description" class="col-sm-2 control-label mt-5">Description:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control validate srch-clear" name="srch_description" id="srch_description">
                                    <div id="srch_description_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_po" class="col-sm-2 control-label mt-5">P.O.:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control validate srch-clear" name="srch_po" id="srch_po">
                                    <div id="srch_po_feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-red" data-dismiss="modal">Close</button>
                    {{-- <button type="button" id="btn_search_excel" class="btn bg-green float-right">Download Excel</button> --}}
                    <button type="submit" class="btn bg-blue float-right permission-button">Filter</button>
                </div>
                
            </div>
        </form>
    </div>
</div>

<div id="modal_items" class="modal fade " data-backdrop="static">
    <div class="modal-dialog modal-full-xxl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Items</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="loadingOverlay-modal"></div>
                <input type="hidden" name="item_code" id="item_code" class="clear_hidden_materials_item_modal" />
                <input type="hidden" name="description" id="description" class="clear_hidden_materials_item_modal" />
                <input type="hidden" name="heat_no" id="heat_no" class="clear_hidden_materials_item_modal" />
                <input type="hidden" name="rmw_qty" id="rmw_qty" class="clear_hidden_materials_item_modal" />

                <div class="row">
                    <div class="col-md-5">
                        <table class="table table-sm table-bordered table-striped nowrap" style="width:100%" id="tbl_mateials_item">
                            <thead class="thead-dark">
                                <th>Material Code</th>
                                <th>Description</th>
                                <th>Heat #</th>
                                <th>Withdrawal Qty.(PCS)</th>
                            </thead>
                            <tbody id="tbl_mateials_item_body"></tbody>
                        </table>
                    </div>

                    <div class="col-md-7">
                        <table class="table table-sm table-bordered table-striped nowrap" style="width:100%" id="tbl_products_item">
                            <thead class="thead-dark">
                                <th width="3%"></th>
                                <th>SC No.</th>
                                <th>Product Code</th>
                                <th>Description</th>
                                <th>Order Qty</th>
                                <th>Sched Qty</th>
                                <th>P.O. No.</th>
                                <th>Status</th>
                                <th>Upload Date</th>
                            </thead>
                            <tbody id="tbl_products_item_body"></tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <span id="note" class='text-red'></span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        {{-- <div class="table-responsive"> --}}
                            <table class="table table-sm table-bordered table-striped nowrap" style="width:100%" id="tbl_bom">
                                <thead class="thead-dark">
                                    <th>Product Code</th>
                                    <th>Description</th>
                                    <th>SC #</th>
                                    <th>Order Qty</th>
                                    <th>Cut Weight</th>
                                    <th>Cut Length</th>
                                    <th>Cut Width</th>
                                    
                                    <th>Sched Qty</th>
                                    <th>Blade Consumption</th>
                                    <th>Qty. Per Piece</th>
                                    <th>Assign Qty</th>
                                    <th>Remaining Qty</th>
                                    <th>Lot No.</th>
                                    <th>Ship Date</th>
                                    <th>Material Used</th>
                                    <th>Mat. Length</th>
                                    <th>Mat. Std. Weight</th>
                                    <th>Mat. width</th>
                                    <th>Size</th>
                                </thead>
                                <tbody id="tbl_bom_body"></tbody>
                            </table>
                        {{-- </div> --}}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-red" data-dismiss="modal">Close</button>
                <button type="button" id="btn_save_bom" class="btn bg-blue float-right permission-button">
                    <i class="fa fa-floppy-o"></i> Save
                </button>
            </div>
            
        </div>
    </div>
</div>

<div id="modal_jo_details" class="modal fade " data-backdrop="static">
    <div class="modal-dialog modal-full-xxl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Job Order Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="loadingOverlay-modal"></div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="input-group input-group-sm mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">J.O. #</span>
                            </div>
                            <input type="text" class="form-control validate clear" name="j_jo_no" id="j_jo_no" readonly>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="input-group input-group-sm mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Ship Date</span>
                            </div>
                            <input type="date" class="form-control validate clear" name="j_ship_date" id="j_ship_date">
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-sm table-bordered table-striped nowrap" style="width:100%" id="tbl_jo_item_details">
                            <thead class="thead-dark">
                                <th></th>
                                <th>SC #</th>
                                <th>Product Code</th>
                                <th>Description</th>
                                <th>Order Qty</th>
                                <th>Sched Qty</th>
                                <th>Heat No.</th>
                                <th>Withdrawal Qty.(PCS)</th>
                                <th>Material Used</th>
                                <th>Lot No.</th>
                                <th>Blade Consumption</th>
                                <th>Cut Weight</th>
                                <th>Cut Length</th>
                                <th>Cut Width</th>
                                <th>Mat. Length</th>
                                <th>Mat. Std. Weight</th>
                                <th>Assign Qty</th>
                                <th>Remaining Qty</th>
                            </thead>
                            <tbody id="tbl_jo_item_details_body"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-red" data-dismiss="modal">Close</button>
                <button type="button" id="btn_save_jo_item" class="btn bg-blue float-right permission-button">
                    <i class="fa fa-floppy-o"></i> Save
                </button>
            </div>
            
        </div>
    </div>
</div>

<div id="modal_jo_search" class="modal fade " data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <form method="GET" action="{{ url('/transaction/production-schedule/search-filter-jo') }}" id="frm_search_jo">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Search / Filter Job Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="loadingOverlay-modal"></div>

                    @csrf

                    <div class="row">
                        <div class="col-md-6">

                            <div class="form-group row">
                                <label for="srch_date" class="col-sm-2 control-label mt-5">Date:</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-sm">
                                        <input type="date" class="form-control validate srch-clear" name="srch_jdate_from" id="srch_jdate_from">
                                        
                                        <div class="input-group-append">
                                            <span class="input-group-text">-</span>
                                        </div>
                                        <input type="date" class="form-control validate srch-clear" name="srch_jdate_to" id="srch_jdate_to">
                                        
                                    </div>
                                    <div id="srch_jdate_from_feedback"></div>
                                    <div id="srch_jdate_to_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_jjo_no" class="col-sm-2 control-label mt-5">J.O. No.:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control validate srch-clear" name="srch_jjo_no" id="srch_jjo_no">
                                    <div id="srch_jjo_no_feedback"></div>
                                </div>
                            </div>
                            

                            <div class="form-group row">
                                <label for="srch_jprod_code" class="col-sm-2 control-label mt-5">Prod. Code:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control validate srch-clear" name="srch_jprod_code" id="srch_jprod_code">
                                    <div id="srch_jprod_code_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_jsc_no" class="col-sm-2 control-label mt-5">SC No.:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control validate srch-clear" name="srch_jsc_no" id="srch_jsc_no">
                                    <div id="srch_jsc_no_feedback"></div>
                                </div>
                            </div>                            
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="srch_jdescription" class="col-sm-2 control-label mt-5">Description:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control validate srch-clear" name="srch_jdescription" id="srch_jdescription">
                                    <div id="srch_jdescription_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_jmaterial_used" class="col-sm-2 control-label mt-5">Mat. Used:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control validate srch-clear" name="srch_jmaterial_used" id="srch_jmaterial_used">
                                    <div id="srch_jmaterial_used_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_jmaterial_heat_no" class="col-sm-2 control-label mt-5">Mat. Heat #:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control validate srch-clear" name="srch_jmaterial_heat_no" id="srch_jmaterial_heat_no">
                                    <div id="srch_jmaterial_heat_no_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_jstatus" class="col-sm-2 control-label mt-5">Status:</label>
                                <div class="col-sm-10">
                                    <select class="form-control validate srch-clear" name="srch_jstatus" id="srch_jstatus">
                                        <option value=""></option>
                                        <option value="0">No quantity issued</option>
                                        <option value="1">Ready of printing</option>
                                        <option value="2">On Production</option>
                                        <option value="3">Cancelled</option>
                                        <option value="5">Closed</option>
                                    </select>
                                    <div id="srch_jstatus_feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-red" data-dismiss="modal">Close</button>
                    {{-- <button type="button" id="btn_search_excel" class="btn bg-green float-right">Download Excel</button> --}}
                    <button type="submit" class="btn bg-blue float-right permission-button">Filter</button>
                </div>
                
            </div>
        </form>
    </div>
</div>
