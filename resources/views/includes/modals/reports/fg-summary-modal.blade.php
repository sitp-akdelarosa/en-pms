<div id="modal_fg_summary" class="modal fade " data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" action="{{ url('reports/fg-summary/save') }}" id="frm_fg_summary">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assigning SC #</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="loadingOverlay"></div>
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="row">
                        <div class="col-md-6">

                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="input-group input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Current SC No.:</span>
                                        </div>
                                        <input type='text' class="form-control" name="current_sc_no" id="current_sc_no" readonly>
                                        <div id="current_sc_no_feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Product Code</span>
                                        </div>
                                        <input type="text" class="form-control " name="prod_code" id="prod_code" readonly>
                                        <div id="prod_code_feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Description</span>
                                        </div>
                                        <input type="text" class="form-control" name="description" id="description" readonly>
                                        <div id="description_feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">SC No</span>
                                        </div>
                                        <select type="text" class="form-control select-validate" name="sc_no" id="sc_no"></select>
                                        <div id="sc_no_feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">

                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Order Qty</span>
                                        </div>
                                        <input type="text" class="form-control " name="order_qty" id="order_qty" readonly>
                                        <div id="order_qty_feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Qty</span>
                                        </div>
                                        <input type="number" class="form-control validate" name="qty" id="qty">
                                        <div id="qty_feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Order Qty of SC # Selected</span>
                                        </div>
                                        <input type="text" class="form-control" name="total_order_qty" id="total_order_qty" value="0" readonly>
                                        <div id="total_order_qty_feedback"></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-red" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-blue float-right permission-button">
                        <i class="fa fa-floppy-o"></i> Save
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
