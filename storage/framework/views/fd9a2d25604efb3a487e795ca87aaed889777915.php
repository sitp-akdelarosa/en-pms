<?php $__env->startSection('content'); ?>
 <?php
 $exist = 0;
foreach ($user_accesses as $user_access){
        if($user_access->code == "T0004" ){
            $exist++;
        }
}
    if($exist == 0){
        echo  '<script>window.history.back()</script>';
        exit;
    }
?>
<section class="content-header">
    <h1>Production Schedule</h1>
</section>
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li><a class="active" href="#production_summary" data-toggle="tab">Production Summary</a></li>
            <li><a href="#jo_details" data-toggle="tab">JO Details</a></li>
            <li><a href="#travel_sheet" data-toggle="tab">Cancel JO Details</a></li>
        </ul>
        <div class="tab-content">

            <div class="tab-pane active" id="production_summary">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="form-group row">
                            <div class="col-md-5">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">From</span>
                                    </div>
                                    <input type="text" class="form-control" name="from" id="from">
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">To</span>
                                    </div>
                                    <input type="text" class="form-control" name="to" id="to">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <button type="button" class="btn btn-block btn-lg bg-blue" id="searchPS">
                                    <i class="fa fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover table-striped dt-responsive nowrap" style="width: 100%" id="tbl_prod_sum">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="5%"></th>
                                        <th>SC No.</th>
                                        <th>Product Code</th>
                                        <th>Description</th>
                                        <th>Order Qty</th>
                                        <th>Sched Qty</th>
                                        <th>P.O. No.</th>
                                        <th>Status</th>
                                        <th>Upload Date</th>
                                    </tr>
                                </thead>
                                <tbody id="tbl_prod_sum_body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>   
            </div>


            <div class="tab-pane" id="jo_details">
                <div class="row">
                    <div class="col-md-2 pull-right">
                        <div class="input-group input-group-sm mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Status</span>
                            </div>
                            <select class="form-control" name="status" id="status">
                                <option value="">New</option>
                                
                                <option value="scheduled">Scheduled</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center mb-15">
                    <div class="col-md-12">
                        <div class="table-reponsive">
                            <div class="loadingOverlay"></div>
                            <table class="table table-sm table-striped dt-responsive nowrap" style="width:100%" id="tbl_jo_details">
                                <thead class="thead-dark">
                                    <tr>
                                        <th></th>
                                        <th>SC No.</th>
                                        <th>Product Code</th>
                                        <th>Description</th>
                                        <th>Back Order Qty.</th>
                                        <th>Sched. Qty.</th>
                                        <th>Material Heat No.</th>
                                        <th>Material Used</th>
                                        <th>Lot No.</th>
                                    </tr>
                                </thead>
                                <tbody id="tbl_jo_details_body"></tbody>
                            </table>
                        </div>
                            
                    </div>
                </div>

                <form id="formbaba">
                    <div class="row justify-content-center mb-15">
                        <div class="col-md-4">

                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <p><input type="checkbox" class="table-checkbox" id="is_same" checked> Same In:Material Heat No. , Material Used and Lot No.</p>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Total Schedule Quantity:</span>
                                        </div>
                                        <input type="text" class="form-control clear" name="total_sched_qty" id="total_sched_qty" readonly value="0">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Job Order No.:</span>
                                        </div>
                                        <input list="jo_no" name="jo_no" class="form-control clear" id="jono" readonly>
                                        <datalist id="jo_no"></datalist>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Created By:</span>
                                        </div>
                                        <input type="text" class="form-control clear" name="created_by" id="created_by" value="<?php echo e(Auth::user()->user_id); ?>" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Created Date:</span>
                                        </div>
                                        <input type="text" class="form-control clear" name="created_date" id="created_date" value="<?php echo e(date('m/d/Y')); ?>" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Updated By:</span>
                                        </div>
                                        <input type="text" class="form-control clear" name="updated_by" id="updated_by" value="<?php echo e(Auth::user()->user_id); ?>" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Updated Date:</span>
                                        </div>
                                        <input type="text" class="form-control clear" name="updated_date" id="updated_date" value="<?php echo e(date('m/d/Y')); ?>" readonly>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-md-3 mb-5">
                            <button type="button" id="btn_save" class="btn bg-green btn-block permission-button">
                                <i class="fa fa-floppy-o"></i> Save
                            </button>
                        </div>
                        <div class="col-md-3 mb-5">
                            <button type="button" id="btn_edit" class="btn bg-blue btn-block permission-button">
                                <i class="fa fa-edit"></i> Edit
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

            <div class="tab-pane" id="travel_sheet">
                <div class="row justify-content-center mb-15">
                    <div class="col-md-12">
                        <div class="table-reponsive">
                            <table class="table table-sm table-striped dt-responsive nowrap" style="width:100%" id="tbl_travel_sheet">
                                <thead class="thead-dark">
                                    <tr>
                                        <th></th>
                                        <th>Job Order No.</th>
                                        <th>SC No.</th>
                                        <th>Product Code</th>
                                        <th>Description</th>
                                        <th>Order Qty.</th>
                                        <th>Sched Qty.</th>
                                        <th>Issued Qty.</th>
                                        <th>Material Used</th>
                                        <th>Material Heat No.</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="tbl_jo_details_body"></tbody>
                            </table>
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
        var datatableUpload = "<?php echo e(url('/transaction/production-schedule/get-production-list')); ?>";
        var getMaterialUsedURL = "<?php echo e(url('/transaction/production-schedule/get-material-used')); ?>";
        var getMaterialHeatNoURL = "<?php echo e(url('/transaction/production-schedule/get-material-heat-no')); ?>";
        var savejodetailsURL = "<?php echo e(url('/transaction/production-schedule/SaveJODetails')); ?>";
        var getjosuggest = "<?php echo e(url('/transaction/production-schedule/JOsuggest')); ?>";
        var getjotables = "<?php echo e(url('/transaction/production-schedule/getjotables')); ?>";
        var getjotablesALL = "<?php echo e(url('/transaction/production-schedule/getjoALL')); ?>";
        var getTravelSheetURL = "<?php echo e(url('/transaction/production-schedule/getTravelSheet')); ?>";
        var cancelTravelSheetURL = "<?php echo e(url('/transaction/production-schedule/cancelTravelSheet')); ?>";
        var code_permission = 'T0004';
        
    </script>
    <script type="text/javascript" src="<?php echo e(asset('/js/pages/ppc/transactions/production-schedule/production-schedule.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>