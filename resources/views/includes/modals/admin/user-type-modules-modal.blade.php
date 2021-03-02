<div id="modal_user_type_modules" class="modal fade " data-backdrop="static">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Select Pages</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body table-responsive">
				<div class="loadingOverlay-modal"></div>

				<div class="row">
					<div class="col-md-12">
						<table class="table table-sm table-striped table-bordered" width="100%" id="tbl_modules">
							<thead class="thead-dark">
								<tr>
									<th>
										<input type="checkbox" class="table-checkbox check_all_modules">
									</th>
									<th>Code</th>
									<th>Title</th>
								</tr>
							</thead>
							<tbody id="tbl_modules_body"></tbody>
						</table>
					</div>
				</div>
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn bg-red" data-dismiss="modal">Close</button>
				<button type="button" class="btn bg-green float-right permission-button" id="btn_save_module">Select</button>
			</div>

		</div>
	</div>
</div>