
<div id="modal_dropdown_option" class="modal fade " data-backdrop="static">
    <div class="modal-dialog" role="document">
        <form method="POST" action="<?php echo e(url('/masters/dropdown-master/save/dropdown-item')); ?>" id="frm_dropdown_items">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_dropdown_option_title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="loadingOverlay"></div>
                    <div class="form-group row">
                        <label for="dropdown_name" class="col-sm-2 control-label mt-5">Item/Option:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="new_item" id="new_item">
                            <div id="new_item_feedback"></div>
                        </div>
                        
                        <div class="col-sm-2">
                            <button type="button" class="btn bg-green float-right btn-block" id="btn_add_dropdown_item">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="dropdown_item_id" id="dropdown_item_id">
                    <input type="hidden" name="selected_dropdown_name_id" id="selected_dropdown_name_id">
                    <input type="hidden" name="selected_dropdown_name" id="selected_dropdown_name">

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-sm table-hover table-striped dt-responsive nowrap" style="width: 100%" id="tbl_item_list">
                                <thead class="thead-dark">
                                    <th>#</th>
                                    <th>Items / Options</th>
                                    <th></th>
                                </thead>
                                <tbody id="tbl_item_list_body">
                                    <tr>
                                        <td colspan="3">No data displayed.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-red" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-blue float-right permission-button">Save</button>
                </div>
                
            </div>
        </form>
    </div>
</div>