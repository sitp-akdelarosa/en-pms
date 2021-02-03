<div id="modal_material_inventory" class="modal fade " data-backdrop="static">
    <div class="modal-dialog modal-full" role="document">
        <form method="POST" action="{{ url('/transaction/update-inventory/AddManual') }}" id="frm_material_inventory">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add / Edit Inventory</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="loadingOverlay-modal"></div>

                    @csrf
                    <input type="hidden" class="clear" name="item_id" id="item_id">

                    <div class="row">
                        <div class="col-md-4">

                            <div class="form-group row">
                                <label for="item_class" class="col-sm-4 control-label mt-5">Item Class:</label>
                                <div class="col-sm-8">
                                    <select class="form-control select-validate clear" name="item_class" id="item_class">
                                        <option value=""></option>
                                        @foreach($item_classes as $class)
                                            <option value="{{ $class->description }}">{{ $class->description }}</option>
                                        @endforeach
                                    </select>
                                    <div id="item_class_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row material_div">
                                <label for="receiving_no" class="col-sm-4 control-label mt-5">Receiving No.:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control validate clear" name="receiving_no" id="receiving_no">
                                    <div id="receiving_no_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row material_div">
                                <label for="received_date" class="col-sm-4 control-label mt-5">Received Date:</label>
                                <div class="col-sm-8">
                                <input type="date" class="form-control validate clear" name="received_date" id="received_date" value="<?php echo date('Y-m-d');?>">
                                    <div id="received_date_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row product_div">
                                <label for="jo_no" class="col-sm-4 control-label mt-5">J.O. No.:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control validate clear" name="jo_no" id="jo_no">
                                    <div id="jo_no_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row product_div">
                                <label for="product_line" class="col-sm-4 control-label mt-5">Product Line:</label>
                                <div class="col-sm-8">
                                    <select class="form-control select-validate clear" name="product_line" id="product_line" style="width:100%;">
                                        <option value=""></option>
                                    </select>
                                    <div id="product_line_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row material_div">
                                <label for="materials_type" class="col-sm-4 control-label mt-5">Material Type:</label>
                                <div class="col-sm-8">
                                    <select class="form-control select-validate clear" name="materials_type" id="materials_type" style="width:100%;">
                                        <option value=""></option>
                                    </select>
                                    <div id="materials_type_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="item_code" class="col-sm-4 control-label mt-5">Item Code:</label>
                                <div class="col-sm-8">
                                    <select class="form-control select-validate clear" name="item_code" id="item_code">
                                        <option value=''></option>
                                    </select>
                                    <div id="item_code_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <textarea class="form-control validate clear" name="description" id="description" style="height:50px;resize: none" placeholder="Description" readonly></textarea>
                                    <div id="description_feedback"></div>
                                </div>
                            </div>
                            
                        </div>

                        <div class="col-md-4">
                            <div class="form-group row">
                                <label for="warehouse" class="col-sm-3 control-label mt-5">Warehouse:</label>
                                <div class="col-sm-9">
                                    <select class="form-control select-validate srch-clear" name="warehouse" style="width:100%;" id="warehouse">
                                        <option value=""></option>
                                    </select>
                                    <div id="warehouse_feedback"></div>
                                </div>
                            </div>
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
                                <label for="schedule" class="col-sm-3 control-label mt-5">Schedule/Class:</label>
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

                             <div class="form-group row material_div">
                                <label for="width" class="col-sm-3 control-label mt-5">Width:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate clear" name="width" id="width">
                                    <div id="width_feedback"></div>
                                </div>
                            </div>
                            <div class="form-group row material_div">
                                <label for="length" class="col-sm-3 control-label mt-5">Length:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate clear" name="length" id="length">
                                    <div id="length_feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">

                            <div class="form-group row">
                                <label for="qty_weight" class="col-sm-3 control-label mt-5">Qty/Weight:</label>
                                <div class="col-sm-9">
                                    <div class="input-group mb-3 input-group-sm">
                                        <input type="number" step="0.01" class="form-control validate clear number" name="qty_weight" id="qty_weight">
                                        <input type="hidden" class="form-control validate clear " name="finish_weight" id="finish_weight">
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
										<input type="number" step="0.01" class="form-control validate clear number" name="qty_pcs" id="qty_pcs">
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

                            <div class="form-group row product_div">
                                <label for="lot_no" class="col-sm-3 control-label mt-5">Lot #:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate clear" name="lot_no" id="lot_no">
                                    <div id="lot_no_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row material_div">
                                <label for="invoice_no" class="col-sm-3 control-label mt-5">Invoice No.:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate clear" name="invoice_no" id="invoice_no">
                                    <div id="invoice_no_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row material_div">
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

                            <div class="form-group row product_div">
                                <label for="material_used" class="col-sm-3 control-label mt-5">Material Used (Code):</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate clear" name="material_used" id="material_used">
                                    <div id="material_used_feedback"></div>
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
                    <h5 class="modal-title">These Codes are not registered in Material/Product Master</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <p>Most of data are uploaded but these codes are not registered in Material/Product Master.</p>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-bordered nowrap" id="tbl_material_not_existing" style="width:100%">
                            <thead class="thead-dark">
                                <th>Item Class</th>
                                <th>J.O. / Receiving #</th>
                                <th>Mat. Type / Prod. Line</th>
                                <th>Item Code</th>
                                <th>Qty(KGS)</th>
                                <th>Qty(PCS)</th>
                                <th>Heat No.</th>
                                <th>Lot No.</th>
                                <th>Invoice No.</th>
                                <th>Received Date</th>
                                <th>Supplier</th>
                            </thead>
                        </table>
                    </div>
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
                            <th>Product Line</th>
                            <th>Item Code</th>
                            <th>Warehouse</th>
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

