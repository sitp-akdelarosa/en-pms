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
                            <input type="date" class="form-control input-sm validate" name="date" id="date" value="<?php echo e(date('Y-m-d')); ?>">
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
                            value="<?php echo e(Auth::user()->firstname); ?> <?php echo e(Auth::user()->lastname); ?>">
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

                <input type="hidden" name="print_format" id="print_format" value="material_withdrawal">

                
                        
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