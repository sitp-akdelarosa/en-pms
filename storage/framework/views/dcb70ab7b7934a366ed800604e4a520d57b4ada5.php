<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title>Login | <?php echo e(config('app.name', 'Production Monitoring System')); ?></title>
    <link rel="stylesheet" href="<?php echo e(mix('/css/main.css')); ?>">

    <link rel="shortcut icon" type="image/x-icon" href="<?php echo e(asset('images/favicon.ico')); ?>">
</head>
<body class="hold-transition login-page">
    <div id="app">
        <div class="login-box">

            <div class="login-box-body">
                <div class="login-logo">
                    <a href="<?php echo e(url('/')); ?>">
                        <img src="<?php echo e(asset('images/logo2.png')); ?>" alt="">
                    </a>
                </div>

                <h4 class="login-box-msg">Production Monitoring System</h4>

                <?php echo $__env->yieldContent('content'); ?>

            </div>

        </div>
    </div>
    <script src="<?php echo e(mix('/js/main.js')); ?>"></script>
</body>
</html>
