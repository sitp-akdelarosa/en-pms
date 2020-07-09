<?php $__env->startPush('styles'); ?>
    <style>
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
 <?php
$exist = 0;
foreach ($user_accesses as $user_access){
        if($user_access->code == "A0005" ){
            $exist++;
        }
}
    if($exist == 0){
        echo  '<script>window.history.back()</script>';
        exit;
    }
?>
<section class="content-header">
    <h1>Settings</h1>
</section>

<section class="content">

	<div class="box">
		<div class="box-body">

            <div class="row">

                <div class="col-md-6">
                    <form role="form" method="POST" enctype="multipart/form-data" action="<?php echo e(url('/admin/settings/save')); ?>" id="frm_iso">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" id="id" name="id" class="clear">

                        <div class="form-group row justify-content-center">
                            <div class="col-sm-5">
                                <img src="<?php echo e(asset('images/default_upload_photo.jpg')); ?>" height="80px" width="200px" class="photo" id="whs_photo">
                            </div>
                        </div>

                        <div class="form-group row justify-content-center mb-5">
                            <div class="col-sm-5">
                                <div class="custom-file mb-3" id="customFile" lang="es">
                                    <input type="file" class="custom-file-input validate clear" id="photo" name="photo" aria-describedby="fileHelp">
                                    <label class="custom-file-label" id="photo_label" for="photo">Select a photo...</label>
                                    <div id="photo_feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Name:</span>
                                    </div>
                                    <input type="text" class="form-control input-sm validate clear" name="iso_name" id="iso_name">
                                    <div id="iso_name_feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">ISO Code:</span>
                                    </div>
                                    <input type="text" class="form-control input-sm validate clear" name="iso_code" id="iso_code">
                                    <div id="iso_code_feedback"></div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group row">
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-block bg-blue permission-button" id="btn_save">
                                    <i class="fa fa-floppy-o"></i> Save
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-block bg-grey permission-button" id="btn_clear">
                                    <i class="fa fa-refresh"></i> Clear
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-block bg-red permission-button" id="btn_delete">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-md-6">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped dt-responsive nowrap" id="tbl_iso" style="width:100%">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="5%">
                                        <input type="checkbox" class="table-checkbox check_all">
                                    </th>
                                    <th width="5%"></th>
                                    <th>Name</th>
                                    <th>ISO Code</th>
                                </tr>
                            </thead>
                            <tbody id="tbl_iso_body"></tbody>
                        </table>
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
        var getISOTable = "<?php echo e(url('/admin/settings/getISOTable')); ?>";
        var deleteISO = "<?php echo e(url('/admin/settings/destroy')); ?>";
        var defultphoto = "<?php echo e(asset('images/default_upload_photo.jpg')); ?>";
        var code_permission = 'A0005';
    </script>
    <script src="<?php echo e(mix('/js/pages/admin/settings/settings.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>