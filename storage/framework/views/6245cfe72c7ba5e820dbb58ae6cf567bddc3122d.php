<header class="main-header">
    <a href="<?php echo e(url('/')); ?>" class="logo">
        <span class="logo-mini">
            <img src="<?php echo e(asset('images/logo2.png')); ?>" class="img-fluid">
        </span>
        <span class="logo-lg">
            <div class="logo-wrapper">
                <img src="<?php echo e(asset('images/logo2.png')); ?>" class="img-fluid">
            </div>
        </span>
    </a>
    <nav class="navbar navbar-static-top">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <?php if(Auth::check()): ?>
                            <img src="<?php echo e(asset(Auth::user()->photo)); ?>" class="user-image rounded" alt="User Image">
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu scale-up">
                        <li class="user-header">
                            <?php if(Auth::check()): ?>
                                <img src="<?php echo e(asset(Auth::user()->photo)); ?>" class="rounded float-left" alt="User Image">
                            <?php endif; ?>

                            <p>
                                <?php if(Auth::check()): ?>
                                    <?php echo e(Auth::user()->firstname.' '.Auth::user()->lastname); ?>

                                <?php endif; ?>

                                <small>
                                    <?php if(Auth::check()): ?>
                                        <?php echo e(Auth::user()->user_type); ?>

                                    <?php endif; ?>
                                </small>
                            </p>
                        </li>
                        <li class="user-footer">
                            <?php if(Auth::check()): ?>
                                <a href="<?php echo e(url('/profile/user/'.Auth::user()->user_id)); ?>" class="btn btn-sm bg-blue btn-block">Profile</a>
                            <?php endif; ?>
                        </li>
                    </ul>
                </li>

                <li class="dropdown messages-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="notification_bell">
                        <i class="fa fa-bell"></i>
                    </a>
                    <ul class="dropdown-menu scale-up">
                        <li class="header">Notifications</li>
                        <li id="notification_list_header"></li>
                        <li class="footer"><a href="<?php echo e(url('/notification/')); ?>" id="view_all_notification">See all notification</a></li>
                    </ul>
                </li>

                <li>
                    <a href="<?php echo e(route('logout')); ?>"
                       onclick="event.preventDefault();
                                     document.getElementById('logout-form').submit();">
                        <i class="fa fa-power-off"></i>
                    </a>

                    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                        <?php echo csrf_field(); ?>
                    </form>
                </li>
            </ul>
        </div>
    </nav>
</header>
