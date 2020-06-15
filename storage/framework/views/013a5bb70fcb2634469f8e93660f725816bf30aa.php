<?php $__env->startSection('content'); ?>
 <?php
 $exist = 0;
foreach ($user_accesses as $user_access){
        if($user_access->code == "M0005" ){
            $exist++;
        }
}
    if($exist == 0){
        echo  '<script>window.history.back()</script>';
        exit;
    }
?>
<section class="content-header">
    <h1>Process Master</h1>
</section>

<section class="content">
	<div class="row">

		<div class="col-lg-6 col-md-10 col-sm-12 col-xs-12">
            <div class="box">
                <div class="box-body">
                	<div class="row mb-10">
                		<div class="col-md-12">
                			<div class="loadingOverlay"></div>
                			<h4>Add a Set</h4>
                			<form method="post" action="<?php echo e(url('/masters/process-master/save-set')); ?>" id="frm_add_set">
                				<?php echo csrf_field(); ?>
                				<div class="form-group row">
	                				<label for="" class="control-label col-sm-2 mt-5">Set:</label>
	                                <div class="col-sm-8">
	                                    <input type="text" class="form-control validate" id="set" name="set">
	                                    <div id="set_feedback"></div>
	                                </div>
	                                <div class="col-sm-2">
	                                    <button type="submit" class="btn btn-block bg-green">
	                                    	<i class="fa fa-plus"></i>
	                                    </button>
	                                </div>
	                			</div>
                			</form>
                			<hr>
                			<div class="row mb-10">
                				<div class="col-md-12" id="set_list" style="width: 100%;"></div>
                			</div>
                			<div class="row">
                				<div class="col-md-12">
                					<button type="button" class="btn btn-block btn-sm bg-red permission-button" id="btn_delete_set">
	                					<i class="fa fa-trash"></i> Delete Set
	                				</button>
                				</div>
                			</div>
                			<hr>
                			<h4>Choose Processes</h4>

		                	<table class="table table-sm table-hover table-striped dt-responsive nowrap" id="tbl_select_process" style="width:100%">
		                		<thead class="thead-dark">
		                			<th width="10%">
		                				<input type="checkbox" class="table-checkbox check_all">
		                			</th>
		                			<th width="90%">Process</th>
		                		</thead>
		                		<tbody id="tbl_select_process_body"></tbody>
		                	</table>
                		</div>
                	</div>
                	<div class="row">
                		<div class="col-md-12">
                			<button type="button" class="btn btn-sm btn-block bg-green" id="btn_add_process">
                				<i class="fa fa-plus"></i> Add to Set
                			</button>
                		</div>
                	</div>
	                	
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-10 col-sm-12 col-xs-12">
            <div class="box">

                <div class="box-body">
                	<div class="loadingOverlay"></div>
                	<h4>Selected Process</h4>

                    <div class="row mb-5">
                        <div class="col-sm-12">
                        	<div class="form-group row">
                                <label for="" class="control-label col-sm-2 mt-5">Set:</label>
                                <div class="col-sm-10">
                                    <select class="form-control select-validate" id="selected_set" name="selected_set"></select>
                                    <div id="selected_set_feedback"></div>
                                </div>
                            </div>

                        </div>
                    </div>


                    <div class="row mb-10">
                        <div class="col-sm-12">
                            <div class="table-reponsive">
                                <table class="table table-striped table-sm mb-10 table-hover" id="tbl_selected_process" style="width:100%">
                                    <thead class="thead-dark">
                                        <tr>
                                        	<th width="10%"></th>
                                            <th width="10%">#</th>
                                            <th width="70%">Process Name</th>
                                            <th width="10%"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbl_selected_process_body">
                                        <tr>
                                            <td colspan="4" class="text-center">No data available.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-sm-3">
                            <button type="submit" id="btn_save_process" class="btn bg-blue btn-block permission-button">
                                <i class="fa fa-floppy-o"></i> Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
	<script type="text/javascript">
		var token = $('meta[name="csrf-token"]').attr('content');
		var processListURL = "<?php echo e(url('masters/process-master/process-list')); ?>";
		var saveProcessURL = "<?php echo e(url('masters/process-master/save')); ?>";
		var selectedProcessListURL = "<?php echo e(url('masters/process-master/selected-process-list')); ?>";
		var getSetURL = "<?php echo e(url('masters/process-master/get-set')); ?>";
		var deleteSetURL = "<?php echo e(url('masters/process-master/delete-set')); ?>";

        var code_permission = "M0005";
	</script>
	<script type="text/javascript" src="<?php echo e(asset('/js/pages/ppc/masters/process-master/process-master.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>