<?php $__env->startSection('content'); ?> 
<?php
 $exist = 0;
foreach ($user_accesses as $user_access){
        if($user_access->code == "T0005" ){
            $exist++;
        }
}
    if($exist == 0){
        echo  '<script>window.history.back()</script>';
        exit;
    }
?>
<section class="content-header">
    <h1>Cutting Schedule</h1>
</section>

<section class="content">
    <div class="box">
        <div class="box-body">

        	<div class="row">
        		<div class="col-md-5">

        			<div class="form-group row">
                        <div class="col-md-12">
                            <div class="input-group input-group-sm mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Withdrawal Slip # / J.O. #:</span>
                                </div>
                                <input type="text" class="form-control validate" name="trans_no" id="trans_no" maxlength="16">
                                <div id="trans_no_feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-sm table-bordered dt-responsive nowrap mb-5" id="tbl_cut_sched">
                                <thead>
                                    <th>
                                    	<input type="checkbox" class="table-checkbox check_all_items">
                                    </th>
                                    <th>Alloy</th>
                                    <th>Size</th>
                                    <th>Item</th>
                                    <th>Class</th>
                                    <th>SC No.</th>
                                    <th>Order Qty.</th>
                                    <th>Qty. Needed</th>
                                    <th>Qty. Cut</th>
                                    <th>Material Description</th>
                                </thead>
                                <tbody id="tbl_cut_sched_body">
                                	<tr>
                                		<td colspan="11" class="text-center">No data displayed.</td>
                                	</tr>
                                </tbody>
                            </table>

                            <hr>

                        </div>
                    </div>

                    <div class="row justify-content-center">
                    	<div class="col-md-3 col-xs-12 col-sm-10">
                    		<button class="btn btn-sm btn-block bg-blue" id="btn_add">
                    			<i class="fa fa-plus"></i> Add Details
                    		</button>
                    	</div>
                    </div>

        		</div>



        		<div class="col-md-7">
        			<div class="loadingOverlay"></div>

                    <form action="">
                    	<div class="form-group row">
	                        <div class="col-md-12">
	                            <div class="input-group input-group-sm mb-3">
	                                <div class="input-group-prepend">
	                                    <span class="input-group-text">Withdrawal Slip #:</span>
	                                </div>
	                                <input type="text" class="form-control validate" name="withdrawal_slip" id="withdrawal_slip" maxlength="16">
	                                <div id="withdrawal_slip_feedback"></div>
	                            </div>
	                        </div>
	                    </div>

	                    <div class="form-group row mb-10">
	                        <div class="col-md-6">
	                            <div class="input-group input-group-sm mb-3">
	                                <div class="input-group-prepend">
	                                    <span class="input-group-text">Date Issued:</span>
	                                </div>
	                                <input type="date" class="form-control validate" name="date_issued" id="date_issued">
	                                <div id="date_issued_feedback"></div>
	                            </div>
	                        </div>

	                        <div class="col-md-6">
	                            <div class="input-group input-group-sm mb-3">
	                                <div class="input-group-prepend">
	                                    <span class="input-group-text">Machine No.:</span>
	                                </div>
	                                <input type="text" class="form-control validate" name="machine_no" id="machine_no">
	                                <div id="machine_no_feedback"></div>
	                            </div>
	                        </div>
	                    </div>
                    

	                    <div class="row">
	                        <div class="col-md-12">
	                            <table class="table table-sm table-bordered dt-responsive nowrap mb-10" id="tbl_cut_details">
	                                <thead>
	                                    <th>No.</th>
	                                    <th>Alloy</th>
	                                    <th>Size</th>
	                                    <th>Item</th>
	                                    <th>Class</th>
	                                    <th>SC No.</th>
	                                    <th>Order Qty.</th>
	                                    <th>Qty. Needed</th>
	                                    <th>Qty. Cut</th>
	                                    <th>Material Description</th>
	                                    <th></th>
	                                </thead>
	                                <tbody id="tbl_cut_details_body">
	                                	<tr>
	                                		<td colspan="11" class="text-center">No data displayed.</td>
	                                	</tr>
	                                </tbody>
	                            </table>
	                        </div>
	                    </div>

	                    <div class="form-group row">
	                        <div class="col-md-6">
	                            <div class="input-group input-group-sm mb-3">
	                                <div class="input-group-prepend">
	                                    <span class="input-group-text">Prepared By:</span>
	                                </div>
	                                <input type="text" class="form-control validate" name="prepared_by" id="prepared_by" value="<?php echo e(Auth::user()->firstname.' '.Auth::user()->lastname); ?>">
	                                <div id="prepared_by_feedback"></div>
	                            </div>
	                        </div>

	                        <div class="col-md-6">
	                            <div class="input-group input-group-sm mb-3">
	                                <div class="input-group-prepend">
	                                    <span class="input-group-text">Leader:</span>
	                                </div>
	                                <input type="text" class="form-control validate" name="leader" id="leader">
	                                <div id="leader_feedback"></div>
	                            </div>
	                        </div>
	                    </div>

	                    <div class="form-group row">
	                        <div class="col-md-12">
	                            <div class="input-group input-group-sm mb-3">
	                                <div class="input-group-prepend">
	                                    <span class="input-group-text">ISO No. For:</span>
	                                </div>
	                                <select class="form-control select-validate" name="iso_control_no" id="iso_control_no"></select>
	                                <div id="iso_control_no_feedback"></div>
	                            </div>

	                            <hr>

	                        </div>
	                    </div>

	                    <div class="row justify-content-center">
	                    	<div class="col-md-3 col-xs-12 col-sm-10">
	                    		<button type="button" class="btn bg-purple btn-block" id="btn_print_preview">
	                    			<i class="fa fa-eye"></i> Print Preview
	                    		</button>
	                    	</div>
	                    </div>
	                </form>
        		</div>
        	</div>

        </div>
    </div>

    <?php echo $__env->make('includes.modals.system-modals', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</section>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
	<script type="text/javascript">
		var token = $('meta[name="csrf-token"]').attr('content');
        var code_permission = "T0005";
        var materialCuttingSchedURL = "<?php echo e(url('/transaction/cutting-schedule/materials')); ?>";
        var pdfCuttingScheduleURL = "<?php echo e(url('/pdf/cutting-schedule')); ?>";
	</script>
	<script type="text/javascript" src="<?php echo e(asset('/js/pages/ppc/transactions/cutting-schedule/cutting-schedule.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>