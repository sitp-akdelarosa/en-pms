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
                            <th width="6.66%"></th>
                            <th width="6.66%">Item Class</th>
                            <th width="6.66%">J.O. No.</th>
                            <th width="6.66%">Item Code</th>
                            <th width="6.66%">description</th>
                            <th width="6.66%">Product Line</th>
                            <th width="6.66%">Lot #</th>
                            <th width="6.66%">Heat #</th>
                            <th width="6.66%">Qty(KGS)</th>
                            <th width="6.66%">Qty(PCS)</th>
                            <th width="6.66%">Current Stock</th>
                            <th width="6.66%">Alloy</th>
                            <th width="6.66%">Item</th>
                            <th width="6.66%">Size</th>
                            <th width="6.66%">Schedule/Class</th>
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