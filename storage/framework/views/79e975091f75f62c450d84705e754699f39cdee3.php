<?php $__env->startSection('title'); ?>
	Transfer Item
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
 <?php
 $exist = 0;
foreach ($user_accesses as $user_access){
		if($user_access->code == "T0008" ){
			$exist++;
		}
}
	if($exist == 0){
		echo  '<script>window.history.back()</script>';
		exit;
	}
?>
<section class="content-header">
	<h1>Transfer Item</h1>
</section>

<section class="content">
	<div class="nav-tabs-custom">
		<ul class="nav nav-tabs">
			<li><a class="active" href="#transfer_entry" data-toggle="tab">Transfer Entry</a></li>
			<li><a href="#received_items" data-toggle="tab">Receive Items</a></li>
		</ul>
		<div class="tab-content">

			<div class="tab-pane active" id="transfer_entry">

				<div class="row justify-content-center mb-10">
					<div class="col-md-2">
						<button type="submit" class="btn btn-sm btn-block bg-green"  id="btn_add">
							<i class="fa fa-plus"></i> Add
						</button>
					</div>
					<div class="col-md-2">
						 <button type="submit" class="btn btn-sm btn-block bg-red permission-button"  id="btn_delete_set">
							<i class="fa fa-trash"></i> Delete
						</button>
					</div>
				</div>

				<table class="table table-sm table-bordered table-hover table-striped dt-responsive nowrap" id="tbl_transfer_entry" style="width: 100%">
					<thead class="thead-dark">
						<tr>
							<th width="5%">
								<input type="checkbox" class="table-checkbox check_all_transfer_item">
							</th>
							<th width="5%"></th>
							<th>Job Order No.</th>
							<th>Product Code</th>
							<th>Process</th>
							<th>Transfer To</th>
							<th>To Process</th>
							<th>Qty.</th>
							<th>Status</th>
							<th>Remarks</th>
							<th>Date</th>
							<th>Item Status</th>
						</tr>
					</thead>
					<tbody id="tbl_transfer_entry_body"></tbody>
				</table>
				
			</div>


			<div class="tab-pane" id="received_items">
				<table class="table table-bordered table-sm table-hover table-striped dt-responsive nowrap" id="tbl_received_items" style="width: 100%">
					<thead class="thead-dark">
						<tr>
							 <th width="5%">
								<input type="checkbox" class="table-checkbox check_all_receive_item">
							</th>
							<th width="5%"></th>
							<th>Job Order No.</th>
							<th>Product Code</th>
							<th>From Division Code</th>
							<th>From process</th>
							<th>To Division Code</th>
							<th>To Process</th>
							<th>Qty.</th>
							<th>Status</th>
							<th>Remarks</th>
							<th>Date</th>
							
						</tr>
					</thead>
					<tbody id="tbl_received_items_body"></tbody>
				</table>
			</div>
		</div>
	</div>
</section>
<?php echo $__env->make('includes.modals.production.transfer-items-modal', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
	<script type="text/javascript">
		var token = $('meta[name="csrf-token"]').attr('content');
		var code_permission = 'T0008';
		var userDivCode = "<?php echo e(Auth::user()->div_code); ?>";
		var getJOdetailsURL = "<?php echo e(url('/prod/transfer-item/get-jo')); ?>";
		var getTransferEntryURL = "<?php echo e(url('/prod/transfer-item/get-transfer-entry')); ?>";

		var transfer_entry = "<?php echo e(url('/prod/transfer-item/get-output')); ?>";
		var getReceiveItemsURL = "<?php echo e(url('/prod/transfer-item/received_items')); ?>";
		var deleteTransferItem = "<?php echo e(url('/prod/transfer-item/destroy')); ?>";

		var getDivisionCode = "<?php echo e(url('/prod/transfer-item/getDivisionCode')); ?>";
		var getDivCodeProcessURL = "<?php echo e(url('/prod/transfer-item/div-code-process')); ?>";
		var unprocessedItem = "<?php echo e(url('/prod/transfer-item/get-unprocessed')); ?>";


	</script>
	<script type="text/javascript" src="<?php echo e(asset('/js/pages/production/transactions/transfer-items/transfer-items.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>