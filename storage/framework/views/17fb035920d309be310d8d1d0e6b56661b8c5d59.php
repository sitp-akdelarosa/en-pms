<?php $__env->startSection('title'); ?>
	Operator Master
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<section class="content-header">
    <h1>Operator Master</h1>
</section>

<section class="content">
	<div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
            <div class="box">
                <div class="box-body">

                    <form method="post" action="<?php echo e(url('/masters/operator-master/save')); ?>" id="frm_operator">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" id="id" name="id" class="clear">

                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Operator's ID:</span>
                                    </div>
                                    <input type="text" class="form-control input-sm validate clear" name="operator_id" id="operator_id">
                                    <div id="operator_id_feedback"></div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">First Name:</span>
                                    </div>
                                    <input type="text" class="form-control input-sm validate clear" name="firstname" id="firstname">
                                    <div id="firstname_feedback"></div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Last Name:</span>
                                    </div>
                                    <input type="text" class="form-control input-sm validate clear" name="lastname" id="lastname">
                                    <div id="lastname_feedback"></div>
                                </div>
                            </div>
                        </div>

                        <hr />

                        <div class="row justify-content-center">
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4">
                                <button type="submit" class="btn btn-block bg-blue permission-button" id="btn_save">
                                    <i class="fa fa-floppy-o"></i> Save
                                </button>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4">
                                <button type="button" class="btn btn-block bg-grey permission-button" id="btn_clear">
                                    <i class="fa fa-refresh"></i> Clear
                                </button>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4">
                                <button type="button" class="btn btn-block bg-red permission-button" id="btn_delete">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                            <div class="table-responsive">
                                <table class="table table-sm table-striped dt-responsive nowrap" id="tbl_operator" style="width:100%">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th width="5%">
                                                <input type="checkbox" class="table-checkbox check_all">
                                            </th>
                                            <th width="5%"></th>
                                            <th>Operator's ID</th>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Date Created</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbl_operator_body"></tbody>
                                </table>
                            </div>

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
        var code_permission = "M0006";
        var getOutputsURL = "<?php echo e(url('/masters/operator-master/get-output')); ?>";
        var deleteOM = "<?php echo e(url('/masters/operator-master/destroy')); ?>";
	</script>
	<script type="text/javascript" src="<?php echo e(asset('/js/pages/ppc/masters/operator-master/operator-master.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>