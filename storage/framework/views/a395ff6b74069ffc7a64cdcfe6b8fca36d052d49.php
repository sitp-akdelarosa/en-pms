<?php $__env->startSection('title'); ?>
	Production Output
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
	<style>
		.thead-dark {
			width: 100%;
			margin: 0 auto;
		}
        .dataTables_scrollHeadInner{
            width:100% !important;
        }
        .dataTables_scrollHeadInner table{
            width:100% !important;
        }
        .modal-backdrop {
            z-index: -1;
        }
	</style>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>
 <?php
$exist = 0;
foreach ($user_accesses as $user_access){
        if($user_access->code == "T0007" ){
            $exist++;
        }
}
    if($exist == 0){
        echo  '<script>window.history.back()</script>';
        exit;
    }
?>
<section class="content-header">
    <h1>Production Output</h1>
</section>

<section class="content">
	<div class="row justify-content-center">
        <div class="col-lg-6 col-md-10 col-sm-12 col-xs-12">

                <div class="box">
                    <div class="box-body">

                        <form id="frm_search_jo" role="form" method="POST" action="<?php echo e(url('/prod/production-output/search-jo')); ?>">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="id" id="id">
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">J.O. No. (Barcode):</span>
                                        </div>
                                        <input class="form-control input-sm validate clear" name="search_jo" id="search_jo">
                                        <div id="search_jo_feedback"></div>
                                        <!-- <button type="submit" class="btn btn-sm bg-blue">Search</button> -->
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <table class="table table-striped table-bordered table-sm dt-responsive" id="tbl_searched_jo" width="100%">
                <thead class="thead-dark">
                    <tr>
                        <th width="5%"></th>
                        <th>J.O. No.</th>
                        <th>Barcode</th>
                        <th>Product Code</th>
                        <th>Division</th>
                        <th>Total Qty</th>
                        <th>Process</th>
                        <th>Unprocessed</th>
                        <th>Good</th>
                        <th>Rework</th>
                        <th>Scrap</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="tbl_searched_jo_body"></tbody>
            </table>
        </div>
    </div>
</section>
<?php echo $__env->make('includes.modals.production.production-output-modal', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
    <script type="text/javascript">
        var token = $('meta[name="csrf-token"]').attr('content');
        var code_permission = 'T0007';
        var getOutputsURL = "<?php echo e(url('/prod/production-output/get-output')); ?>";
        var deleteProductonOutput = "<?php echo e(url('/prod/production-output/destroy')); ?>";
        var getOperatorURl = "<?php echo e(url('/prod/production-output/get-Operator')); ?>";    
        var checkSequence = "<?php echo e(url('/prod/production-output/check-Sequence')); ?>";
        var getTransferQtyURL = "<?php echo e(url('/prod/production-output/get-TransferQty')); ?>";
    </script>
    <script type="text/javascript" src="<?php echo e(asset('/js/pages/production/transactions/production-output/production-output.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>