<?php $__env->startSection('content'); ?>
<section class="content-header">
    <h1>Travel Sheet Status</h1>
</section>
<section class="content">
	<div class="row justify-content-center">
        <div class="col-lg-12">
        	 <div class="box">
                <div class="box-body">
	            	<form method="post" action="" id="frm_travel_sheet_status">
	                    <?php echo csrf_field(); ?>
	                    <input type="hidden" name="id" class="clear" id="id">
	            		<div class="row mb-5">
	            			<div class="col-md-6">

	            				<div class="form-group row">
	                    			<label for="mat_type" class="col-sm-3 control-label">Jo Number:</label>
	                                <div class="col-sm-9">
	                                    <input type="text" class="form-control validate clear switch" name="jo_no" id="jo_no">
	                                    <div id="jo_no_feedback"></div>
	                                </div>
	                            </div>

	                            <div class="form-group row">
	                    			<label for="character_num" class="col-sm-3 control-label">SC Number.:</label>
	                                <div class="col-sm-9">
	                                    <input type="text" class="form-control validate clear switch" name="sc_no" id="sc_no" disabled>
	                                    <div id="sc_no_feedback"></div>
	                                </div>
	                            </div>

	                            <div class="form-group row">
	                    			<label for="character_code" class="col-sm-3 control-label">Product Code:</label>
	                                <div class="col-sm-9">
	                                    <input type="text" class="form-control validate clear switch" name="prod_code" id="prod_code" disabled>
	                                    <div id="prod_code_feedback"></div>
	                                </div>
	                            </div>

		            		</div>

		            		<div class="col-md-6">

	                            <div class="form-group row">
	                    			<label for="description" class="col-sm-3 control-label">Qty:</label>
	                                <div class="col-sm-9">
	                                    <input type="text" class="form-control validate clear switch" name="qty" id="qty">
	                                        <div id="qty_feedback"></div>
	                                </div>
	                            </div>

	                           	<div class="form-group row">
	                    			<label for="description" class="col-sm-3 control-label">Status:</label>
	                                <div class="col-sm-9">
	                                   	<select class="form-control select-validate clear" name="status" id="status">
	                                   		<option value=""></option>
			                            	<option value="Working Process">Working Process</option>
			                            	<option value="Convert">Convert</option>
			                            	<option value="Completed">Completed</option>
			                           	</select>
	                                     	<div id="status_feedback"></div>
	                                </div>
	                            </div>

		            		</form>
		            		</div>
	            		</div>

	            	<div class="row">
	            		<div class="col-md-12">
	            			<div class="table-responsive">
	            				<table class="table table-sm table-hover table-striped dt-responsive nowrap" id="tbl_travel_sheet_status" style="width:100%">
		            				<thead class="thead-dark">
		            					<tr>
		            						<th width="5%">
	                                            <input type="checkbox" class="table-checkbox check_all">
	                                        </th>
	                                        <th width="5%"></th>
		            						<th>Division Code</th>
		            						<th>Leader</th>
		            						<th>Process</th>
		            						<th>Unprocess</th>
		            						<th>Good</th>
		            						<th>Rework</th>
		            						<th>Scrap</th>
		            						<th>Convert</th>
		            					</tr>
		            				</thead>
		            				<tbody id="tbl_travel_sheet_status_body"></tbody>
		            			</table>
	            			</div>
	            		</div>
	            	</div>

    	</div>
    </div>

    <div class="row">
		<div class="col-md-12">
			<div class="row justify-content-center">
			    <div class="col-md-2 mb-5">
			       	<button type="submit" id="btn_export_exel" class="btn bg-green btn-block permission-button">
			            <i class="fa fa-file-text-o"></i> Export Exel
			        </button>
			    </div>
			    <div class="col-md-2 mb-5">
			        <button type="button" id="btn_close" class="btn bg-red btn-block">
			            <i class="fa fa-times"></i> Close
			        </button>
			    </div>
			</div>
		</div>
	</div>

</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>