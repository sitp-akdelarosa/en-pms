<?php $__env->startSection('content'); ?>
 <?php
 $exist = 0;
foreach ($user_accesses as $user_access){
        if($user_access->code == "M0002" ){
            $exist++;
        }
}
    if($exist == 0){
        echo  '<script>window.history.back()</script>';
        exit;
    }
?>
<section class="content-header">
    <h1>Dropdown Master</h1>
</section>

<section class="content">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">

                <div class="box">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped dt-responsive nowrap" id="tbl_dropdown" style="width:100%">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th width="10%"></th>
                                                <th width="90%">Dropdown Name</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbl_dropdown_body"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

        </div>

    </div>
</section>
<?php echo $__env->make('includes.modals.masters.dropdown-master-modals', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo $__env->make('includes.modals.system-modals', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script type="text/javascript">
        var code_permission = "M0002";
        var token = $('meta[name="csrf-token"]').attr('content');
        var dropdownListURL = "<?php echo e(url('masters/dropdown-master/dropdown-list')); ?>";
        var dropdownNamesURL = "<?php echo e(url('masters/dropdown-master/names')); ?>"
        var getDropdownItemURL = "<?php echo e(url('masters/dropdown-master/items')); ?>";


        var dropdownNamesDeleteURL = "<?php echo e(url('masters/dropdown-master/destroy/names')); ?>";
        var dropdownItemsDeleteURL = "<?php echo e(url('masters/dropdown-master/destroy/items')); ?>";
        var checkItemExistURL = "<?php echo e(url('masters/dropdown-master/check-items')); ?>";
        
    </script>
    <script type="text/javascript" src="<?php echo e(asset('/js/pages/ppc/masters/dropdown-master/dropdown-master.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>