<?php $__env->startSection('title'); ?>
	PPC Dashboard
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<section class="content-header">
    <h1>Dashboard</h1>
</section>

<section class="content">
    <div class="form-group row">
        
    </div>

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Work In Progress</h3>
            <div class="box-tools">
                <button type="button" class="btn btn-sm bg-teal" id="btn_search_filter">
                    <i class="fa fa-search"></i> Search / Filter
                </button>
            </div>
        </div>

        <div class="box-body">
            
                <table class="table table-sm table-hover table-bordered table-striped nowrap" id="tbl_dashboard" style="width: 100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>JO No.</th>
                            <th>Item Code</th>
                            <th>Description</th>
                            <th>Divison</th>
                            <th>Plant</th>
                            <th>Process</th>
                            <th>Material</th>
                            <th>Heat No.</th>
                            <th>Lot No.</th>
                            <th>Sched Qty.</th> 
                            <th>Unprocess</th>
                            <th>Good</th>
                            <th>Rework</th>
                            <th>Scrap</th>
                            <th>Total Ouput</th>
                            <th>Order Qty.</th>
                            <th>Total Issued Qty</th>
                            <th>Issued Qty</th> 
                            <th>Update Date</th> 
                            <th>End Date</th>                      
                            <th>Status</th>

                        </tr>
                    </thead>
                    <tbody id="tbl_dashboard_body"></tbody>
                </table>
                
            
            
        </div>
    </div>

   <form action="" method="post" role="form">
        <div class="form-group row">
            <div class="col-md-5">
                <div class="input-group input-group-sm mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Job Order</span>
                    </div>
                    <select class="form-control" name="jo_no" id="jo_no" ></select>
                </div>
            </div>
        </div>
    </form>

    <div class="row" id="chart"></div>
</section>
<?php echo $__env->make('includes.modals.system-modals', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo $__env->make('includes.modals.ppc-dashboard-modal', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script type="text/javascript">
        var get_dashboard = "<?php echo e(url('/dashboard/get-dashboard')); ?>";
        var get_chartURl = "<?php echo e(url('/dashboard/pie-graph')); ?>";
        var get_jonoURL = "<?php echo e(url('/dashboard/get-jono')); ?>";
        var downloadSearchExcelURL = "<?php echo e(url('/dashboard/dashboard-search-excel')); ?>";
        var getFilteredDataURL = "<?php echo e(url('/dashboard/dashboard-search-filter')); ?>";
    </script>
    <script src="<?php echo e(mix('/js/pages/ppc/dashboard/dashboard.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>