<?php $__env->startSection('content'); ?>
 <?php
 $exist = 0;
foreach ($user_accesses as $user_access){
        if($user_access->code == "T0003" ){
            $exist++;
        }
}
    if($exist == 0){
        echo  '<script>window.history.back()</script>';
        exit;
    }
?>
<section class="content-header">
    <h1>Raw Material Withdrawal</h1>
</section>

<section class="content">
    <div class="box">
        <div class="box-body">
            <form id="frm_raw_material" role="form" method="POST" action="<?php echo e(url('/transaction/raw-material-withdrawal/save')); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id" id="id">

                <div class="row mb-10">
                	<div class="col-md-6">
                		<div class="form-group row">
		                    <label for="trans_no" class="col-sm-3 control-label mt-5">Transaction No:</label>
		                    <div class="col-sm-7">
		                        <div class="input-group input-group-sm">
		                            <input type="text" class="form-control form-control-sm validate" name="trans_no" id="trans_no">
		                            <div id="trans_no_feedback"></div>
		                            <div class="input-group-append">
		                            	<button type="button" class="btn btn-sm bg-blue" id="btn_first">
		                            		<i class="fa fa-angle-double-left"></i>
		                            	</button>
		                            	<button type="button" class="btn btn-sm bg-blue" id="btn_prev">
		                            		<i class="fa fa-angle-left"></i>
		                            	</button>
		                            	<button type="button" class="btn btn-sm bg-blue" id="btn_next">
		                            		<i class="fa fa-angle-right"></i>
		                            	</button>
		                            	<button type="button" class="btn btn-sm bg-blue" id="btn_last">
		                            		<i class="fa fa-angle-double-right"></i>
		                            	</button>
		                            	
		                            </div>
		                        </div>
		                    </div>
		                </div>
                	</div>
                </div>

                <div class="row mb-10">
                	<div class="col-md-3">
                		<input class="clear" type="hidden" name="item_id" id="item_id">
                        <input class="clear" type="hidden" name="save_issued_qty" id="save_issued_qty">
                        <div class="form-group row">
                            <label for="material_heat_no" class="col-sm-4 control-label mt-5">Material Heat No:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm validate clear input" id="material_heat_no" name="material_heat_no">
                                <div id="material_heat_no_feedback"></div>
                            </div>
                        </div>
                		<div class="form-group row">
                            <label for="mat_code" class="col-sm-4 control-label mt-5">Material Code:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm validate clear input" id="mat_code" name="mat_code" maxlength="16" readonly>
		                        <div id="mat_code_feedback"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="alloy" class="col-sm-3 control-label mt-5">Alloy:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control form-control-sm validate clear input" id="alloy" name="alloy" maxlength="20" readonly>
		                        <div id="alloy_feedback"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="item" class="col-sm-3 control-label mt-5">Item:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control form-control-sm validate clear input" id="item" name="item" maxlength="20" readonly>
		                        <div id="item_feedback"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="size" class="col-sm-3 control-label mt-5">Size:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control form-control-sm validate clear input" id="size" name="size" maxlength="10" readonly>
		                        <div id="size_feedback"></div>
                            </div>
                        </div>

                	</div>

                	<div class="col-md-3">

                        <div class="form-group row">
                            <label for="schedule" class="col-sm-3 control-label mt-5">Schedule:</label>
                            <div class="col-sm-9">
                                <input type="hidden" class="clear" id="hide_schedule" name="hide_schedule">
                                <input type="text" class="form-control form-control-sm validate clear input" id="schedule" name="schedule" maxlength="15">
		                        <div id="schedule_feedback"></div>
                            </div>
                        </div>

                		

                        
                            

                                

			            <div class="form-group row">
		                    <div class="col-sm-12">
		                        <textarea class="form-control clear input" id="remarks" style="resize: none" placeholder="Remarks"></textarea>
		                    </div>
			            </div>

                	</div>

                	<div class="col-md-3">

                		

                        <div class="form-group row">
                            <label for="issued_qty" class="col-sm-4 control-label mt-5">Current Stock:</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control form-control-sm validate clear input" id="inv_qty" maxlength="5" readonly>
                                <div id="inv_qty_feedback"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="issued_qty" class="col-sm-4 control-label mt-5">Issued Qty:</label>
                            <div class="col-sm-3">
                                <input type="number" step='1' class="form-control form-control-sm validate clear input" id="issued_qty" maxlength="5" readonly>
		                        <div id="issued_qty_feedback"></div>
                            </div>
                            <label for="issued_uom" class="col-sm-2 control-label mt-5">UoM:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control form-control-sm validate clear input" id="issued_uom" maxlength="5" readonly>
                                <div id="issued_uom_feedback"></div>
                            </div>
                        </div>

                        

                	</div>

                	<div class="col-md-3">
                		<div class="form-group row">
                            <label for="create_user" class="col-sm-3 control-label mt-5">Created By:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control form-control-sm clear" name="create_user" id="create_user" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="created_at" class="col-sm-3 control-label mt-5">Create Date:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control form-control-sm clear" name="created_at" id="created_at" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="update_user" class="col-sm-3 control-label mt-5">Updated By:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control form-control-sm clear" name="update_user" id="update_user" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="updated_at" class="col-sm-3 control-label mt-5">Update Date:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control form-control-sm clear" name="updated_at" id="updated_at" readonly>
                            </div>
                        </div>

                	</div>
                </div>

                

                <div class="form-group row justify-content-center" id="controls">
                    <div class="col-md-2 mb-5">
                        <button type="button" id="btn_add" class="btn bg-green btn-block">
                            <i class="fa fa-plus"></i> Add
                        </button>
                    </div>
                    <div class="col-md-2 mb-5">
                        <button type="button" id="btn_clear" class="btn bg-gray btn-block">
                            <i class="fa fa-refresh"></i> Clear
                        </button>
                    </div>
                </div>
	            <div class="row">
	                <div class="col-12">
	                    <table class="table table-sm table-hover table-striped dt-responsive nowrap" id="tbl_raw_material" style="width:100%">
	                        <thead class="thead-dark">
	                            <tr>
	                                <th width="5%"></th>
                                    <th width="5%"></th>
	                                <th>Material Code</th>
	                                <th>Alloy</th>
	                                <th>Item / Description</th>
	                                <th>Size / Sched</th>
	                                <th>Issued Qty</th>
	                                
	                                <th>Material Heat No.</th>
	                                
	                                <th>Remarks</th>
	                            </tr>
	                        </thead>
	                        <tbody id="tbl_raw_material_body"></tbody>
	                    </table>
	                </div>
	            </div>

            	<div class="form-group row justify-content-center">
            		<div class="col-md-2 mb-5" id="add_new">
                        <button type="button" id="btn_new" class="btn bg-green btn-block permission-button">
                            <i class="fa fa-pencil"></i> Add New
                        </button>
                    </div>
                    <div class="col-md-2 mb-5" id="edit">
                        <button type="button" id="btn_edit" class="btn bg-purple btn-block permission-button">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                    </div>
                    <div class="col-md-2 mb-5" id="save">
                        <button type="submit" id="btn_save" class="btn bg-blue btn-block permission-button">
                            <i class="fa fa-floppy-o"></i> Save
                        </button>
                    </div>
                    <div class="col-md-2 mb-5" id="delete">
                        <button type="button" id="btn_delete" class="btn bg-red btn-block permission-button">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </div>
                    <div class="col-md-2 mb-5" id="cancel">
                        <button type="button" id="btn_cancel" class="btn bg-red btn-block">
                            <i class="fa fa-times"></i> Cancel
                        </button>
                    </div>
                    <div class="col-md-2 mb-5" id="print">
                        <button type="button" id="btn_prepare_print" class="btn bg-navy btn-block">
                            <i class="fa fa-print"></i> Prepare for printing
                        </button>
                    </div>
                    
                </div>
            </form>

        </div>
    </div>
