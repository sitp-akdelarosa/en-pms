<header class="main-header">
    <a href="{{ url('/') }}" class="logo">
        <span class="logo-mini">
            <img src="{{ asset('images/logo2.png') }}" class="img-fluid">
        </span>
        <span class="logo-lg">
            <div class="logo-wrapper">
                <img src="{{ asset('images/logo2.png') }}" class="img-fluid">
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
                        @if (Auth::check())
                            <img src="{{ asset(Auth::user()->photo) }}" class="user-image rounded" alt="User Image">
                        @endif
                    </a>
                    <ul class="dropdown-menu scale-up">
                        <li class="user-header">
                            @if (Auth::check())
                                <img src="{{ asset(Auth::user()->photo) }}" class="rounded float-left" alt="User Image">
                            @endif

                            <p>
                                @if (Auth::check())
                                    {{ Auth::user()->firstname.' '.Auth::user()->lastname }}
                                @endif

                                <small>
                                    @if (Auth::check())
                                        {{ Auth::user()->user_type }}
                                    @endif
                                </small>
                            </p>
                        </li>
                        <li class="user-footer">
                            @if (Auth::check())
                                <a href="{{ url('/profile/user/'.Auth::user()->user_id) }}" class="btn btn-sm bg-blue btn-block">Profile</a>
                            @endif
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
                        <li class="footer"><a href="{{ url('/notification/') }}" id="view_all_notification">See all notification</a></li>
                    </ul>
                </li>

                <li>
                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                                     document.getElementById('logout-form').submit();">
                        <i class="fa fa-power-off"></i>
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </nav>
</header>
