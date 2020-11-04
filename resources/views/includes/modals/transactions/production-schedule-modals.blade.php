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