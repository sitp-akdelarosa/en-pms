<?php $__env->startSection('content'); ?>
 <?php
$exist = 0;
foreach ($user_accesses as $user_access){
		if($user_access->code == "A0002" ){
			$exist++;
		}
}
	if($exist == 0){
		echo  '<script>window.history.back()</script>';
		exit;
	}
?>


<section class="content-header">
	<h1>Assign Product Line</h1>
</section>

<section class="content">
	<div class="row justify-content-center">
		<div class="col-lg-12">

				<div class="box">
					<div class="box-body">
						<div class="row mb-5">
							<div class="col-md-4">
								<h5>Choose User</h5>
								<table class="table table-sm table-hover table-striped dt-responsive nowrap" id="tbl_users" style="width:100%">
									<thead class="thead-dark">
										<tr>
											<th width="5%">
												<input type="checkbox" class="table-checkbox check_all_users">
											</th>
											<th width="40%">User ID</th>
											<th width="50%">Name</th>
											<th width="5%"></th>
										</tr>
									</thead>
									<tbody id="tbl_users_body"></tbody>
								</table>

								
							</div>

							<div class="col-md-3">
								<h5>Assign user to Product Line</h5>
								<table class="table table-striped table-sm dt-responsive" id="tbl_productline" style="width:100%">
									<thead class="thead-dark">
										<tr>
											<th>
												<input type="checkbox" class="table-checkbox check_all_prods">
											</th>
											<th>Product Line</th>
										</tr>
									</thead>
									<tbody id="tbl_productline_body"></tbody>
								</table>
							</div>

							<div class="col-md-5">
								<h5>Assiged Product Line</h5>
								<table class="table table-striped table-sm dt-responsive" id="tbl_assign_productline" style="width:100%">
									<thead class="thead-dark">
										<tr>
											<th>
												<input type="checkbox" class="table-checkbox check_all">
											</th>
											<th>Product Line</th>
											<th>Assigned To</th>
											<th>Assigned Date</th>
										</tr>
									</thead>
									<tbody id="tbl_assign_productline_body"></tbody>
								</table>
							</div>
						</div>

						<div class="row justify-content-center">
							<div class="col-md-1 mb-5">
								<button type="button" id="btn_save" class="btn bg-blue btn-block permission-button btn-sm">
									<i class="fa fa-floppy-o"></i> Save
								</button>
							</div>
							<div class="col-md-1 mb-5">
								<button type="button" id="btn_clear" class="btn bg-grey btn-block btn-sm">
									<i class="fa fa-refresh"></i> Clear
								</button>
							</div>
							<div class="col-md-1 mb-5">
								<button type="button" id="btn_delete" class="btn bg-red btn-block permission-button btn-sm">
									<i class="fa fa-trash"></i> Delete
								</button>
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
		var getUserURL = "<?php echo e(url('/admin/assign-production-line/users')); ?>";
		var prodLineListURL = "<?php echo e(url('/admin/assign-production-line/list')); ?>";
		var prodLineDeleteURL = "<?php echo e(url('/admin/assign-production-line/destroy')); ?>";
		var dropdownProduct = "<?php echo e(url('/admin/assign-production-line/productline-select')); ?>";
		var SaveURL = "<?php echo e(route('admin.assign-production-line.save')); ?>";
		var code_permission = "A0002";

	</script>
	<script type="text/javascript" src="<?php echo e(asset('/js/pages/admin/assign-production-line/assign-production-line.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>