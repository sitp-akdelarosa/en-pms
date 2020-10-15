<div id="modal_material_inventory" class="modal fade " data-backdrop="static">
    <div class="modal-dialog modal-full" role="document">
        <form method="POST" action="{{ url('/transaction/update-inventory/AddManual') }}" id="frm_material_inventory">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add / Edit Material Inventory</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="loadingOverlay-modal"></div>

                    @csrf
                    <input type="hidden" class="clear" name="material_id" id="material_id">

                    <div class="row mb-15">
                        <div class="offset-md-7 col-md-5">
                            <div class="form-group row">
                                <label for="received_date" class="col-sm-3 control-label mt-5">Received Date:</label>
                                <div class="col-sm-9">
                                    <input type="date" class="form-control validate clear" name="received_date" id="received_date">
                                    <div id="received_date_feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group row">
                                <label for="receiving_no" class="col-sm-4 control-label mt-5">Receiving No.:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control validate clear" name="receiving_no" id="receiving_no">
                                    <div id="receiving_no_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="materials_type" class="col-sm-4 control-label mt-5">Material Type:</label>
                                <div class="col-sm-8">
                                    <select class="form-control select-validate clear" name="materials_type" id="materials_type"></select>
                                    <div id="materials_type_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="materials_code" class="col-sm-4 control-label mt-5">Material Code:</label>
                                <div class="col-sm-8">
                                    <select class="form-control select-validate clear" name="materials_code" id="materials_code">
                                        <option value=''></option>
                                    </select>
                                    <div id="materials_code_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <textarea class="form-control validate clear" name="description" id="description" style="height:100px;resize: none" placeholder="Description" readonly></textarea>
                                    <div id="description_feedback"></div>
                                </div>
                            </div>
                            
                        </div>

                        <div class="col-md-4">
                            <div class="form-group row">
                                <label for="item" class="col-sm-3 control-label mt-5">Item:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate clear" name="item" id="item" readonly>
                                    <div id="item_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="alloy" class="col-sm-3 control-label mt-5">Alloy:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate clear" name="alloy" id="alloy" readonly>
                                    <div id="alloy_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="schedule" class="col-sm-3 control-label mt-5">Schedule:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate clear" name="schedule" id="schedule" readonly>
                                    <div id="schedule_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="size" class="col-sm-3 control-label mt-5">Size:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate clear" name="size" id="size" readonly>
                                    <div id="size_feedback"></div>
                                </div>
                            </div>
                            {{-- <div class="form-group row">
                                <label for="thickness" class="col-sm-3 control-label mt-5">Thickness:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate clear" name="thickness" id="thickness" >
                                    <div id="thickness_feedback"></div>
                                </div>
                            </div> --}}
                             <div class="form-group row">
                                <label for="width" class="col-sm-3 control-label mt-5">Width:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate clear" name="width" id="width">
                                    <div id="width_feedback"></div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="length" class="col-sm-3 control-label mt-5">Length:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate clear" name="length" id="length">
                                    <div id="length_feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            {{-- <div class="form-group row">
                                <label for="quantity" class="col-sm-3 control-label mt-5">Qty:</label>
                                <div class="col-sm-3">
                                    <input type="number" class="form-control validate clear" name="quantity" id="quantity" step="any">
                                    <div id="quantity_feedback"></div>
                                </div>

                                <label for="uom" class="col-sm-2 control-label mt-5">UOM:</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control validate clear" name="uom" id="uom">
                                    <div id="uom_feedback"></div>
                                </div>
                            </div> --}}

                            <div class="form-group row">
                                <label for="qty_weight" class="col-sm-3 control-label mt-5">Qty/Weight:</label>
                                <div class="col-sm-9">
                                    <div class="input-group mb-3 input-group-sm">
										<input type="number" class="form-control validate clear " step=".01" name="qty_weight" id="qty_weight">
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
										<input type="number" class="form-control validate clear " name="qty_pcs" id="qty_pcs">
                                        <div class="input-group-append">
                                            <span class="input-group-text">PCS</span>
                                        </div>
                                        <div id="qty_pcs_feedback"></div>
									</div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="heat_no" class="col-sm-3 control-label mt-5">Heat #:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate clear" name="heat_no" id="heat_no">
                                    <div id="heat_no_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="invoice_no" class="col-sm-3 control-label mt-5">Invoice No.:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate clear" name="invoice_no" id="invoice_no">
                                    <div id="invoice_no_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="supplier" class="col-sm-3 control-label mt-5">Supplier:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate clear" name="supplier" id="supplier">
                                    <div id="supplier_feedback"></div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="supplier_heat_no" class="col-sm-3 control-label mt-5">Supplier Heat No:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate clear" name="supplier_heat_no" id="supplier_heat_no">
                                    <div id="supplier_heat_no_feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-red" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-blue float-right permission-button" id="AddInventoryUpdate">Save</button>
                </div>
                
            </div>
        </form>
    </div>
</div>

