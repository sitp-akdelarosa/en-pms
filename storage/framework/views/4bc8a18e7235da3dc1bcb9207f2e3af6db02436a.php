<?php $__env->startPush('styles'); ?>
    <style>
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<section class="content-header">
    <h1>Transfer Items for Approval</h1>
</section>

<section class="content">
	<div class="row justify-content-center">
		<div class="col-lg-10 col-md-8 col-sm-12 col-xs-12">

			<div class="row" id="timeline-loading">
		        <div class="offset-md-4 col-md-3">
		            <img src="<?php echo e(asset('images/spinner.gif')); ?>" width="185px" height="60px">
		        </div>
		    </div>


			<div class="box">
				<div class="box-body" id="transfer_item_approval"></div>
			</div>

	    </div>
	</div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script type="text/javascript">
        var token = $('meta[name="csrf-token"]').attr('content');
        var getTransferItemsURL = "<?php echo e(url('/for-approval/transfer-items')); ?>";
        var answerRequestURL = "<?php echo e(url('/for-approval/answer')); ?>";
    </script>
    <script src="<?php echo e(mix('/js/pages/ppc/for-approval.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>