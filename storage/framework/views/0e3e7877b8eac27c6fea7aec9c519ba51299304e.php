<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="image float-left">
                <?php if(Auth::check()): ?>
                    <img src="<?php echo e(asset(Auth::user()->photo)); ?>" class="rounded" alt="User Image">
                <?php endif; ?>
            </div>
            <div class="info float-left">
                <p>
                    <?php if(Auth::check()): ?>
                        <?php echo e(Auth::user()->firstname.' '.Auth::user()->lastname); ?>

                    <?php endif; ?>
                </p>
                <a href="#">
                    
                    <?php if(Auth::check()): ?>

                        <?php echo e(Auth::user()->user_types->description); ?>

                    <?php endif; ?>
                </a>
            </div>
        </div>

        <?php
            $json  = json_encode($user_accesses);
            $array = json_decode($json, true);
            $category = array_column($array, 'category');
            $url = ""; $slug = ""; $route="";
        ?>

        <ul class="sidebar-menu" data-widget="tree">
            <?php if(Auth::user()->user_category == 'PRODUCTION'): ?>
                <li class="<?php echo e(Request::is('prod/dashboard') ? ' active' : null); ?>">
                    <a href="<?php echo e(route('prod.dashboard')); ?>">
                        <i class="fa fa-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            <?php else: ?>
                <li class="<?php echo e(Request::is('dashboard') ? ' active' : null); ?>">
                    <a href="<?php echo e(route('dashboard')); ?>">
                        <i class="fa fa-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            <?php endif; ?>
            

            <?php if(in_array("System Maintenance",$category)): ?>

                <li class="treeview <?php echo e(Request::is('masters/*') ? ' active' : null); ?>">
                    <a href="#">
                        <i class="fa fa-laptop"></i>
                        <span>System Maintenance</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <?php $__currentLoopData = $user_accesses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $access): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $slug = 'masters/'.strtolower(str_replace(' ', '-', $access->title));
                                $route = str_replace('/', '.', $slug);
                            ?>
                            <?php if($access->category == 'System Maintenance'): ?>
                                <li class=" <?php echo e(Request::is($slug) ? ' active' : null); ?>">
                                    <a href="<?php echo e(route($route)); ?>">
                                        <i class="fa fa-circle"></i>
                                        <span><?php echo e($access->title); ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </li>
            <?php endif; ?>


            <?php if(in_array("Transaction",$category)): ?>
                
                <li class="treeview <?php echo e((Request::is('prod/production-output') || Request::is('prod/transfer-item') || Request::is('prod/receive-item') || Request::is('transaction/*')) ? ' active' : null); ?>">
                    <a href="#">
                        <i class="fa fa-handshake-o"></i>
                        <span>Transaction</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <?php if(Auth::user()->user_category == 'PRODUCTION' || Auth::user()->user_category == 'ALL'): ?>
                            <?php $__currentLoopData = $user_accesses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $access): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $slug = strtolower(str_replace(' ', '-', $access->title));
                                    $route = str_replace('/', '.', $slug);
                                ?>
                                <?php if($access->category == 'Transaction'): ?>
                                    <?php
                                        if($access->user_category == 'PRODUCTION') {
                                    ?>
                                            <li class=" <?php echo e(Request::is('prod/'.$slug) ? ' active' : null); ?>">
                                                <a href="<?php echo e(route('prod.'.$route)); ?>">
                                                    <i class="fa fa-circle"></i>
                                                    <span><?php echo e($access->title); ?></span>
                                                </a>
                                            </li>
                                    <?php
                                        }
                                    ?>

                                        
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        <?php if(Auth::user()->user_category == 'OFFICE' || Auth::user()->user_category == 'ALL'): ?>
                            <?php $__currentLoopData = $user_accesses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $access): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $slug = 'transaction/'.strtolower(str_replace(' ', '-', $access->title));
                                    $route = str_replace('/', '.', $slug);
                                ?>
                                <?php if($access->category == 'Transaction'): ?>
                                    <?php
                                        if($access->user_category == 'OFFICE') {
                                    ?>
                                        <li class=" <?php echo e(Request::is($slug) ? ' active' : null); ?>">
                                            <a href="<?php echo e(route($route)); ?>">
                                                <i class="fa fa-circle"></i>
                                                <span><?php echo e($access->title); ?></span>
                                            </a>
                                        </li>
                                    <?php
                                        }
                                    ?>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>


            <?php if(in_array("Reports",$category)): ?>
                
                <li class="treeview <?php echo e(( Request::is('prod/reports/*') || Request::is('reports/*') )  ? ' active' : null); ?>">
                    <a href="#">
                        <i class="fa fa-file-excel-o"></i>
                        <span>Reports</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <?php if(Auth::user()->user_category == 'PRODUCTION' || Auth::user()->user_category == 'ALL'): ?>
                            <?php $__currentLoopData = $user_accesses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $access): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $slug = 'prod.reports/'.strtolower(str_replace(' ', '-', $access->title));
                                    $route = str_replace('/', '.', $slug);
                                ?>
                                <?php if($access->category == 'Reports'): ?>
                                    <?php
                                        if($access->user_category == 'PRODUCTION') {
                                    ?>
                                        <li class=" <?php echo e(Request::is($slug) ? ' active' : null); ?>">
                                            <a href="<?php echo e(route($route)); ?>">
                                                <i class="fa fa-circle"></i>
                                                <span><?php echo e($access->title); ?></span>
                                            </a>
                                        </li>
                                    <?php
                                        }
                                    ?>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        <?php if(Auth::user()->user_category == 'OFFICE' || Auth::user()->user_category == 'ALL'): ?>
                            <?php $__currentLoopData = $user_accesses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $access): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $slug = 'reports/'.strtolower(str_replace(' ', '-', $access->title));
                                    $route = str_replace('/', '.', $slug);
                                ?>
                                <?php if($access->category == 'Reports'): ?>
                                    <?php
                                        if($access->user_category == 'OFFICE') {
                                    ?>
                                            <li class=" <?php echo e(Request::is($slug) ? ' active' : null); ?>">
                                                <a href="<?php echo e(route($route)); ?>">
                                                    <i class="fa fa-circle"></i>
                                                    <span><?php echo e($access->title); ?></span>
                                                </a>
                                            </li>
                                    <?php
                                        }
                                    ?>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>


            <?php if(Auth::user()->is_admin == 1): ?>
                <?php if(in_array("Administrator",$category)): ?>

                    <li class="treeview <?php echo e(Request::is('admin/*') ? ' active' : null); ?>">
                        <a href="#">
                            <i class="fa fa-user"></i>
                            <span>Admininistrator</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <?php $__currentLoopData = $user_accesses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $access): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $slug = 'admin/'.strtolower(str_replace(' ', '-', $access->title));
                                    $route = str_replace('/', '.', $slug);
                                ?>
                                <?php if($access->category == 'Administrator'): ?>
                                    <li class=" <?php echo e(Request::is($slug) ? ' active' : null); ?>">
                                        <a href="<?php echo e(route($route)); ?>">
                                            <i class="fa fa-circle"></i>
                                            <span><?php echo e($access->title); ?></span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </li>
                <?php endif; ?>
            <?php endif; ?>

        </ul>

        
    </section>
</aside>