<div id="modal_material_not_existing" class="modal fade " data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <form>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">These Material Code are not registered in Material Master</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <p>Most of data are uploaded but these material are not registered in Material Master.</p>
                    <br>
                    <table class="table table-sm table-striped dt-responsive nowrap" id="tbl_material_not_existing" style="width:100%">
                        <thead class="thead-dark">
                            <th>Material Type</th>
                            <th>Material Code</th>
                            <th>Qty(KGS)</th>
                            <th>Qty(PCS)</th>
                            <th>Heat No.</th>
                            <th>Invoice No.</th>
                            <th>Received Date</th>
                            <th>Supplier</th>
                        </thead>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-red" data-dismiss="modal">Close</button>
                    <button type="button" class="btn bg-green float-right permission-button" id="btn_excel">
                         <i class="fa fa-download"></i> Download excel file
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="modal_same_material" class="modal fade " data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <form>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">These Materials were already uploaded.</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <table class="table table-sm table-striped nowrap" id="tbl_same_material" style="width:100%">
                        <thead class="thead-dark">
                            <th>Receiving No.</th>
                            <th>Material Type</th>
                            <th>Material Code</th>
                            <th>Qty(KGS)</th>
                            <th>Qty(PCS)</th>
                            <th>Heat No.</th>
                            <th>Length</th>
                            <th>Invoice No.</th>
                            <th>Received Date</th>
                            <th>Supplier</th>
                        </thead>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-red" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="modal_material_search" class="modal fade " data-backdrop="static">
    <div class="modal-dialog modal-full" role="document">
        <form method="GET" action="{{ url('/transaction/update-inventory/search-filter') }}" id="frm_search">
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
                                <label for="srch_received_date" class="col-sm-4 control-label mt-5">Received Date:</label>
                                <div class="col-sm-8">
                                    <div class="input-group input-group-sm">
                                        <input type="date" class="form-control validate srch-clear" name="srch_received_date_from" id="srch_received_date_from">
                                        
                                        <div class="input-group-append">
                                            <span class="input-group-text">-</span>
                                        </div>
                                        <input type="date" class="form-control validate srch-clear" name="srch_received_date_to" id="srch_received_date_to">
                                        
                                    </div>
                                    <div id="srch_received_date_from_feedback"></div>
                                    <div id="srch_received_date_to_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_receiving_no" class="col-sm-4 control-label mt-5">Receiving No.:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control validate srch-clear" name="srch_receiving_no" id="srch_receiving_no">
                                    <div id="receiving_no_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_materials_type" class="col-sm-4 control-label mt-5">Material Type:</label>
                                <div class="col-sm-8">
                                    <select class="form-control select-validate srch-clear" name="srch_materials_type" id="srch_materials_type"></select>
                                    <div id="srch_materials_type_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_materials_code" class="col-sm-4 control-label mt-5">Material Code:</label>
                                <div class="col-sm-8">
                                    <select class="form-control select-validate srch-clear" name="srch_materials_code" id="srch_materials_code">
                                        <option value=''></option>
                                    </select>
                                    <div id="srch_materials_code_feedback"></div>
                                </div>
                            </div>
                            
                        </div>

                        <div class="col-md-4">
                            <div class="form-group row">
                                <label for="srch_item" class="col-sm-3 control-label mt-5">Item:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate srch-clear" name="srch_item" id="srch_item">
                                    <div id="srch_item_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_alloy" class="col-sm-3 control-label mt-5">Alloy:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate srch-clear" name="srch_alloy" id="srch_alloy">
                                    <div id="srch_alloy_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_schedule" class="col-sm-3 control-label mt-5">Schedule:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate srch-clear" name="srch_schedule" id="srch_schedule">
                                    <div id="srch_schedule_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_size" class="col-sm-3 control-label mt-5">Size:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate srch-clear" name="srch_size" id="srch_size">
                                    <div id="srch_size_feedback"></div>
                                </div>
                            </div>
                             <div class="form-group row">
                                <label for="srch_width" class="col-sm-3 control-label mt-5">Width:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate srch-clear" name="srch_width" id="srch_width">
                                    <div id="srch_width_feedback"></div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="srch_length" class="col-sm-3 control-label mt-5">Length:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate srch-clear" name="srch_length" id="srch_length">
                                    <div id="srch_length_feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">

                            <div class="form-group row">
                                <label for="srch_heat_no" class="col-sm-3 control-label mt-5">Heat #:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate srch-clear" name="srch_heat_no" id="srch_heat_no">
                                    <div id="srch_heat_no_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_invoice_no" class="col-sm-3 control-label mt-5">Invoice No.:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate srch-clear" name="srch_invoice_no" id="srch_invoice_no">
                                    <div id="srch_invoice_no_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_supplier" class="col-sm-3 control-label mt-5">Supplier:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate srch-clear" name="srch_supplier" id="srch_supplier">
                                    <div id="srch_supplier_feedback"></div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="srch_supplier_heat_no" class="col-sm-3 control-label mt-5">Supplier Heat No:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate srch-clear" name="srch_supplier_heat_no" id="srch_supplier_heat_no">
                                    <div id="srch_supplier_heat_no_feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-red" data-dismiss="modal">Close</button>
                    <button type="button" id="btn_search_excel" class="btn bg-green float-right">Download Excel</button>
                    <button type="submit" class="btn bg-blue float-right permission-button">Filter</button>
                </div>
                
            </div>
        </form>
    </div>
</div>