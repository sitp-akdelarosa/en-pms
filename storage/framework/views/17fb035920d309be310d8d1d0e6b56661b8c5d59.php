<?php $__env->startSection('title'); ?>
	Operator Master
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<section class="content-header">
    <h1>Operator Master</h1>
</section>

<section class="content">
	<div class="row justify-content-center">
        <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">

            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-5">
                            <form method="post" action="<?php echo e(url('/masters/operator-master/save')); ?>" id="frm_operator">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" id="id" name="id" class="clear">

                                <div class="form-group row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Operator's ID:</span>
                                            </div>
                                            <input type="text" class="form-control input-sm validate clear readonly_op switch" name="operator_id" id="operator_id">
                                            <div id="operator_id_feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">First Name:</span>
                                            </div>
                                            <input type="text" class="form-control input-sm validate clear readonly_op switch" name="firstname" id="firstname">
                                            <div id="firstname_feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Last Name:</span>
                                            </div>
                                            <input type="text" class="form-control input-sm validate clear readonly_op switch" name="lastname" id="lastname">
                                            <div id="lastname_feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <hr />

                                <div class="row justify-content-center">
                                    <div class="col-lg-3 col-md-3 col-sm-2 col-xs-2" id="div_add">
                                        <button type="button" class="btn btn-block btn-sm bg-green permission-button switch" id="btn_add">
                                            <i class="fa fa-plus"></i> Add New
                                        </button>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-2 col-xs-2" id="div_save">
                                        <button type="submit" class="btn btn-block btn-sm bg-blue permission-button switch" id="btn_save">
                                            <i class="fa fa-floppy-o"></i> Save
                                        </button>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-2 col-xs-2" id="div_clear">
                                        <button type="button" class="btn btn-block btn-sm bg-grey permission-button switch" id="btn_clear">
                                            <i class="fa fa-refresh"></i> Clear
                                        </button>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-2 col-xs-2" id="div_cancel">
                                        <button type="button" class="btn btn-block btn-sm bg-red permission-button switch" id="btn_cancel">
                                            <i class="fa fa-times"></i> Cancel
                                        </button>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-2 col-xs-2" id="div_delete">
                                        <button type="button" class="btn btn-block btn-sm bg-red permission-button switch" id="btn_delete">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="col-md-7">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-bordered dt-responsive nowrap" id="tbl_operator" style="width:100%">
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
                                            <th></th>
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
        var getOperatorsURL = "<?php echo e(url('/masters/operator-master/get-operators')); ?>";
        var deleteOM = "<?php echo e(url('/masters/operator-master/destroy')); ?>";
        var disabledURL = "<?php echo e(url('/masters/operator-master/enable-disabled-operator')); ?>";
	</script>
	<script type="text/javascript" src="<?php echo e(asset('/js/pages/ppc/masters/operator-master/operator-master.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>