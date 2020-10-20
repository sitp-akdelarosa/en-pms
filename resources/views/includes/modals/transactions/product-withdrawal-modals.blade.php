<div id="modal_inventory" class="modal fade " data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <form>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Inventory</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="loadingOverlay-modal"></div>
                    <table class="table table-sm table-striped nowrap table-bordered" id="tbl_inventory" style="width:100%">
                        <thead class="thead-dark">
                            <th width="6.66%"></th>
                            <th width="6.66%">Item Class</th>
                            <th width="6.66%">J.O. No.</th>
                            <th width="6.66%">Item Code</th>
                            <th width="6.66%">description</th>
                            <th width="6.66%">Product Line</th>
                            <th width="6.66%">Lot #</th>
                            <th width="6.66%">Heat #</th>
                            <th width="6.66%">Qty(KGS)</th>
                            <th width="6.66%">Qty(PCS)</th>
                            <th width="6.66%">Current Stock</th>
                            <th width="6.66%">Alloy</th>
                            <th width="6.66%">Item</th>
                            <th width="6.66%">Size</th>
                            <th width="6.66%">Schedule/Class</th>
                        </thead>
                        <tbody id="tbl_inventory_body"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-red" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="modal_withdrawal_slip" class="modal fade " data-backdrop="static">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Withdrawal Slip Preparation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="form-group row">
                    <div class="col-sm-12">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Date:</span>
                            </div>
                            <input type="date" class="form-control input-sm validate" name="date" id="date" value="{{date('Y-m-d')}}">
                            <div id="date_feedback"></div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group row">
                    <div class="col-sm-12">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Prepared By:</span>
                            </div>
                            <input type="text" class="form-control input-sm validate" name="prepared_by" id="prepared_by" 
                            value="{{Auth::user()->firstname}} {{Auth::user()->lastname}}">
                            <div id="prepared_by_feedback"></div>
                        </div>
                    </div>
                </div>
            
                <div class="form-group row">
                    <div class="col-sm-12">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Issued by:</span>
                            </div>
                            <input type="text" class="form-control input-sm validate clear" name="issued_by" id="issued_by">
                            <div id="issued_by_feedback"></div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-12">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Received by:</span>
                            </div>
                            <input type="text" class="form-control input-sm validate clear" name="received_by" id="received_by">
                            <div id="received_by_feedback"></div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-12">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Plant:</span>
                            </div>
                            <input type="text" class="form-control input-sm validate clear" name="plant" id="plant">
                            <div id="plant_feedback"></div>
                        </div>
                    </div>
                </div>
                        
            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-red" data-dismiss="modal">Close</button>
                <button type="button" id="btn_print_finished" class="btn bg-green pull-right btn_print" data-print_format="FINISHED" target="_tab">
                    <i class="fa fa-print"></i> Finished
                </button>
                <button type="button" id="btn_print_crude" class="btn bg-blue pull-right btn_print" data-print_format="CRUDE" target="_tab">
                    <i class="fa fa-print"></i> Crude
                </button>
            </div>

        </div>
    </div>
</div>

<div id="modal_product_search" class="modal fade " data-backdrop="static">
    <div class="modal-dialog modal-full" role="document">
        <form method="GET" action="{{ url('/transaction/product-withdrawal/search-filter-product-withdrawal') }}" id="frm_search">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Search / Filter Withdrawed Product</h5>
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
                                <label for="srch_date_withdrawal" class="col-sm-3 control-label mt-5">Withdrawal Date:</label>
                                <div class="col-sm-9">
                                    <div class="input-group input-group-sm">
                                        <input type="date" class="form-control validate srch-clear" name="srch_date_withdrawal_from" id="srch_date_withdrawal_from">
                                        
                                        <div class="input-group-append">
                                            <span class="input-group-text">-</span>
                                        </div>
                                        <input type="date" class="form-control validate srch-clear" name="srch_date_withdrawal_to" id="srch_date_withdrawal_to">
                                        
                                    </div>
                                    <div id="srch_date_withdrawal_from_feedback"></div>
                                    <div id="srch_date_withdrawal_to_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_trans_no" class="col-sm-3 control-label mt-5">Withdrawal No.:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm validate srch-clear" id="srch_trans_no" name="srch_trans_no">
                                        <div id="srch_trans_no_feedback"></div>                              
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_item_class" class="col-sm-3 control-label mt-5">Item Class:</label>
                                <div class="col-sm-9">
                                    <select class="form-control select-validate clear" id="srch_item_class" name="srch_item_class">
                                        <option value=""></option>
                                        <option value="CRUDE">CRUDE</option>
                                        <option value="FINISHED">FINISHED</option>
                                    </select>
                                    <div id="srch_item_class_feedback"></div>                              
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_item_code" class="col-sm-3 control-label mt-5">Item Code:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm validate srch-clear" id="srch_item_code" name="srch_item_code" maxlength="16">
                                    <div id="srch_item_code_feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                             

                            <div class="form-group row">
                                <label for="srch_jo_no" class="col-sm-3 control-label mt-5">J.O. #:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm validate srch-clear" id="srch_jo_no" name="srch_jo_no" maxlength="10">
                                    <div id="srch_jo_no_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_lot_no" class="col-sm-3 control-label mt-5">Lot #:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm validate srch-clear" id="srch_lot_no" name="srch_lot_no" maxlength="20">
                                    <div id="srch_lot_no_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_heat_no" class="col-sm-3 control-label mt-5">Heat #:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm validate srch-clear" id="srch_heat_no" name="srch_heat_no" maxlength="10">
                                    <div id="srch_heat_no_feedback"></div>
                                </div>
                            </div>

                             <div class="form-group row">
                                <label for="srch_sc_no" class="col-sm-3 control-label mt-5">SC #:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm validate srch-clear" id="srch_sc_no" name="srch_sc_no" maxlength="15">
                                    <div id="srch_sc_no_feedback"></div>
                                </div>
                            </div>
                            
                        </div>

                        <div class="col-md-4">
                            <div class="form-group row">
                                <label for="srch_alloy" class="col-sm-3 control-label mt-5">Alloy:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm validate srch-clear" id="srch_alloy" name="srch_alloy" maxlength="20">
                                    <div id="srch_alloy_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_item" class="col-sm-3 control-label mt-5">Item:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm validate srch-clear" id="srch_item" name="srch_item" maxlength="20">
                                    <div id="srch_item_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_size" class="col-sm-3 control-label mt-5">Size:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm validate srch-clear" id="srch_size" name="srch_size" maxlength="20">
                                    <div id="srch_size_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_schedule" class="col-sm-3 control-label mt-5">Schedule:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm validate srch-clear" id="srch_schedule" name="srch_schedule" maxlength="15">
                                    <div id="srch_schedule_feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-sm table-striped nowrap table-bordered" id="tbl_search" style="width:100%">
                                <thead class="thead-dark">
                                    <th></th>
                                    <th>Withdrawal Slip No.</th>
                                    <th>Item Class</th>
                                    <th>Item Code</th>
                                    <th>J.O. #</th>
                                    <th>Lot #</th>
                                    <th>Heat #</th>
                                    <th>SC #</th>
                                    <th>Alloy</th>
                                    <th>Item</th>
                                    <th>Size</th>
                                    <th>Class</th>
                                    <th>Qty Withdrawed</th>
                                    <th>Remarks</th>
                                    <th>Date Withdrawed</th>
                                </thead>
                                <tbody id="tbl_search_body"></tbody>
                            </table>
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