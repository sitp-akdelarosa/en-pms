<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="image float-left">
                @if (Auth::check())
                    <img src="{{ asset(Auth::user()->photo) }}" class="rounded" alt="User Image">
                @endif
            </div>
            <div class="info float-left">
                <p>
                    @if (Auth::check())
                        {{ Auth::user()->firstname.' '.Auth::user()->lastname }}
                    @endif
                </p>
                <a href="#">
                    {{-- <i class="fa fa-circle text-success"></i> --}}
                    @if (Auth::check())

                        {{ Auth::user()->user_types->description }}
                    @endif
                </a>
            </div>
        </div>

        <?php
            $json  = json_encode($user_accesses);
            $array = json_decode($json, true);
            $category = array_column($array, 'category');
            $url = ""; $slug = ""; $route="";

            $user_accecss = \DB::table('users as u')
                                ->join('admin_user_types as ut','u.user_type','=','ut.id')
                                ->select('ut.description','ut.category')
                                ->where('u.id',Auth::user()->id)
                                ->first();
        ?>  

        <ul class="sidebar-menu" data-widget="tree">
            @if ($user_accecss->category == 'PRODUCTION')
                <li class="{{ Request::is('prod/dashboard') ? ' active' : null }}">
                    <a href="{{ route('prod.dashboard') }}">
                        <i class="fa fa-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            @else
                <li class="{{ Request::is('dashboard') ? ' active' : null }}">
                    <a href="{{ route('dashboard') }}">
                        <i class="fa fa-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            @endif
            

            @if (in_array("System Maintenance",$category))

                <li class="treeview {{ Request::is('masters/*') ? ' active' : null }}">
                    <a href="#">
                        <i class="fa fa-laptop"></i>
                        <span>System Maintenance</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @foreach ($user_accesses as $access)
                            <?php
                                $slug = 'masters/'.strtolower(str_replace(' ', '-', $access->title));
                                $route = str_replace('/', '.', $slug);
                            ?>
                            @if ($access->category == 'System Maintenance')
                                <li class=" {{ Request::is($slug) ? ' active' : null }}">
                                    <a href="{{ route($route) }}">
                                        <i class="fa fa-circle"></i>
                                        <span>{{$access->title}}</span>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </li>
            @endif


            @if (in_array("Transaction",$category))
                
                <li class="treeview {{ (Request::is('prod/production-output') || Request::is('prod/transfer-item') || Request::is('prod/receive-item') || Request::is('transaction/*')) ? ' active' : null }}">
                    <a href="#">
                        <i class="fa fa-handshake-o"></i>
                        <span>Transaction</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @if (Auth::user()->user_category == 'PRODUCTION' || Auth::user()->user_category == 'ALL')
                            @foreach ($user_accesses as $access)
                                <?php
                                    $slug = strtolower(str_replace(' ', '-', $access->title));
                                    $route = str_replace('/', '.', $slug);
                                ?>
                                @if ($access->category == 'Transaction')
                                    <?php
                                        if($access->user_category == 'PRODUCTION') {
                                    ?>
                                            <li class=" {{ Request::is('prod/'.$slug) ? ' active' : null }}">
                                                <a href="{{ route('prod.'.$route) }}">
                                                    <i class="fa fa-circle"></i>
                                                    <span>{{$access->title}}</span>
                                                </a>
                                            </li>
                                    <?php
                                        }
                                    ?>

                                        
                                @endif
                            @endforeach
                        @endif
                        @if (Auth::user()->user_category == 'OFFICE' || Auth::user()->user_category == 'ALL')
                            @foreach ($user_accesses as $access)
                                <?php
                                    $slug = 'transaction/'.strtolower(str_replace(' ', '-', $access->title));
                                    $route = str_replace('/', '.', $slug);
                                ?>
                                @if ($access->category == 'Transaction')
                                    <?php
                                        if($access->user_category == 'OFFICE') {
                                    ?>
                                        <li class=" {{ Request::is($slug) ? ' active' : null }}">
                                            <a href="{{ route($route) }}">
                                                <i class="fa fa-circle"></i>
                                                <span>{{$access->title}}</span>
                                            </a>
                                        </li>
                                    <?php
                                        }
                                    ?>
                                @endif
                            @endforeach
                        @endif
                    </ul>
                </li>
            @endif


            @if (in_array("Reports",$category))
                
                <li class="treeview {{ ( Request::is('prod/reports/*') || Request::is('reports/*') )  ? ' active' : null }}">
                    <a href="#">
                        <i class="fa fa-file-excel-o"></i>
                        <span>Reports</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @if (Auth::user()->user_category == 'PRODUCTION' || Auth::user()->user_category == 'ALL')
                            @foreach ($user_accesses as $access)
                                <?php
                                    $slug = 'prod.reports/'.strtolower(str_replace(' ', '-', $access->title));
                                    $route = str_replace('/', '.', $slug);
                                ?>
                                @if ($access->category == 'Reports')
                                    <?php
                                        if($access->user_category == 'PRODUCTION') {
                                    ?>
                                        <li class=" {{ Request::is($slug) ? ' active' : null }}">
                                            <a href="{{ route($route) }}">
                                                <i class="fa fa-circle"></i>
                                                <span>{{$access->title}}</span>
                                            </a>
                                        </li>
                                    <?php
                                        }
                                    ?>
                                @endif
                            @endforeach
                        @endif
                        @if (Auth::user()->user_category == 'OFFICE' || Auth::user()->user_category == 'ALL')
                            @foreach ($user_accesses as $access)
                                <?php
                                    $slug = 'reports/'.strtolower(str_replace(' ', '-', $access->title));
                                    $route = str_replace('/', '.', $slug);
                                ?>
                                @if ($access->category == 'Reports')
                                    <?php
                                        if($access->user_category == 'OFFICE') {
                                    ?>
                                            <li class=" {{ Request::is($slug) ? ' active' : null }}">
                                                <a href="{{ route($route) }}">
                                                    <i class="fa fa-circle"></i>
                                                    <span>{{$access->title}}</span>
                                                </a>
                                            </li>
                                    <?php
                                        }
                                    ?>
                                @endif
                            @endforeach
                        @endif
                    </ul>
                </li>
            @endif


            @if (Auth::user()->is_admin == 1)
                @if (in_array("Administrator",$category))

                    <li class="treeview {{ Request::is('admin/*') ? ' active' : null }}">
                        <a href="#">
                            <i class="fa fa-user"></i>
                            <span>Admininistrator</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @foreach ($user_accesses as $access)
                                <?php
                                    $slug = 'admin/'.strtolower(str_replace(' ', '-', $access->title));
                                    $route = str_replace('/', '.', $slug);
                                ?>
                                @if ($access->category == 'Administrator')
                                    <li class=" {{ Request::is($slug) ? ' active' : null }}">
                                        <a href="{{ route($route) }}">
                                            <i class="fa fa-circle"></i>
                                            <span>{{$access->title}}</span>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </li>
                @endif
            @endif

        </ul>

        
    </section>
</aside>
