<div id="modal_user_access" class="modal fade " data-backdrop="static">
    <div class="modal-dialog" role="document">
        <form id="frm_user" role="form" method="POST" enctype="multipart/form-data" action="{{ route('admin.user-master.save') }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">User Modules</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body table-responsive">
                    <div class="loadingOverlay"></div>
                    <div class="row">
                        <div class="col-md-4">
							<div class="form-group row">
				                <div class="col-sm-12">
				                	<img src="{{ asset('images/default-profile.png') }}" class="photo img-fluid" id="photo_profile">
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
                            @csrf
                            <input type="hidden" id="id" name="id" class="clear">
				            <div class="form-group row">
				            	<label for="user_id" class="col-sm-3 control-label">User ID:</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control input-sm validate clear" name="user_id" id="user_id">
                                    <div id="user_id_feedback"></div>
                                </div>

			                    <div class="col-sm-4">
				                    <label><input type="checkbox" name="is_admin" id="is_admin" class="table-checkbox" value="1"> Set as Administrator</label>
				                </div>
			                </div>

				            <div class="form-group row">
			                	<label for="firstname" class="col-sm-3 control-label">First Name:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm validate clear" name="firstname" id="firstname" >
			                        <span id="firstname_feedback"></span>
                                </div>
				            </div>

				            <div class="form-group row">
				            	<label for="lastname" class="col-sm-3 control-label">Last Name:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-sm validate clear" name="lastname" id="lastname" >
				                    <span id="lastname_feedback"></span>
                                </div>
				            </div>

				            <div class="form-group row">
				            	<label for="user_type" class="col-sm-3 control-label">User Type:</label>
                                <div class="col-sm-9">
                                    <select class="form-control select-validate input-sm clear" name="user_type" id="user_type"></select>
			                        <span id="user_type_feedback"></span>
                                </div>
				            </div>

{{-- 				            <div class="form-group row">
				            	<label for="div_code" class="col-sm-3 control-label">Division Code:</label>
                                <div class="col-sm-9">
                                    <select class="form-control input-sm select-validate clear" list="divcode" name="div_code" id="div_code"></select>
			                        <span id="div_code_feedback"></span>
                                </div>
				            </div>
 --}}
				            <div class="form-group row">
				            	<label for="email" class="col-sm-3 control-label">Email:</label>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control input-sm validate clear" name="email" id="email" >
				                    <span id="email_feedback"></span>
                                </div>
				            </div>

				            <div class="form-group row">
				            	<label for="password" class="col-sm-3 control-label">Password:</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control input-sm validate clear" name="password" id="password" >
				                    <span id="password_feedback"></span>
                                </div>
				            </div>

				            <div class="form-group row">
				            	<label for="password_confirmation" class="col-sm-4 control-label">Confirm Password:</label>
                                <div class="col-sm-8">
                                    <input type="password" class="form-control input-sm validate clear" name="password_confirmation" id="password_confirmation" >
                                </div>
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
