<?php $__env->startSection('title'); ?>
	Production Dashboard
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<section class="content-header">
	<h1>Dashboard</h1>
</section>
<section class="content" >
	<div class="box">
        <div class="box-body">
			<table class="table table-sm table-hover table-striped table-bordered nowrap" id="tbl_prod_dashboard" style="width: 100%">
				<thead class="thead-dark">
                    <tr>
                        <th>JO No.</th>
                        <th>Item Code</th>
                        <th>Description</th>
                        <th>Lot No.</th>
                        <th>Issued Qty.</th>
                        <th>Process</th>
                        <th>Unprocessed</th>
                        <th>Good</th>
                        <th>Rework</th>
                        <th>Scrap</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="tbl_dashboard_body"></tbody>
			</table>
		</div>
	</div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
	<script type="text/javascript">
		var token = $('meta[name="csrf-token"]').attr('content');
		var detailsURL = "<?php echo e(url('/prod/dashboard/details-list')); ?>";
		var URLprocess = "<?php echo e(url('/prod/dashboard/get_process')); ?>";
		var getDashBoardURL = "<?php echo e(url('/prod/dashboard/getDashBoardURL')); ?>";
		
	</script>
	<script src="<?php echo e(mix('/js/pages/production/dashboard/dashboard.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>