</section>
<?php echo $__env->make('includes.modals.transactions.raw-material-slip-modals', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
    <script type="text/javascript">
        var token = $('meta[name="csrf-token"]').attr('content');
        var getScNoURL = "<?php echo e(url('/transaction/raw-material-withdrawal/get-sc-no')); ?>";
        var getMaterialHeatNoURL = "<?php echo e(url('/transaction/raw-material-withdrawal/get-heat-no')); ?>";
        var deleteRawMaterial = "<?php echo e(url('/transaction/raw-material-withdrawal/destroy')); ?>";
        var searchTransNoURL = "<?php echo e(url('/transaction/raw-material-withdrawal/search-trans-no')); ?>";
        var scnosuggestURL = "<?php echo e(url('/transaction/raw-material-withdrawal/scnosuggest')); ?>";
        var getMaterialDetailsURL = "<?php echo e(url('/transaction/raw-material-withdrawal/material-details')); ?>";
        var RawMaterialWithdrawalSlipURL = "<?php echo e(url('/pdf/raw-material-withdrawal-slip')); ?>";
        var code_permission = 'T0003';
        var getComputationIssuedQty = "<?php echo e(url('/transaction/raw-material-withdrawal/getComputationIssuedQty')); ?>";
    </script>
    <script type="text/javascript" src="<?php echo e(mix('/js/pages/ppc/transactions/raw-material-withdrawal/raw-material-withdrawal.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>