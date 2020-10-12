<div id="modal_upload_orders" class="modal fade " data-backdrop="static">
    <div class="modal-dialog" role="document">
        <form>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Do you want to overwrite from Production Schedule?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <p id="overwrite_msg"></p>
                    <table class="table table-sm table-striped dt-responsive nowrap" id="tbl_overwrite" style="width:100%">
                        <thead class="thead-dark">
                            <th></th>
                            <th>SC#</th>
                            <th>Product Code</th>
                            <th>Current Quantity</th>
                            <th>New Quantity</th>
                            <th>P.O. No.</th>
                            <th style="display:none;">ID</th>
                        </thead>
                        <tbody id="tbl_overwrite_body">
                            <tr>
                                <td colspan="5">No data displayed.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-red" data-dismiss="modal">
                        <i class="fa fa-thumbs-down"></i> No
                    </button>
                    
                    <button type="button" class="btn bg-blue float-right" id="btn_yes">
                         <i class="fa fa-thumbs-up"></i> Yes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>


<div id="modal_not_registered" class="modal fade " data-backdrop="static">
    <div class="modal-dialog" role="document">
        <form>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">These products are not registered in Product Master</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <p>Most of data are uploaded but these products are not registered in Product Master.</p>
                    <br>
                    <table class="table table-sm table-striped dt-responsive nowrap" id="tbl_not_registered" style="width:100%">
                        <thead class="thead-dark">
                            <th>SC#</th>
                            <th>Product Code</th>
                            <th>Quantity</th>
                            <th>P.O. No.</th>
                        </thead>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-red" data-dismiss="modal">Close</button>
                    <button type="button" class="btn bg-green float-right permission-button" id="btn_excel">
                         <i class="fa fa-download"></i> Download excel file
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="modal_Schedule" class="modal fade " data-backdrop="static">
    <div class="modal-dialog" role="document">
        <form>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">These products are already have a pending production schedule</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <p>Most of data are uploaded but these products are have a pending production schedule.</p>
                    <br>
                    <table class="table table-sm table-striped dt-responsive nowrap" id="tbl_Schedule" style="width:100%">
                        <thead class="thead-dark">
                            <th>SC#</th>
                            <th>Product Code</th>
                            <th>Quantity</th>
                            <th>P.O. No.</th>
                        </thead>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-red" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>