<div id="modal_search" class="modal fade " data-backdrop="static">
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
                                <label for="srch_item_class" class="col-sm-4 control-label mt-5">Item Class:</label>
                                <div class="col-sm-8">
                                    <select class="form-control select-validate clear" name="srch_item_class" id="srch_item_class">
                                        <option value=""></option>
                                        @foreach($item_classes as $class)
                                            <option value="{{ $class->description }}">{{ $class->description }}</option>
                                        @endforeach
                                    </select>
                                    <div id="srch_item_class_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row srch_material_div">
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

                            <div class="form-group row srch_material_div">
                                <label for="srch_receiving_no" class="col-sm-4 control-label mt-5">Receiving No.:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control validate srch-clear" name="srch_receiving_no" id="srch_receiving_no">
                                    <div id="srchreceiving_no_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row srch_product_div">
                                <label for="srch_jo_no" class="col-sm-4 control-label mt-5">J.O. No.:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control validate srch-clear" name="srch_jo_no" id="srch_jo_no">
                                    <div id="srch_jo_no_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row srch_material_div">
                                <label for="srch_materials_type" class="col-sm-4 control-label mt-5">Material Type:</label>
                                <div class="col-sm-8">
                                    <select class="form-control select-validate srch-clear" name="srch_materials_type" style="width:100%;" id="srch_materials_type">
                                        <option value=""></option>
                                    </select>
                                    <div id="srch_materials_type_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row srch_product_div">
                                <label for="srch_product_line" class="col-sm-4 control-label mt-5">Product Line:</label>
                                <div class="col-sm-8">
                                    <select class="form-control select-validate srch-clear" name="srch_product_line" style="width:100%;" id="srch_product_line">
                                        <option value=""></option>
                                    </select>
                                    <div id="srch_product_line_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_item_code" class="col-sm-4 control-label mt-5">Item Code:</label>
                                <div class="col-sm-8">
                                    <select class="form-control select-validate srch-clear" name="srch_item_code" id="srch_item_code">
                                        <option value=''></option>
                                    </select>
                                    <div id="srch_item_code_feedback"></div>
                                </div>
                            </div>
                            
                        </div>

                        <div class="col-md-4">
                            <div class="form-group row">
                                <label for="srch_warehouse" class="col-sm-3 control-label mt-5">Warehouse:</label>
                                <div class="col-sm-9">
                                    <select class="form-control select-validate srch-clear" name="srch_warehouse" style="width:100%;" id="srch_warehouse">
                                        <option value=""></option>
                                    </select>
                                    <div id="srch_warehouse_feedback"></div>
                                </div>
                            </div>

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
                                <label for="srch_schedule" class="col-sm-3 control-label mt-5">Schedule/Class:</label>
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
                             <div class="form-group row srch_material_div">
                                <label for="srch_width" class="col-sm-3 control-label mt-5">Width:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate srch-clear" name="srch_width" id="srch_width">
                                    <div id="srch_width_feedback"></div>
                                </div>
                            </div>
                            <div class="form-group row srch_material_div">
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

                            <div class="form-group row srch_product_div">
                                <label for="srch_lot_no" class="col-sm-3 control-label mt-5">Lot #:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate srch-clear" name="srch_lot_no" id="srch_lot_no">
                                    <div id="srch_lot_no_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row srch_material_div">
                                <label for="srch_invoice_no" class="col-sm-3 control-label mt-5">Invoice No.:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate srch-clear" name="srch_invoice_no" id="srch_invoice_no">
                                    <div id="srch_invoice_no_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row srch_material_div">
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
                            <div class="form-group row srch_product_div">
                                <label for="srch_material_used" class="col-sm-3 control-label mt-5">Material Used (Code):</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate clear" name="srch_material_used" id="srch_material_used">
                                    <div id="srch_material_used_feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-md-12" style="display:none" id="progress">
                            <div class="progress">
                                <div class="progress-bar progress-bar-success progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="00" aria-valuemin="0" aria-valuemax="100" style="width: 00%">
                                    <span class="sr-only"></span>
                                </div>
                            </div>
                            <span class="progress-msg">Processing...</span>
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

<div id="modal_excel_format" class="modal fade " data-backdrop="static">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Choose Upload Format</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="loadingOverlay-modal"></div>
                <div class="row justify-content-center mb-5">
                    <div class="col-md-12">
                        <button type="button" id="btn_material_format" class="btn bg-blue btn-block">Raw Material Format</button>
                    </div>
                </div>
                <div class="row justify-content-center mb-5">
                    <div class="col-md-12">
                        <button type="button" id="btn_product_format" class="btn bg-green btn-block">Product Format</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-red" data-dismiss="modal">Close</button>
            </div>
            
        </div>
    </div>
</div>

<div id="modal_check_delete" class="modal fade " data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <form>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Already recorded in Withdrawal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <p style="font-size: 11px;">These codes were already recorded in Withdrawal.</p>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-bordered nowrap" id="tbl_check_delete" style="width:100%">
                            <thead class="thead-dark">
                                <th>Item Class</th>
                                <th>Item Code</th>
                                <th>Description</th>
                                <th>Lot No./Heat No.</th>
                                <th>Length</th>
                                <th>Warehouse</th>
                                <th>Issued_qty</th>
                                <th>Mat. Type / Prod. Line</th>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-red" data-dismiss="modal">Close</button>
                    {{-- <button type="button" class="btn bg-green float-right permission-button" id="btn_excel">
                         <i class="fa fa-download"></i> Download excel file
                    </button> --}}
                </div>
            </div>
        </form>
    </div>
</div>