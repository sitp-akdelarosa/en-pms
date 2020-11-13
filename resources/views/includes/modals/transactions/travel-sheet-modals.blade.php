<div id="modal_travel_sheet" class="modal fade " data-backdrop="static">
    <div class="modal-dialog modal-full" role="document">
        <form method="POST" action="{{ url('/transaction/travel-sheet/set-up/save') }}" id="frm_travel_sheet">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Set Up Travel Sheet</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="loadingOverlay-modal"></div>
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
                                <div class="col-md-6">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">ISO Control No.:</span>
                                        </div>
                                        <select class="form-control select-validate disableOnProduction" name="iso_no" id="iso_no"></select>
                                        <div id="iso_no_feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Est. Ship Date:</span>
                                        </div>
                                        <input type="date" class="form-control validate disableOnProduction" name="ship_date" id="ship_date" />
                                        <div id="ship_date_feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Issued Qty</span>
                                        </div>
                                        <input type="number" class="form-control disableOnProduction" name="issued_qty" id="issued_qty">
                                        <div id="issued_qty_feedback"></div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Total Issued Qty</span>
                                        </div>
                                        <input type="number" class="form-control disableOnProduction" name="issued_qty_table" id="issued_qty_table" readonly>
                                        <div id="issued_qty_table_feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <div class="col-sm-6">
                                    <textarea name="ts_remarks" id="ts_remarks" class="form-control form-control-sm" style="resize:none;height:60px" placeholder="Add remarks here"></textarea>
                                </div>

                                <div class="col-sm-6">
                                    <div class="row mb-3">
                                        <div class="col-sm-12">
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Qty. per sheet</span>
                                                </div>
                                                <input type="number" class="form-control disableOnProduction" name="qty_per_sheet" id="qty_per_sheet">
                                                <div id="qty_per_sheet_feedback"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <button type="button" class="btn btn-block bg-green mb-3 disableOnProduction" id="btn_add_prod">
                                                <i class="fa fa-plus"></i> Add
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover table-bordered table-striped dt-responsive nowrap" id="tbl_product" style="width: 100%">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th width="20%">Product Code</th>
                                                    <th width="10%">Issued Qty</th>
                                                    <th width="20%">SC. Qty</th>
                                                    <th width="40%">SC No.</th>
                                                    <th width="10%"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbl_product_body"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-sm mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Process Set</span>
                                </div>

                                <input type="hidden" name="process_id" id="process_id">
                                <input type="hidden" name="prod_id" id="prod_id">

                                <select class="form-control form-control-sm disableOnProduction" name="set" id="set">
                                    <option value=""></option>
                                </select>
                            </div>

                            <div class="input-group mb-3 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Process:</span>
                                </div>
                                <select class="form-control select-validate" id="process"></select>
                                <div id="process_feedback"></div>
                            </div>

                            <div class="input-group mb-3 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Sequence No.:</span>
                                </div>
                                <input type="number" class="form-control select-validate" id="sequence" min="1">
                                <div id="sequence_feedback"></div>
                            </div>

                            <div class="row justify-content-center">
                                <div class="col-sm-3 col-sm-3 mb-3" id="add_process">
                                    <button type="button" id="btn_add_process" class="btn btn-sm bg-green btn-block">
                                        <i class="fa fa-plus"></i> Add Process
                                    </button>
                                </div>
                                <div class="col-sm-3 col-sm-3 mb-3" id="cancel_process">
                                    <button type="button" id="btn_cancel_process" class="btn btn-sm bg-red btn-block">
                                        <i class="fa fa-times"></i> Cancel
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-bordered table-striped dt-responsive nowrap" id="tbl_process">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Process Name</th>
                                            <th>Remarks</th>
                                            <th>Div. Code</th>
                                            <th></th>
                                            <th></th>
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
