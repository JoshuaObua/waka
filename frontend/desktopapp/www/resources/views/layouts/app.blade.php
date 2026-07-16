<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="description" content="Responsive Bootstrap 4 and web Application ui kit.">
    <title>@yield('title', config('company.name'))</title>
    <link rel="icon" href="{{ asset(config('company.favicon_url')) }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}">
    
    <!-- Page Specific Stylesheets -->
    @yield('styles')
    
    <!-- Custom Css -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.min.css') }}">
</head>

<body class="theme-blush">
    <script>
        (function() {
            var savedMode = localStorage.getItem('theme-mode');
            if (savedMode === 'dark') {
                document.body.classList.add('theme-dark');
            } else {
                document.body.classList.remove('theme-dark');
            }

            var savedSkin = localStorage.getItem('theme-skin');
            if (savedSkin) {
                document.body.className = document.body.className.replace(/\btheme-[a-z]+\b/g, '');
                document.body.classList.add('theme-' + savedSkin);
            }
        })();
    </script>

    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="m-t-30"><img class="zmdi-hc-spin" src="{{ asset('assets/images/loader.svg') }}" width="48" height="48" alt="{{ config('company.name') }}"></div>
            <p>Please wait...</p>
        </div>
    </div>

    <!-- Overlay For Sidebars -->
    <div class="overlay"></div>

    <!-- Main Search -->
    <div id="search">
        <button id="close" type="button" class="close btn btn-primary btn-icon btn-icon-mini btn-round">x</button>
        <form>
            <input type="search" value="" placeholder="Search..." />
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>

    <!-- Right Icon menu Sidebar -->
    <div class="navbar-right">
        <ul class="navbar-nav">
            <li><a href="#search" class="main_search" title="Search..."><i class="zmdi zmdi-search"></i></a></li>
            <li class="dropdown">
                <a href="javascript:void(0);" class="dropdown-toggle" title="App" data-toggle="dropdown" role="button"><i class="zmdi zmdi-apps"></i></a>
                <ul class="dropdown-menu slideUp2">
                    <li class="header">App Shortcuts</li>
                    <li class="body">
                        <ul class="menu app_sortcut list-unstyled">
                            <li>
                                <a href="javascript:void(0);">
                                    <div class="icon-circle mb-2 bg-blue"><i class="zmdi zmdi-camera"></i></div>
                                    <p class="mb-0">Photos</p>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);">
                                    <div class="icon-circle mb-2 bg-amber"><i class="zmdi zmdi-translate"></i></div>
                                    <p class="mb-0">Translate</p>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);">
                                    <div class="icon-circle mb-2 bg-green"><i class="zmdi zmdi-calendar"></i></div>
                                    <p class="mb-0">Calendar</p>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);">
                                    <div class="icon-circle mb-2 bg-purple"><i class="zmdi zmdi-account-calendar"></i></div>
                                    <p class="mb-0">Contacts</p>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);">
                                    <div class="icon-circle mb-2 bg-red"><i class="zmdi zmdi-tag"></i></div>
                                    <p class="mb-0">News</p>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);">
                                    <div class="icon-circle mb-2 bg-grey"><i class="zmdi zmdi-map"></i></div>
                                    <p class="mb-0">Maps</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="javascript:void(0);" class="dropdown-toggle" title="Notifications" data-toggle="dropdown" role="button"><i class="zmdi zmdi-notifications"></i>
                    <div class="notify"><span class="heartbit"></span><span class="point"></span></div>
                </a>
                <ul class="dropdown-menu slideUp2">
                    <li class="header">Notifications</li>
                    <li class="body">
                        <ul class="menu list-unstyled">
                            <li>
                                <a href="javascript:void(0);">
                                    <div class="icon-circle bg-blue"><i class="zmdi zmdi-account"></i></div>
                                    <div class="menu-info">
                                        <h4>8 New Members joined</h4>
                                        <p><i class="zmdi zmdi-time"></i> 14 mins ago </p>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);">
                                    <div class="icon-circle bg-amber"><i class="zmdi zmdi-shopping-cart"></i></div>
                                    <div class="menu-info">
                                        <h4>4 Sales made</h4>
                                        <p><i class="zmdi zmdi-time"></i> 22 mins ago </p>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="footer"> <a href="javascript:void(0);">View All Notifications</a> </li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button"><i class="zmdi zmdi-flag"></i>
                    <div class="notify"><span class="heartbit"></span><span class="point"></span></div>
                </a>
                <ul class="dropdown-menu slideUp2">
                    <li class="header">Tasks List <small class="float-right"><a href="javascript:void(0);">View All</a></small></li>
                    <li class="body">
                        <ul class="menu tasks list-unstyled">
                            <li>
                                <div class="progress-container progress-primary">
                                    <span class="progress-badge">PMS Operations</span>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="86" aria-valuemin="0" aria-valuemax="100" style="width: 86%;">
                                            <span class="progress-value">86%</span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li><a href="javascript:void(0);" class="app_calendar" title="Calendar"><i class="zmdi zmdi-calendar"></i></a></li>
            <li><a href="/documents" class="app_google_drive" title="File Manager"><i class="zmdi zmdi-google-drive"></i></a></li>
            <li><a href="javascript:void(0);" class="app_group_work" title="Group Work"><i class="zmdi zmdi-group-work"></i></a></li>
            <li><a href="javascript:void(0);" class="js-right-sidebar" title="Setting"><i class="zmdi zmdi-settings zmdi-hc-spin"></i></a></li>
            <li><a href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="mega-menu" title="Sign Out"><i class="zmdi zmdi-power"></i></a></li>
        </ul>
        <form id="logout-form" action="/logout" method="POST" style="display: none;">
            @csrf
        </form>
    </div>

    <!-- Left Sidebar -->
    <aside id="leftsidebar" class="sidebar">
        <div class="navbar-brand">
            <button class="btn-menu ls-toggle-btn" type="button"><i class="zmdi zmdi-menu"></i></button>
            <a href="/"><img src="{{ asset('assets/images/logo.svg') }}" width="25" alt="{{ config('company.name') }}"><span class="m-l-10">{{ config('company.name') }}</span></a>
        </div>
        <div class="menu">
            <ul class="list">
                <li>
                    <div class="user-info">
                        <a class="image" href="/users/{{ session('user_id', '') }}"><img src="{{ asset('assets/images/profile_av.jpg') }}" alt="User"></a>
                        <div class="detail">
                            <h4>{{ ucfirst(explode('@', session('user_email', 'User'))[0]) }}</h4>
                            <small>{{ implode(', ', session('user_roles', ['Guest'])) }}</small>                        
                        </div>
                    </div>
                </li>
                <li class="{{ Request::is('/') ? 'active' : '' }}"><a href="/"><i class="zmdi zmdi-home"></i><span>Dashboard</span></a></li>
                
                <li class="{{ Request::is('properties') || Request::is('units') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-city"></i><span>Properties & Units</span></a>
                    <ul class="ml-menu">
                        <li class="{{ Request::is('properties') ? 'active' : '' }}"><a href="/properties">Properties List</a></li>
                        <li class="{{ Request::is('units') ? 'active' : '' }}"><a href="/units">Rentable Units</a></li>
                    </ul>
                </li>
                
                <li class="{{ Request::is('tenants') || Request::is('leases') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-accounts"></i><span>Tenants & Leases</span></a>
                    <ul class="ml-menu">
                        <li class="{{ Request::is('tenants') ? 'active' : '' }}"><a href="/tenants">Tenants List</a></li>
                        <li class="{{ Request::is('leases') ? 'active' : '' }}"><a href="/leases">Lease Contracts</a></li>
                    </ul>
                </li>
                
                <li class="{{ Request::is('invoices') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-receipt"></i><span>Billing & Invoices</span></a>
                    <ul class="ml-menu">
                        <li class="{{ Request::is('invoices') ? 'active' : '' }}"><a href="/invoices">Invoices</a></li>
                    </ul>
                </li>
                
                <li class="{{ Request::is('wallets') || Request::is('transactions') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-balance-wallet"></i><span>Financials & Wallets</span></a>
                    <ul class="ml-menu">
                        <li class="{{ Request::is('wallets') ? 'active' : '' }}"><a href="/wallets">Wallets & Ledgers</a></li>
                        <li class="{{ Request::is('transactions') ? 'active' : '' }}"><a href="/transactions">Gateway Transactions</a></li>
                    </ul>
                </li>
                
                <li class="{{ Request::is('maintenance-requests') || Request::is('vendors') || Request::is('work-orders') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-wrench"></i><span>Maintenance & Vendors</span></a>
                    <ul class="ml-menu">
                        <li class="{{ Request::is('maintenance-requests') ? 'active' : '' }}"><a href="/maintenance-requests">Maintenance Requests</a></li>
                        <li class="{{ Request::is('work-orders') ? 'active' : '' }}"><a href="/work-orders">Work Orders</a></li>
                        <li class="{{ Request::is('vendors') ? 'active' : '' }}"><a href="/vendors">Vendors Directory</a></li>
                    </ul>
                </li>
                
                <li class="{{ Request::is('users') || Request::is('roles') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-settings"></i><span>User Management</span></a>
                    <ul class="ml-menu">
                        <li class="{{ Request::is('users') ? 'active' : '' }}"><a href="/users">Users List</a></li>
                        <li class="{{ Request::is('roles') ? 'active' : '' }}"><a href="/roles">Roles & Permissions</a></li>
                    </ul>
                </li>

                <li class="{{ Request::is('utility-meters') || Request::is('utility-tariffs') || Request::is('utility-bills') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-flash"></i><span>Utility Billing</span></a>
                    <ul class="ml-menu">
                        <li class="{{ Request::is('utility-meters') ? 'active' : '' }}"><a href="/utility-meters">Meter Settings</a></li>
                        <li class="{{ Request::is('utility-tariffs') ? 'active' : '' }}"><a href="/utility-tariffs">Tariffs Directory</a></li>
                        <li class="{{ Request::is('utility-bills') ? 'active' : '' }}"><a href="/utility-bills">Bills & Invoices</a></li>
                    </ul>
                </li>

                <li class="{{ Request::is('visitors') ? 'active' : '' }}">
                    <a href="/visitors"><i class="zmdi zmdi-walk"></i><span>Visitor Management</span></a>
                </li>

                <li class="{{ Request::is('webhooks') || Request::is('audit-logs') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-dns"></i><span>System & Integrations</span></a>
                    <ul class="ml-menu">
                        <li class="{{ Request::is('webhooks') ? 'active' : '' }}"><a href="/webhooks">Webhook Subscriptions</a></li>
                        <li class="{{ Request::is('audit-logs') ? 'active' : '' }}"><a href="/audit-logs">Audit Trail Logs</a></li>
                    </ul>
                </li>

                <li class="{{ Request::is('graphql') ? 'active' : '' }}">
                    <a href="/graphql"><i class="zmdi zmdi-code-shortcut"></i><span>GraphQL Explorer</span></a>
                </li>
            </ul>
        </div>
    </aside>

    <!-- Right Sidebar -->
    <aside id="rightsidebar" class="right-sidebar">
        <ul class="nav nav-tabs sm">
            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#setting"><i class="zmdi zmdi-settings zmdi-hc-spin"></i></a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#chat"><i class="zmdi zmdi-comments"></i></a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="setting">
                <div class="slim_scroll">
                    <div class="card">
                        <h6>Theme Option</h6>
                        <div class="light_dark">
                            <div class="radio">
                                <input type="radio" name="radio1" id="lighttheme" value="light" checked="">
                                <label for="lighttheme">Light Mode</label>
                            </div>
                            <div class="radio mb-0">
                                <input type="radio" name="radio1" id="darktheme" value="dark">
                                <label for="darktheme">Dark Mode</label>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <h6>Color Skins</h6>
                        <ul class="choose-skin list-unstyled">
                            <li data-theme="purple"><div class="purple"></div></li>                   
                            <li data-theme="blue"><div class="blue"></div></li>
                            <li data-theme="cyan"><div class="cyan"></div></li>
                            <li data-theme="green"><div class="green"></div></li>
                            <li data-theme="orange"><div class="orange"></div></li>
                            <li data-theme="blush" class="active"><div class="blush"></div></li>
                        </ul>                    
                    </div>
                </div>                
            </div>       
            <div class="tab-pane right_chat" id="chat">
                <div class="slim_scroll">
                    <div class="card">
                        <ul class="list-unstyled">
                            <li class="online">
                                <a href="javascript:void(0);">
                                    <div class="media">
                                        <img class="media-object " src="{{ asset('assets/images/xs/avatar4.jpg') }}" alt="">
                                        <div class="media-body">
                                            <span class="name">Sophia <small class="float-right">11:00AM</small></span>
                                            <span class="message">Standard platform updates scheduled.</span>
                                            <span class="badge badge-outline status"></span>
                                        </div>
                                    </div>
                                </a>                            
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Yield -->
    <section class="content">
        <div class="body_scroll">
            @yield('content')
        </div>
    </section>

    <!-- Global Base JS bundles -->
    <script src="{{ asset('assets/bundles/libscripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/bundles/vendorscripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/bundles/mainscripts.bundle.js') }}"></script>
    
    <!-- Page Specific Scripts -->
    @yield('scripts')

    <script>
        $(document).ready(function() {
            // Bind navigation link clicks on sidebar to show preloader instantly
            $('#leftsidebar a').on('click', function(e) {
                var href = $(this).attr('href');
                
                // Only intercept actual links (not menu-toggles, empty links, or javascript links)
                if (href && href !== '#' && href !== 'javascript:void(0);' && !$(this).hasClass('menu-toggle')) {
                    // Show full-screen loader
                    $('.page-loader-wrapper').fadeIn(150);
                }
            });

            // Synchronize theme mode selector status
            var savedMode = localStorage.getItem('theme-mode') || 'light';
            if (savedMode === 'dark') {
                $('#darktheme').prop('checked', true);
            } else {
                $('#lighttheme').prop('checked', true);
            }

            // Synchronize active color accent indicator state
            var savedSkin = localStorage.getItem('theme-skin') || 'blush';
            $('.choose-skin li').removeClass('active');
            $('.choose-skin li[data-theme="' + savedSkin + '"]').addClass('active');

            // Listen to theme mode radio clicks to update storage
            $('.light_dark input').on('change', function() {
                var val = $(this).val();
                localStorage.setItem('theme-mode', val);
            });

            // Listen to theme skin selection clicks to update storage
            $('.choose-skin li').on('click', function() {
                var skin = $(this).data('theme');
                if (skin) {
                    localStorage.setItem('theme-skin', skin);
                }
            });
        });
    </script>

</body>
</html>
