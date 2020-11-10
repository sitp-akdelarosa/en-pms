<div id="modal_download_excel" class="modal fade " tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Download Material Codes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="loadingOverlay-modal"></div>
                <div class="row mb-5">
                    <div class="col-sm-12">
                        <select class="form-control select-validate" id="mat_types" name="mat_types[]" multiple>

                        </select>
                        <div id="mat_types_feedback">Reminder: Select your desired Material Types to download. If all Material Types, leave it empty.</div>
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