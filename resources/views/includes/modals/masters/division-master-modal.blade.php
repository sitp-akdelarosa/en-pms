<div id="modal_process" class="modal fade " data-backdrop="static">
    <div class="modal-dialog modal-sm" role="document">
        <form>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Processes</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="process" class="col-sm-3 control-label mt-5">Process:</label>
                        <div class="col-sm-7">
                            <select class="form-control form-control-sm select-validate clear" id="process" name="process">
                                <option value=""></option>
                            </select>
                            <div id="process_feedback"></div>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-sm btn-block bg-green" id="btn_add_process">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-sm table-striped table-bordered dt-responsive nowrap" id="tbl_process" style="width:100%">
                                <thead class="thead-dark">
                                    <th>#</th>
                                    <th>Process</th>
                                    <th></th>
                                </thead>
                                <tbody id="tbl_process_body">
                                    <tr>
                                        <td colspan="3">No data displayed.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn bg-red" data-dismiss="modal">Close</button> --}}
                    <button type="button" class="btn bg-blue float-right" data-dismiss="modal">
                        <i class="fa fa-floppy-o"></i> Save
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="modal_prodline" class="modal fade " data-backdrop="static">
    <div class="modal-dialog" role="document">
        <form>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Product Lines</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="prod_lines" class="col-sm-3 control-label mt-5">Product Line:</label>
                        <div class="col-sm-7">
                            <select class="form-control form-control-sm select-validate clear" id="prod_lines" name="prod_lines">
                                <option value=""></option>
                            </select>
                            <div id="prod_lines_feedback"></div>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-sm btn-block bg-green" id="btn_add_prod_lines">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-sm table-striped dt-responsive table-bordered nowrap" id="tbl_prodlines" style="width:100%">
                                <thead class="thead-dark">
                                    <th>#</th>
                                    <th>Product Lines</th>
                                    <th></th>
                                </thead>
                                <tbody id="tbl_prodlines_body">
                                     <td colspan="3">No data displayed.</td>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn bg-red" data-dismiss="modal">Close</button> --}}
                    <button type="button" class="btn bg-blue float-right" data-dismiss="modal">
                        <i class="fa fa-floppy-o"></i> Save
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>