<?php $__env->startPush('styles'); ?>
    <style>
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<section class="content-header">
    <h1>Notification</h1>
</section>

<section class="content">
	<div class="row justify-content-center">
		<div class="col-lg-10 col-md-8 col-sm-12 col-xs-12">

			<div class="box">
				<div class="box-body" id="noti_items">
					
				</div>
			</div>

			<div class="row" id="timeline-loading">
                <div class="offset-md-4 col-md-3">
                    <img src="<?php echo e(asset('images/spinner.gif')); ?>" width="185px" height="60px">
                </div>
            </div>
            <div class="row" id="show-more-row">
                <div class="offset-md-4 col-md-3 text-center">
                    <button class="btn btn-outline-secondary btn-block" id="show-more">Show more</button>
                </div>
            </div>

	    </div>
	</div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script type="text/javascript">
        var token = $('meta[name="csrf-token"]').attr('content');
    </script>
    <script src="<?php echo e(mix('/js/pages/notification.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>