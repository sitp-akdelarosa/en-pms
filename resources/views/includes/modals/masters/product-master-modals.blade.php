<div id="modal_process" class="modal fade " data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Product Processes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="loadingOverlay-modal"></div>
                <form method="POST" action="{{ url('/masters/product-master/code/processes/save') }}" id="frm_product_processes" class="mb-10">
                    @csrf
                    <input type="hidden" name="process_id" id="process_id">
                    <input type="hidden" name="prod_id" id="prod_id">

                    <div class="row mb-5">
                        <div class="col-sm-12">

                            <div class="input-group mb-3 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Product Code:</span>
                                </div>
                                <input type="text" class="form-control validate" id="prod_code" readonly>
                                <div id="prod_code_feedback"></div>
                            </div>

                            <div class="input-group mb-3 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Set:</span>
                                </div>
                                <select class="form-control select-validate" id="set"></select>
                                <div id="set_feedback"></div>
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

                            {{-- <div class="form-group row">
                                <label for="" class="control-label col-sm-3 mt-5">Product Code:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate" id="prod_code" readonly>
                                    <div id="prod_code_feedback"></div>
                                </div>
                            </div> --}}

                            {{-- <div class="form-group row">
                                <label for="" class="control-label col-sm-3 mt-5">Set:</label>
                                <div class="col-sm-9">
                                    <select class="form-control select-validate" id="set"></select>
                                    <div id="set_feedback"></div>
                                </div>
                            </div> --}}

                            {{-- <div class="form-group row">
                                <label for="" class="control-label col-sm-3 mt-5">Process:</label>
                                <div class="col-sm-9">
                                    <select class="form-control select-validate" id="process"></select>
                                    <div id="process_feedback"></div>
                                </div>
                            </div> --}}

                            {{-- <div class="form-group row">
                                <label for="" class="control-label col-sm-3 mt-5">Sequence No.:</label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control select-validate" id="sequence" min="1">
                                    <div id="sequence_feedback"></div>
                                </div>
                            </div> --}}

                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-sm-3 col-sm-3 mb-10">
                            <button type="button" id="btn_add_process" class="btn bg-green btn-block">
                                <i class="fa fa-plus"></i> Add Process
                            </button>

                            <button type="button" id="btn_cancel_process" class="btn bg-red btn-block">
                                <i class="fa fa-times"></i> Cancel
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-reponsive">
                                <table class="table table-striped table-bordered table-sm mb-10" id="tbl_prod_process" style="width:100%">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th>Process Name</th>
                                            <th>Remarks</th>
                                            <th width="5%"></th>
                                            <th width="5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbl_prod_process_body"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-md-2 col-sm-3">
                            <button type="submit" id="btn_save_process" class="btn bg-blue btn-block permission-button">
                                <i class="fa fa-floppy-o"></i> Save
                            </button>
                        </div>
                    </div>

                </form>
                    
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm bg-red pull-right" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<div id="modal_download_excel" class="modal fade " tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Download Product Codes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="loadingOverlay-modal"></div>
                <div class="row mb-5">
                    <div class="col-sm-12">
                        <select class="form-control select-validate" id="prod_lines" name="prod_lines[]" multiple>

                        </select>
                        <div id="prod_lines_feedback">Reminder: Select your desired Product Lines to download. If all product Lines, leave it empty.</div>
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
                <button type="button" class="btn btn-sm bg-red pull-right" data-dismiss="modal" data-toggle="popover" 
                    data-content="This Button is to close this modal." 
                    data-placement="right">Close</button>
                <button type="button" class="btn btn-sm bg-green pull-right" id="btn_download_excel" data-toggle="popover" 
                    data-content="This Button is to download the Excel file." 
                    data-placement="right">Download</button>
            </div>
        </div>
    </div>
</div>