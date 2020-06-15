<?php $__env->startSection('content'); ?>
 <?php
 $exist = 0;
foreach ($user_accesses as $user_access){
        if($user_access->code == "M0003" ){
            $exist++;
        }
}
    if($exist == 0){
        echo  '<script>window.history.back()</script>';
        exit;
    }
?>
<section class="content-header">
    <h1>Product Master</h1>
</section>

<section class="content">
	<div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li><a class="active" href="#product_code_assembly_tab" data-toggle="tab">Product Code Assembly</a></li>
            <li><a href="#product_code_tab" data-toggle="tab">Product Code (16 Code)</a></li>
        </ul>
        <div class="tab-content">

            <div class="tab-pane active" id="product_code_assembly_tab">
            	<form method="post" action="<?php echo e(route('masters.product-master.assembly.save')); ?>" id="frm_code_assembly">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="assembly_id" class="clear" id="assembly_id">
            		<div class="row mb-5">
            			<div class="col-md-6">

            				<div class="form-group row">
                                <label for="prod_type" class="col-sm-3 control-label mt-5">Product Line:</label>
                                <div class="col-sm-9">
                                    <select class="form-control select-validate clear switch" name="prod_type" id="prod_type"></select>
                                    <div id="prod_type_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="character_num" class="col-sm-3 control-label mt-5">Character #:</label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control validate clear switch" name="character_num" id="character_num" min="1" max="16">
                                    <div id="character_num_feedback"></div>
                                </div>
                            </div>

	            		</div>

	            		<div class="col-md-6">

	            			<div class="form-group row">
                                <label for="character_code" class="col-sm-3 control-label mt-5">Character Code:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate clear switch" name="character_code" id="character_code">
                                    <div id="character_code_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="description" class="col-sm-3 control-label mt-5">Description:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate clear switch" name="description" id="description">
                                    <div id="description_feedback"></div>
                                </div>
                            </div>

	            		</div>
            		</div>

            		<div class="row justify-content-center">
                        <div class="col-md-2 mb-5">
                            <button type="submit" id="btn_save_assembly" class="btn bg-blue btn-block permission-button">
                                <i class="fa fa-plus"></i> Add
                            </button>
                        </div>
                        <div class="col-md-2 mb-5" id="div_clear">
                            <button type="button" id="btn_clear_assembly" class="btn bg-gray btn-block">
                                <i class="fa fa-refresh"></i> Clear
                            </button>
                        </div>
                        <div class="col-md-2 mb-5" id="div_delete">
                            <button type="button" id="btn_delete_assembly" class="btn bg-red btn-block permission-button">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </div>
                        <div class="col-md-2 mb-5" id="div_cancel">
                            <button type="button" id="btn_cancel_assembly" class="btn bg-red btn-block">
                                <i class="fa fa-times"></i> Cancel
                            </button>
                        </div>
                    </div>
            	</form>

            	<div class="row">
            		<div class="col-md-12">

            			<div class="table-responsive">
            				<table class="table table-sm table-striped dt-responsive nowrap" id="tbl_prodcode_assembly" style="width:100%">
	            				<thead class="thead-dark">
	            					<tr>
	            						<th width="5%">
                                            <input type="checkbox" class="table-checkbox check_all">
                                        </th>
                                        <th width="5%"></th>
	            						<th>Product Line</th>
	            						<th>Character #</th>
	            						<th>Character Code</th>
	            						<th>Description</th>
	            					</tr>
	            				</thead>
	            				<tbody id="tbl_prodcode_assembly_body"></tbody>
	            			</table>
            			</div>

            		</div>
            	</div>
            </div>


            <div class="tab-pane" id="product_code_tab">
		        <div class="row mb-5">
                    <div class="loading"></div>
		            <div class="col-md-5">

		                <div class="form-group row mb-10">
		                    <label for="" class="control-label mt-5 col-sm-3 mt-5">Product Line:</label>
		                    <div class="col-sm-9">
                                <select class="form-control select-validatselect-codeselect-codee clear switch" name="product-type" id="product-type"></select>
		                    </div>
		                </div>

                        <form action="<?php echo e(url('/masters/product-master/code/product/save')); ?>" id="frm_prod_code">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="product_type" id="product_type" class="clear">
                            <input type="hidden" name="product_id" id="product_id" class="clear">

                            <div class="form-group row">
                                <label for="" class="control-label mt-5 col-sm-3">Product Code:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate clear" name="product_code" id="product_code" maxlength="16">
                                    <div id="product_code_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="" class="control-label mt-5 col-sm-3">Description:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control validate clear" name="code_description" id="code_description">
                                    <div id="code_description_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="" class="control-label mt-5 col-sm-3">Alloy:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control clear" name="alloy" id="alloy">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="" class="control-label mt-5 col-sm-3">Item:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control clear" name="item" id="item">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="" class="control-label mt-5 col-sm-3">Size:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control clear" name="size" id="size" >
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="" class="control-label mt-5 col-sm-3">Class:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control clear" name="class" id="class" >
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="" class="control-label mt-5 col-sm-3">Standard Material Used:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control clear" name="standard_material_used" id="standard_material_used" >
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="" class="control-label mt-5 col-sm-3">Cut Weight:</label>
                                <div class="col-sm-5">
                                    <input type="number" class="form-control validate mb-3 size " name="cut_weight" id="cut_weight" step="any">
                                    <div id="cut_weight_feedback"></div>
                                </div>
                                <div class="col-sm-4">
                                    <select class="form-control select-validate mb-3" name="cut_weight_uom" id="cut_weight_uom">
                                        <option value="N/A">N/A</option>
                                        <option value="KG">kg</option>
                                        <option value="LBS">lbs</option>
                                    </select>
                                    <div id="cut_weight_uom_feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row mb-10">
                                <label for="" class="control-label mt-5 col-sm-3">Cut Length:</label>
                                <div class="col-sm-5">
                                    <input type="number" class="form-control validate mb-3 size" name="cut_length" id="cut_length" step="any">
                                    <div id="cut_length_feedback"></div>
                                </div>
                                <div class="col-sm-4">
                                    <select class="form-control select-validate mb-3" name="cut_length_uom" id="cut_length_uom">
                                        <option value="N/A">N/A</option>
                                        <option value="MM">mm</option>
                                        <option value="CM">cm</option>
                                        <option value="M">M</option>
                                    </select>
                                    <div id="cut_length_uom_feedback"></div>
                                </div>
                            </div>

    		                <div class="form-group row">
                                <label for="first" class="col-sm-3 control-label mt-5">1st Code:</label>
                                <div class="col-sm-6">
                                    <select class="form-control select-code mb-3" name="first" id="first">
    	                                <option value=""></option>
    	                            </select>
                                </div>
                                <div class="col-sm-3">
    		                        <select class="form-control select-code mb-3" name="first_val" id="first_val">
    		                            <option value=""></option>
    		                        </select>
    		                    </div>
                            </div>

                            <div class="form-group row">
                                <label for="second" class="col-sm-3 control-label mt-5">2nd Code:</label>
                                <div class="col-sm-6">
                                    <select class="form-control select-code mb-3" name="second" id="second">
    	                                <option value=""></option>
    	                            </select>
                                </div>
                                <div class="col-sm-3">
    		                        <select class="form-control select-code mb-3" name="second_val" id="second_val">
    		                            <option value=""></option>
    		                        </select>
    		                    </div>
                            </div>

                            <div class="form-group row" id="hide_3rd">
                                <label for="third" class="col-sm-3 control-label mt-5">3rd Code:</label>
                                <div class="col-sm-6">
                                    <select class="form-control select-code mb-3" name="third" id="third">
    	                                <option value=""></option>
    	                            </select>
                                </div>
                                <div class="col-sm-3">
    		                        <select class="form-control select-code mb-3" name="third_val" id="third_val">
    		                            <option value=""></option>
    		                        </select>
    		                    </div>
                            </div>

                            <div class="form-group row">
                                <label for="forth" class="col-sm-3 control-label mt-5">4th Code:</label>
                                <div class="col-sm-6">
                                    <select class="form-control select-code mb-3" name="forth" id="forth">
    	                                <option value=""></option>
    	                            </select>
                                </div>
                                <div class="col-sm-3">
    		                        <select class="form-control select-code mb-3" name="forth_val" id="forth_val">
    		                            <option value=""></option>
    		                        </select>
    		                    </div>
                            </div>

                            <div class="form-group row">
                                <label for="fifth" class="col-sm-3 control-label mt-5">5th Code:</label>
                                <div class="col-sm-6">
                                    <select class="form-control select-code mb-3" name="fifth" id="fifth">
    	                                <option value=""></option>
    	                            </select>
                                </div>
                                <div class="col-sm-3">
    		                        <select class="form-control select-code mb-3" name="fifth_val" id="fifth_val">
    		                            <option value=""></option>
    		                        </select>
    		                    </div>
                            </div>

                            <div class="form-group row">
                                <label for="seventh" class="col-sm-3 control-label mt-5">7th Code:</label>
                                <div class="col-sm-6">
                                    <select class="form-control select-code mb-3" name="seventh" id="seventh">
    	                                <option value=""></option>
    	                            </select>
                                </div>
                                <div class="col-sm-3">
    		                        <select class="form-control select-code mb-3" name="seventh_val" id="seventh_val">
    		                            <option value=""></option>
    		                        </select>
    		                    </div>
                            </div>

                            <div class="form-group row">
                                <label for="eighth" class="col-sm-3 control-label mt-5">8th Code:</label>
                                <div class="col-sm-6">
                                    <select class="form-control select-code mb-3" name="eighth" id="eighth">
    	                                <option value=""></option>
    	                            </select>
                                </div>
                                <div class="col-sm-3">
    		                        <select class="form-control select-code mb-3" name="eighth_val" id="eighth_val">
    		                            <option value=""></option>
    		                        </select>
    		                    </div>
                            </div>

                            <div class="form-group row" id="hide_9th">
                                <label for="ninth" class="col-sm-3 control-label mt-5">9th Code:</label>
                                <div class="col-sm-6">
                                    <select class="form-control select-code mb-3" name="ninth" id="ninth">
    	                                <option value=""></option>
    	                            </select>
                                </div>
                                <div class="col-sm-3">
    		                        <select class="form-control select-code mb-3" name="ninth_val" id="ninth_val">
    		                            <option value=""></option>
    		                        </select>
    		                    </div>
                            </div>

                            <div class="form-group row">
                                <label for="eleventh" class="col-sm-3 control-label mt-5">11th Code:</label>
                                <div class="col-sm-6">
                                    <select class="form-control select-code mb-3" name="eleventh" id="eleventh">
    	                                <option value=""></option>
    	                            </select>
                                </div>
                                <div class="col-sm-3">
    		                        <select class="form-control select-code mb-3" name="eleventh_val" id="eleventh_val">
    		                            <option value=""></option>
    		                        </select>
    		                    </div>
                            </div>

                            <div class="form-group row mb-10" id="hide_14th">
                                <label for="forteenth" class="col-sm-3 control-label mt-5">14th Code:</label>
                                <div class="col-sm-6">
                                    <select class="form-control select-code mb-3" name="forteenth" id="forteenth">
    	                                <option value=""></option>
    	                            </select>
                                </div>
                                <div class="col-sm-3">
    		                        <select class="form-control select-code mb-3" name="forteenth_val" id="forteenth_val">
    		                            <option value=""></option>
    		                        </select>
    		                    </div>
                            </div>

			                <div class="row justify-content-center">
					            <div class="col-md-3 mb-5">
					                <button type="submit" id="btn_save" class="btn bg-green btn-block permission-button">
					                    <i class="fa fa-floppy-o"></i> Save
					                </button>
					            </div>
					            <div class="col-md-3 mb-5">
					                <button type="button" id="btn_cancel" class="btn bg-red btn-block">
					                    <i class="fa fa-times"></i> Cancel
					                </button>
					            </div>
					        </div>
		                </form>
		            </div>

		            <div class="col-md-7">
		                <div class="table-reponsive">
		                    <table class="table table-striped table-sm mb-10 dt-responsive display nowrap" id="tbl_product_code" style="width:100%">
		                        <thead class="thead-dark">
		                            <tr>
		                                <th width="5%">
		                                    <input type="checkbox" class="table-checkbox check_all_product">
		                                </th>
		                                <th></th>
		                                <th>Product Line</th>
		                                <th>Product Code</th>
		                                <th>Description</th>
		                                <th>Date Created</th>
		                            </tr>
		                        </thead>
		                        <tbody id="tbl_product_code_body"></tbody>
		                    </table>
		                </div>

		                <div class="row justify-content-center">
		                	<div class="col-md-3 mb-5">
				                <button type="button" id="btn_delete_product" class="btn bg-red btn-block permission-button">
				                    <i class="fa fa-trash"></i> Delete Product
				                </button>
				            </div>
		                </div>
		            </div>
		        </div>

            </div>
        </div>
    </div>
