<?php $__env->startSection('content'); ?>
 <?php
$exist = 0;
foreach ($user_accesses as $user_access){
        if($user_access->code == "A0003" ){
            $exist++;
        }
}
    if($exist == 0){
        echo  '<script>window.history.back()</script>';
        exit;
    }
?>
<section class="content-header">
    <h1>User Type</h1>
</section>

<section class="content">
	<div class="row justify-content-center">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

                <div class="box">
                    <div class="box-body">
                        <form role="form" action="<?php echo e(route('admin.user-type.save')); ?>" method="post" id="frm_user_type">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" id="id" name="id" class="clear">

                            <div class="form-group row mb-10">
                                <label for="description" class="col-sm-3 control-label">User Type:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control form-control-sm clear validate" name="description" id="description">
                                    <div id="description_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row mb-10">
                                <label for="description" class="col-sm-3 control-label">Category:</label>
                                <div class="col-sm-9">
                                    <select class="form-control form-control-sm clear select-validate" name="category" id="category">
                                        <option value=""></option>
                                        <option value="OFFICE">OFFICE</option>
                                        <option value="PRODUCTION">PRODUCTION</option>
                                        <option value="ALL">ALL</option>
                                    </select>
                                    <div id="category_feedback"></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped" id="tbl_type" style="width:100%">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th width="5%">
                                                        <input type="checkbox" class="table-checkbox check_all">
                                                    </th>
                                                    <th width="5%"></th>
                                                    <th width="45%">User Type</th>
                                                    <th width="45%">Category</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbl_type_body"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-sm bg-blue permission-button" id="btn_save">
                                        <i class="fa fa-floppy-o"></i> Save
                                    </button>
                                    <button type="button" class="btn btn-sm bg-red permission-button" id="btn_delete">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </form>

                            

                    </div>
                </div>

        </div>

    </div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script type="text/javascript">
        var token = $('meta[name="csrf-token"]').attr('content');
        var typeDeleteURL = "<?php echo e(url('/admin/user-type/destroy')); ?>";
        var typeListURL = "<?php echo e(url('/admin/user-type/list')); ?>";
        var code_permission = "A0003";
    </script>
    <script type="text/javascript" src="<?php echo e(mix('/js/pages/admin/user-type/user-type.js')); ?>"></script>
<?php $__env->stopPush(); ?>



<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>