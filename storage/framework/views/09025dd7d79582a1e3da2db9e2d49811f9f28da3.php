<?php $__env->startSection('title'); ?>
	Update Inventory
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
 <?php
 $exist = 0;
foreach ($user_accesses as $user_access){
        if($user_access->code == "T0001" ){
            $exist++;
        }
}
    if($exist == 0){
        echo  '<script>window.history.back()</script>';
        exit;
    }
?>
<section class="content-header">
    <h1>Update Inventory</h1>
</section>

<section class="content">
    
	<div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-sm-12 col-xs-12">

            <form id="frm_update_inventory" role="form" method="post" files="true" enctype="multipart/form-data" action="">
                <?php echo csrf_field(); ?>
                <div class="form-group row">
                    <label for="item_class" class="col-md-2 control-label mt-5">Item Class:</label>
                    <div class="col-md-3">
                        <select class="form-control select-validate clear" name="up_item_class" id="up_item_class">
                            <option value=""></option>
                            <option value="RAW MATERIAL">RAW MATERIAL</option>
                            <option value="PRODUCT">PRODUCT</option>
                        </select>
                        <div id="item_class_feedback"></div>
                    </div>
                    <div class="custom-file col-md-5 mb-3" id="customFile" lang="es">
                        <input type="file" class="custom-file-input" name="file_inventory" id="file_inventory" aria-describedby="fileHelp">
                        <label class="custom-file-label" for="file_inventory" id="file_inventory_label">
                           Select file...
                        </label>
                    </div>

                    <div class="col-md-2 mb-3">
                        <button class="btn btn-block bg-blue">
                            <i class="fa fa-upload"></i> Upload
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="box">
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped table-bordered nowrap mb-10" id="tbl_materials" style="width: 100%">
                            <thead class="thead-dark">
                                <tr>
                                    <th>
                                        <input type='checkbox' class='table-checkbox check_all_inventories'/>
                                    </th>
                                    <th></th>
                                    <th>Item Class</th>
                                    
                                    <th>Item Type / Line</th>
                                    <th>Item Code</th>

                                    <th>Length</th>
                                    <th>Std. Material Wt.</th>
                                    <th>Std. wt. Received</th>
                                    <th>Crude / Finish Wt.</th>
                                    <th>Qty(KGS)</th>
                                    <th>Qty(PCS)</th>
                                    <th>Stock(PCS)</th>
                                    <th>Heat No.</th>
                                    <th>Lot No.</th>

                                    <th>J.O. / Receiving No.</th>

                                    <th>Description</th>

                                    <th>Warehouse</th>
                                    
                                    <th>Item</th>
                                    <th>Alloy</th>
                                    <th>Schedule/Class</th>
                                    <th>Size</th>
                                    <th>Width</th>
                                    
                                    <th>Invoice No.</th>
                                    <th>Received Date</th>
                                    <th>Supplier</th>
                                    <th>Supplier Heat No.</th>
                                    <th>Material Used</th>
                                    <th>Added By</th>
                                    <th>Update Date</th>
                                </tr>
                            </thead>
                            <tbody id="tbl_materials_body"></tbody>
                        </table>
                    </div>
                    
                    <div class="row justify-content-center mb-5">
                        <div class="col-lg-2 col-md-2 col-sm-3">
                            <button id="btn_zero" class="btn btn-sm btn-block bg-blue mb-3">Include 0 quantity</button>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-3">
                            <button id="btn_add" class="btn btn-sm btn-block bg-green mb-3 permission-button">
                                <i class="fa fa-plus"></i> Add Product / Material
                            </button>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-3">
                            <button id="btn_delete" class="btn btn-sm btn-block bg-red mb-3">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-3">
                            <button id="btn_check_unregistered" class="btn btn-sm btn-block bg-purple mb-3 permission-button">
                                <i class="fa fa-check"></i> Check Unregistered Materials
                            </button>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-3">
                            <button id="btn_download_format" class="btn btn-sm btn-block bg-navy mb-3">
                                <i class="fa fa-file-excel-o"></i> Download Format
                            </button>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-3">
                            <button id="btn_search_filter" class="btn btn-sm btn-block bg-teal mb-3">
                                <i class="fa fa-search"></i> Search / Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php echo $__env->make('includes.modals.transactions.update-inventory-modals', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
    <script type="text/javascript">
        var token = $('meta[name="csrf-token"]').attr('content');
        var checkfile = "<?php echo e(url('/transaction/update-inventory/CheckFileUpdateInventory')); ?>";
        var uploadInventory = "<?php echo e(url('/transaction/update-inventory/UploadInventory')); ?>";
        var materialDataTable = "<?php echo e(url('/transaction/update-inventory/materials')); ?>";
        var AddManual = "<?php echo e(url('/transaction/update-inventory/AddManual')); ?>";
        var materialTypeURL = "<?php echo e(url('/transaction/update-inventory/material-type')); ?>";
        var productLineURL = "<?php echo e(url('/transaction/update-inventory/prod-lines')); ?>";
        var warehouseURL = "<?php echo e(url('/transaction/update-inventory/warehouse')); ?>";
        var GetItemCodeURL = "<?php echo e(url('/transaction/update-inventory/GetItemCode')); ?>";
        var getItemCodeDetailsurl = "<?php echo e(url('/transaction/update-inventory/GetItemCodeDetails')); ?>";
        var code_permission = 'T0001';
        var downloadNonexistingURL = "<?php echo e(url('/transaction/update-inventory/download-unregistered-materials')); ?>";
        var getNonexistingURL = "<?php echo e(url('/transaction/update-inventory/get-unregistered-materials')); ?>";
        var downloadMaterialFormatURL = "<?php echo e(url('/transaction/update-inventory/download-inventory-material-format')); ?>";
        var downloadProductFormatURL = "<?php echo e(url('/transaction/update-inventory/download-inventory-product-format')); ?>";
        var downloadSearchExcelURL = "<?php echo e(url('/transaction/update-inventory/download-update-inventory-search')); ?>";
        var inventoryDeleteURL = "<?php echo e(url('/transaction/update-inventory/delete-inventory')); ?>";
        var checkInventoryDeletionURL = "<?php echo e(url('/transaction/update-inventory/check-inventory-deletion')); ?>";
    </script>
    <script type="text/javascript" src="<?php echo e(mix('/js/pages/ppc/transactions/update-inventory/update-inventory.js')); ?>"></script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>