<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <?php if(Auth::check()): ?>
    <meta name="user_id" content="<?php echo e(Auth::user()->id); ?>">
    <?php endif; ?>

    <title><?php echo $__env->yieldContent('title'); ?> | <?php echo e(config('app.name', 'Production Monitoring System')); ?></title>

    <?php echo $__env->yieldPushContent('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(mix('/css/main.css')); ?>">

    <link rel="shortcut icon" type="image/x-icon" href="<?php echo e(asset('images/favicon.ico')); ?>">
</head>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="app" class="wrapper">

        <div class="loadingOverlay"></div>
        
        <?php echo $__env->make('includes.layout.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

        <?php echo $__env->make('includes.layout.sidebar', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>


        <div class="content-wrapper">
            <?php echo $__env->yieldContent('content'); ?>
        </div>

        <footer class="main-footer">
            <div class="pull-right d-none d-sm-inline-block">
            </div>Copyright &copy; <?php echo e(date('Y')); ?> All Rights Reserved.
        </footer>

        <?php echo $__env->make('includes.modals.system-modals', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    </div>

    <script type="text/javascript">
        var getAuditTrailDataURL = "<?php echo e(url('/admin/audit-trail/get-data')); ?>";
    </script>

    <script src="<?php echo e(mix('/js/main.js')); ?>"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>

</body>

</html>