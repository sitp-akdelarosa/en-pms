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
                </div>
                        
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