</section>
<?php echo $__env->make('includes.modals.masters.product-master-modals', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
    <script type="text/javascript">
        var token = $('meta[name="csrf-token"]').attr('content');
        var assemblyListURL = "<?php echo e(url('/masters/product-master/assembly/list')); ?>";
        var assemblyDeleteURL = "<?php echo e(url('/masters/product-master/assembly/destroy')); ?>";
        var prodTypeURL = "<?php echo e(url('/masters/product-master/code/product-type')); ?>";
        var showDropdownURL = "<?php echo e(url('/masters/product-master/code/show-dropdowns')); ?>";
        var divProcessURL = "<?php echo e(url('/masters/product-master/code/get-process-div')); ?>";
        var prodProcessListURL = "<?php echo e(url('/masters/product-master/code/get-prod-process-list')); ?>";
        var prodCodeListURL = "<?php echo e(url('/masters/product-master/code/get-prod-code-list')); ?>";
        var productDeleteURL = "<?php echo e(url('/masters/product-master/product/destroy')); ?>";
        var processDeleteURL = "<?php echo e(url('/masters/product-master/process/destroy')); ?>";
        var productTypeURL = "<?php echo e(url('/masters/product-master/code/product-type')); ?>";
        var getProductLineURL = "<?php echo e(url('/masters/product-master/get-product-line')); ?>";
        var getProcessURL = "<?php echo e(url('/masters/product-master/getProcessURL')); ?>";
        var getdropdownproduct = "<?php echo e(url('/masters/product-master/get_dropdown_product')); ?>";
        var getStandardMaterialURL = "<?php echo e(url('/masters/product-master/get-standard-material')); ?>";

        var getSetURL = "<?php echo e(url('masters/process-master/get-set')); ?>";

        var code_permission = "M0003";
    </script>
    <script type="text/javascript" src="<?php echo e(asset('/js/pages/ppc/masters/product-master/product-master.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>