<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="index" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ URL::asset('assets/images/logo-dark-sm.png') }}" alt="" height="32">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('assets/images/logo-dark.png') }}" alt="" height="40">
            </span>
        </a>

        <a href="index" class="logo logo-light">
            <span class="logo-lg">
                <img src="{{ URL::asset('assets/images/logo-light.png') }}" alt="" height="35">
            </span>
            <span class="logo-sm">
                <img src="{{ URL::asset('assets/images/logo-light-sm.png') }}" alt="" height="38">
            </span>
        </a>
    </div>

    <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect vertical-menu-btn">
        <i class="bx bx-menu align-middle"></i>
    </button>

    <div data-simplebar class="sidebar-menu-scroll">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" data-key="t-menu">Dashboard</li>

                <li>
                    <a href="{{ route('home') }}">
                        <i class="bx bx-home-alt icon nav-icon"></i>
                        <span class="menu-item" data-key="">Dashboard</span>
                    </a>
                </li>

                {{-- <li>
                    <a href="javascript: void(0);">
                        <i class="bx bx-home-alt icon nav-icon"></i>
                        <span class="menu-item" data-key="t-dashboard">Dashboard</span>
                        <span class="badge rounded-pill bg-primary">2</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="index" data-key="t-ecommerce">Ecommerce</a></li>
                        <li><a href="dashboard-sales" data-key="t-sales">Sales</a></li>
                    </ul>
                </li> --}}

                <li class="menu-title" data-key="">Arisan</li>

                <li>
                    <a href="{{ route('groups') }}">
                        <i class="bx bx-group icon nav-icon"></i>
                        <span class="menu-item" data-key="">Groups</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('members') }}">
                        <i class="bx bx-user-pin icon nav-icon"></i>
                        <span class="menu-item" data-key="">Members</span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('feedback') }}">
                        <i class="bx bx-message-dots icon nav-icon"></i>
                        <span class="menu-item" data-key="">Feedback</span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('subscriptions') }}">
                        <i class="bx bx-gift icon nav-icon"></i>
                        <span class="menu-item" data-key="">Subscription</span>
                    </a>
                </li>

                <li class="menu-title" data-key="t-layouts">Settings</li>

                <li>
                    <a href="{{route('setting_configurations')}}">
                        <i class="bx bx-cog icon nav-icon"></i>
                        <span class="menu-item" data-key="">Configuration</span>
                    </a>
                </li>

                <li>
                    <a href="/info">
                        <i class="bx bx-info-circle icon nav-icon"></i>
                        <span class="menu-item" data-key="">About & Info</span>
                    </a>
                </li>

                <li class="menu-title" data-key="t-layouts">Account</li>

                <li>
                    <a href="{{ route('users') }}">
                        <i class="bx bx-ghost icon nav-icon"></i>
                        <span class="menu-item" data-key="">Users</span>
                    </a>
                </li>

                <li>
                    <a href="{{route('profile')}}">
                        <i class="bx bx-user-circle icon nav-icon"></i>
                        <span class="menu-item" data-key="">Profile</span>
                    </a>
                </li>

                <li>
                    <a href="javascript:void();"
                        onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
                        <i class="bx bx-log-out icon nav-icon"></i>
                        <span class="menu-item" data-key="">Logout</span>
                    </a>

                    <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
