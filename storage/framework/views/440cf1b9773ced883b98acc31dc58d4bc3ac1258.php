<?php $__env->startSection('content'); ?>
 <?php
 $exist = 0;
foreach ($user_accesses as $user_access){
        if($user_access->code == "T0002" ){
            $exist++;
        }
}
    if($exist == 0){
        echo  '<script>window.history.back()</script>';
        exit;
    }
?>
<section class="content-header">
    <h1>Upload Orders</h1>
</section>

<section class="content">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-sm-12 col-xs-12">

            <form id="frm_upload_order" role="form" method="post" files="true" enctype="multipart/form-data" action="<?php echo e(url('/transaction/upload-orders/upload-up')); ?>" >
                <?php echo csrf_field(); ?>
                <div class="form-group row">
                    <div class="custom-file col-md-9 mb-3" id="customFile" lang="es">
                        <input type="file" class="custom-file-input" name="fileupload" id="fileupload" aria-describedby="fileHelp">
                        <label class="custom-file-label" for="exampleInputFile" id="filenamess">
                           Select file...
                        </label>
                    </div>

                    <div class="col-md-3 mb-3">
                        <button class="btn btn-block btn-lg bg-blue permission-button">
                            <i class="fa fa-upload"></i> Upload
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
            <div class="box">
                <div class="box-body">
                    <div class="loadingOverlay"></div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped dt-responsive nowrap mb-10" id="tbl_Upload" style="width: 100%">
                            <thead class="thead-dark">
                                <tr>
                                    
                                    <th>SC #</th>
                                    <th>Product Code</th>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>PO Number</th>
                                    <th>Date Uploaded</th>
                                </tr>
                            </thead>
                            <tbody id="tbl_Uploadbody">
                                
                            </tbody>
                        </table>
                    </div>

                    <div class="row justify-content-center">
                      
                       

                        
                    </div>
                </div>
            </div>
        </div>
    </div>


</section>
<?php echo $__env->make('includes.modals.transactions.upload-orders-modal', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script type="text/javascript">
        var token = $('meta[name="csrf-token"]').attr('content');
        var checkfile = "<?php echo e(url('/transaction/upload-orders/CheckFile')); ?>";
        var datatableUpload = "<?php echo e(url('/transaction/upload-orders/DatatableUpload')); ?>";
        var deleteselected = "<?php echo e(url('/transaction/upload-orders/deletefromtemp')); ?>";
        var overwriteURL = "<?php echo e(url('/transaction/upload-orders/overwrite')); ?>";
        var code_permission = 'T0002';
    </script>
    <script type="text/javascript" src="<?php echo e(mix('/js/pages/ppc/transactions/upload-orders/upload-orders.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>