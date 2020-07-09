<div id="modal_travel_sheet" class="modal fade " data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" action="{{ url('/transaction/travel-sheet/set-up/save') }}" id="frm_travel_sheet">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Set Up Travel Sheet</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="loadingOverlay"></div>
                    @csrf
                    <input type="hidden" name="idJO" id="idJO">
                    <input type="hidden" name="travel_sheet_id" id="travel_sheet_id">
                    <input type="hidden" name="jo_no" id="jo_no">
                    <input type="hidden" name="sc_no" id="sc_no">

                    <div class="row">
                        <div class="col-md-6">

                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Product Code</span>
                                        </div>
                                        <input type="text" class="form-control disableOnProduction" name="prod_code" id="prod_code" readonly>
                                        <div id="prod_code_feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="input-group input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">ISO Control No.:</span>
                                        </div>
                                        <select class="form-control select-validate disableOnProduction" name="iso_no" id="iso_no"></select>
                                        <div id="iso_no_feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row mb-5">
                                <div class="col-sm-6 mb-5">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Issued Qty</span>
                                        </div>
                                        <input type="number" class="form-control disableOnProduction" name="issued_qty" id="issued_qty">
                                        <div id="issued_qty_feedback"></div>
                                    </div>
                                </div>
                                <div class="col-sm-6 mb-5">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Total Issued Qty</span>
                                        </div>
                                        <input type="number" class="form-control disableOnProduction" name="issued_qty_table" id="issued_qty_table" readonly>
                                        <div id="issued_qty_table_feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row mb-5">
                                <div class="col-sm-6 mb-5">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Qty. per sheet</span>
                                        </div>
                                        <input type="number" class="form-control disableOnProduction" name="qty_per_sheet" id="qty_per_sheet">
                                        <div id="qty_per_sheet_feedback"></div>
                                    </div>
                                </div>
                            </div>

                                <div class="col-sm-3">
                                    <button type="button" class="btn btn-block bg-green mb-3 disableOnProduction" id="btn_add_prod">
                                        <i class="fa fa-plus"></i> Add
                                    </button>
                                </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover table-striped dt-responsive nowrap" id="tbl_product" style="width: 100%">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th width="20%">Product Code</th>
                                                    <th width="10%">Issued Qty</th>
                                                    <th width="20%">SC. Qty</th>
                                                    <th width="50%">SC No.</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbl_product_body"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-6">
                            <select class="form-control mb-5 disableOnProduction" name="set" id="set">
                                <option value=""></option>
                            </select>

                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-striped dt-responsive nowrap" id="tbl_process">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Process Name</th>
                                            <th>Division Code</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbl_process_body"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-red" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-blue float-right permission-button disableOnProduction">
                        <i class="fa fa-floppy-o"></i> Save
                    </button>
                    <button type="button" class="btn bg-purple float-right" id="btn_travel_sheet_preview">
                        <i class="fa fa-eye"></i> Print Preview
                    </button>
                    {{-- <button type="button" class="btn bg-red float-right" id="btn_cancel_travel_sheet">
                        <i class="fa fa-times"></i> Cancel Travel Sheet
                    </button> --}}
                </div>

            </div>
        </form>
    </div>
</div>
