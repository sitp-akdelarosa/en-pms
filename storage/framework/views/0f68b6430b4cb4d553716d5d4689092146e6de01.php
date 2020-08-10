<div id="modal_user_access" class="modal fade " data-backdrop="static">
	<div class="modal-dialog" role="document">
		<form id="frm_user" role="form" method="POST" enctype="multipart/form-data" action="<?php echo e(route('admin.user-master.save')); ?>">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">User Modules</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body table-responsive">
					<div class="loadingOverlay-modal"></div>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group row">
								<div class="col-sm-12">
									<img src="<?php echo e(asset('images/default-profile.png')); ?>" class="photo img-fluid" id="photo_profile">
								</div>
							</div>

							<div class="form-group row">
								<div class="col-sm-12">
									<div class="custom-file mb-3" id="customFile" lang="es">
										<input type="file" class="custom-file-input validate clear" id="photo" name="photo" aria-describedby="fileHelp">
										<label class="custom-file-label" id="photo_label" for="photo">Select a photo...</label>
									</div>
								</div>
								<div class="col-sm-12">
									<div id="photo_feedback"></div>
								</div>
							</div>
						</div>
						<div class="col-md-8">
							<?php echo csrf_field(); ?>
							<input type="hidden" id="id" name="id" class="clear">

							<div class="row">
								<div class="col-md-7">
									<div class="input-group mb-3 input-group-sm">
										<div class="input-group-prepend">
											<span class="input-group-text">User ID:</span>
										</div>
										<input type="text" class="form-control input-sm validate clear" name="user_id" id="user_id">
										<div id="user_id_feedback"></div>
									</div>
								</div>

								<div class="col-md-5">
									<div class="input-group mb-3">
										<div class="input-group-prepend">
											<div class="input-group-text">
												<input type="checkbox" name="is_admin" id="is_admin" class="table-checkbox" value="1">
											</div>
										</div>
										<input type="text" class="form-control" value="Set as Administrator" disabled="true">
									</div>
								</div>
							</div>

							<div class="input-group mb-3 input-group-sm">
								<div class="input-group-prepend">
									<span class="input-group-text">First Name:</span>
								</div>
								<input type="text" class="form-control input-sm validate clear" name="firstname" id="firstname" >
								<span id="firstname_feedback"></span>
							</div>

							
							
							

							

							<div class="input-group mb-3 input-group-sm">
								<div class="input-group-prepend">
									<span class="input-group-text">Last Name:</span>
								</div>
								<input type="text" class="form-control input-sm validate clear" name="lastname" id="lastname" >
								<span id="lastname_feedback"></span>
							</div>

							

							<div class="input-group mb-3 input-group-sm">
								<div class="input-group-prepend">
									<span class="input-group-text">User Type:</span>
								</div>
								<select class="form-control select-validate input-sm clear" name="user_type" id="user_type"></select>
								<span id="user_type_feedback"></span>
							</div>

							

							

							<div class="input-group mb-3 input-group-sm">
								<div class="input-group-prepend">
									<span class="input-group-text">Email:</span>
								</div>
								<input type="email" class="form-control input-sm validate clear" name="email" id="email" >
								<span id="email_feedback"></span>
							</div>

							

							<div class="input-group mb-3 input-group-sm">
								<div class="input-group-prepend">
									<span class="input-group-text">Password:</span>
								</div>
								<input type="password" class="form-control input-sm validate clear" name="password" id="password" >
								<span id="password_feedback"></span>
							</div>

							

							<div class="input-group mb-3 input-group-sm">
								<div class="input-group-prepend">
									<span class="input-group-text">Confirm Password:</span>
								</div>
								<input type="password" class="form-control input-sm validate clear" name="password_confirmation" id="password_confirmation" >
							</div>

							
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<table class="table table-sm table-striped" width="100%" id="tbl_modules">
								<thead class="thead-dark">
									<tr>
										<th>Code</th>
										<th>Title</th>
										<th>Read/Write</th>
										<th>Read Only</th>
									</tr>
								</thead>
								<tbody id="tbl_modules_body">
									<tr>
										<td colspan="3">Please select user's user type.</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					
				</div>
				<div class="modal-footer">
					<button type="button" class="btn bg-red" data-dismiss="modal">Close</button>
					<button type="submit" class="btn bg-green float-right permission-button">Save</button>
				</div>

			</div>
		</form>
	</div>
</div>
