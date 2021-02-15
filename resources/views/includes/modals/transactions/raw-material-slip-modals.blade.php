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
                            <input type="date" class="form-control input-sm validate" name="date" id="date">
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

                <div class="form-group row">
                    <div class="col-sm-12">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">ISO Code:</span>
                            </div>
                            <select class="form-control input-sm validate clear" name="iso" id="iso">
                                @foreach ($iso as $i)
                                    <option value="{{ $i->iso_code }}">{{ $i->iso_name }}</option>
                                @endforeach
                                
                            </select>
                            <div id="iso_feedback"></div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="print_format" id="print_format" value="material_withdrawal">

                {{-- <div class="form-group row">
                    <div class="col-sm-12">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Print Format:</span>
                            </div>
                            <select class="form-control input-sm validate clear" name="print_format" id="print_format">
                                <option value=""></option>
                                <option value="material_withdrawal">Material Withdrawal</option>
                                <option value="same_with_cutting_schedule">Same w/ Cutting Schedule</option>
                            </select>
                            <div id="print_format_feedback"></div>
                        </div>
                    </div>
                </div> --}}
                        
            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-red" data-dismiss="modal">Close</button>
                <button type="button" id="btn_print" class="btn bg-navy pull-right" target="_tab">
                    <i class="fa fa-print"></i> Print Preview
                </button>
            </div>

        </div>
    </div>
</div>

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
                            <th width="3.66"></th>
                            <th width="6.66%">Receiving No.</th>
                            <th width="6.66%">Material Type</th>
                            <th width="13.66%">Material Code</th>
                            <th width="6.66%">Item</th>
                            <th width="6.66%">Alloy</th>
                            <th width="6.66%">Size</th>
                            <th width="6.66%">Length</th>
                            <th width="4.66%">Qty(KGS)</th>
                            <th width="4.66%">Qty(PCS)</th>
                            <th width="6.66%">Current Stock</th>
                            <th width="6.66%">Heat No.</th>
                            <th width="6.66%">Invoice No.</th>
                            <th width="6.66%">Received Date</th>
                            <th width="6.66%">Supplier</th>
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

<div id="modal_raw_material_search" class="modal fade " data-backdrop="static">
    <div class="modal-dialog modal-full" role="document">
        <form method="GET" action="{{ url('/transaction/raw-material-withdrawal/search-filter-raw-material') }}" id="frm_search">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Search / Filter Withdrawed Raw Materials</h5>
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
                        </div>
                        <div class="col-md-4">

                             <div class="form-group row">
                                <label for="srch_heat_no" class="col-sm-3 control-label mt-5">Material Heat No:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm validate srch-clear" id="srch_heat_no" name="srch_heat_no">
                                        <div id="srch_heat_no_feedback"></div>                              
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_mat_code" class="col-sm-3 control-label mt-5">Material Code:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm validate srch-clear" id="srch_mat_code" name="srch_mat_code" maxlength="16">
                                    <div id="srch_mat_code_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_length" class="col-sm-3 control-label mt-5">Length:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm validate srch-clear" id="srch_length" name="srch_length" maxlength="10">
                                    <div id="srch_length_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="srch_alloy" class="col-sm-3 control-label mt-5">Alloy:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm validate srch-clear" id="srch_alloy" name="srch_alloy" maxlength="20">
                                    <div id="srch_alloy_feedback"></div>
                                </div>
                            </div>

                            
                        </div>

                        <div class="col-md-4">
                            <div class="form-group row">
                                <label for="srch_size" class="col-sm-3 control-label mt-5">Size:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm validate srch-clear" id="srch_size" name="srch_size" maxlength="10">
                                    <div id="srch_size_feedback"></div>
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
                                    <th>Withdrawal Slip No.</th>
                                    <th>Material Code</th>
                                    <th>Material Heat No.</th>
                                    <th>Alloy</th>
                                    <th>Item</th>
                                    <th>Size</th>
                                    <th>Schedule</th>
                                    <th>Length</th>
                                    <th>Qty Withdrawed</th>
                                    <th>Withdrawed By</th>
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