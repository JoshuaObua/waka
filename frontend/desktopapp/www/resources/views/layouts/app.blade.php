<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="description" content="Responsive Bootstrap 4 and web Application ui kit.">
    <title>@yield('title', config('company.name'))</title>
    <link rel="icon" href="{{ asset(config('company.favicon_url')) }}" type="image/x-icon">
    <link rel="stylesheet" href="assets/plugins/bootstrap/css/bootstrap.min.css">
    
    <!-- Page Specific Stylesheets -->
    @yield('styles')
    
    <!-- Custom Css -->
    <link rel="stylesheet" href="assets/css/style.min.css">
</head>

<body class="theme-blush">

    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="m-t-30"><img class="zmdi-hc-spin" src="assets/images/loader.svg" width="48" height="48" alt="{{ config('company.name') }}"></div>
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
            <a href="/"><img src="assets/images/logo.svg" width="25" alt="{{ config('company.name') }}"><span class="m-l-10">{{ config('company.name') }}</span></a>
        </div>
        <div class="menu">
            <ul class="list">
                <li>
                    <div class="user-info">
                        <a class="image" href="profile.html"><img src="assets/images/profile_av.jpg" alt="User"></a>
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
                
                <li class="{{ Request::is('users') || Request::is('roles') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-settings"></i><span>User Management</span></a>
                    <ul class="ml-menu">
                        <li class="{{ Request::is('users') ? 'active' : '' }}"><a href="/users">Users List</a></li>
                        <li class="{{ Request::is('roles') ? 'active' : '' }}"><a href="/roles">Roles & Permissions</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </aside>

    <!-- Main Content Yield -->
    <section class="content">
        <div class="body_scroll">
            @yield('content')
        </div>
    </section>

    <!-- Global Base JS bundles -->
    <script src="assets/bundles/libscripts.bundle.js"></script>
    <script src="assets/bundles/vendorscripts.bundle.js"></script>
    <script src="assets/bundles/mainscripts.bundle.js"></script>
    
    <!-- Page Specific Scripts -->
    @yield('scripts')

</body>
</html>
