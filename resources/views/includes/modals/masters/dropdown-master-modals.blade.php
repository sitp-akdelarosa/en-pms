{{-- <div id="modal_dropdown_name" class="modal fade " data-backdrop="static">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ url('/masters/dropdown-master/save/dropdown-name') }}" id="frm_dropdown_name">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Dropdown Name</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="dropdown_name" class="col-sm-3 control-label mt-5">Dropdown Name:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="dropdown_name" id="dropdown_name">
                            <div id="dropdown_name_feedback"></div>
                        </div>
                        @csrf
                        <input type="hidden" name="dropdown_name_id" id="dropdown_name_id">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-red" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-green float-right">Save</button>
                </div>
                
            </div>
        </form>
    </div>
</div>
 --}}
<div id="modal_dropdown_option" class="modal fade " data-backdrop="static">
    <div class="modal-dialog" role="document">
        
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_dropdown_option_title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="loadingOverlay"></div>

                <form method="POST" id="frm_dropdown_items_value">
                    <div class="form-group row">
                        <div class="col-md-9 col-sm-7 col-xs-7">
                            <div class="input-group mb-3 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Item/Option:</span>
                                </div>
                                <input type="text" class="form-control" name="new_item" id="new_item" autocomplete="false">
                                <div id="new_item_feedback"></div>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-5 col-xs-5">
                            <button type="button" class="btn bg-green float-right btn-block" id="btn_add_dropdown_item">
                                <i class="fa fa-plus"></i> Add Option
                            </button>
                        </div>
                    </div>
                    
                </form>

                {{-- <div class="form-group row">
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
                </div> --}}
                <form method="POST" action="{{ url('/masters/dropdown-master/save/dropdown-item') }}" id="frm_dropdown_items">
                    @csrf
                    <input type="hidden" name="dropdown_item_id" id="dropdown_item_id">
                    <input type="hidden" name="selected_dropdown_name_id" id="selected_dropdown_name_id">
                    <input type="hidden" name="selected_dropdown_name" id="selected_dropdown_name">

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-sm table-hover table-striped table-bordered dt-responsive nowrap" style="width: 100%" id="tbl_item_list">
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

                    <div class="row justify-content-center">
                        <div class="col-md-2">
                            <button type="submit" class="btn bg-blue float-right permission-button">
                                <i class="fa fa-floppy-o"></i> Save
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-red pull-right" data-dismiss="modal">Close</button>
            </div>
        </div>
        
    </div>
</div>