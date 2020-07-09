<?php $__env->startPush('styles'); ?>
    <style>
        .stuck {
            position: fixed;
            width: 25%;
            max-width: 25%;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<section class="content-header">
    <h1>Profile</h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-3">
            <div class="box box-default">
                <div class="fx-card-item">
                    <div class="fx-card-avatar fx-overlay-1">
                        <img src="<?php echo e(asset($user->photo)); ?>" alt="user" class="img-fluid" height="200px">
                    </div>
                    <div class="fx-card-content text-center">
                        <h5 class="box-title"><?php echo e($user->firstname.' '.$user->lastname); ?></h5>
                        <span><?php echo e($user->user_type); ?></span>
                        <input type="hidden" id="user_id" value="<?php echo e($user->user_id); ?>">
                        <br> 
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <ul class="timeline" id="timeline"></ul>
            <div class="row" id="timeline-loading">
                <div class="offset-md-4 col-md-3">
                    <img src="<?php echo e(asset('images/spinner.gif')); ?>" width="185px" height="60px">
                </div>
            </div>
            <div class="row" id="show-more-row">
                <div class="offset-md-4 col-md-3 text-center">
                    <button class="btn btn-outline-secondary btn-block" id="show-more">Show more</button>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script type="text/javascript">
        var token = $('meta[name="csrf-token"]').attr('content');
    </script>
    <script src="<?php echo e(mix('/js/pages/profile.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>