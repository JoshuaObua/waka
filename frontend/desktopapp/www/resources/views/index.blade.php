@extends('layouts.app')

@section('title', config('company.name') . ' - Dashboard')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/plugins/jvectormap/jquery-jvectormap-2.0.3.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/plugins/charts-c3/plugin.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/plugins/morrisjs/morris.min.css') }}" />
    <style>
        .widget_2.big_icon.units::after {
            content: '\f1a3' !important; /* city / property */
            font-family: 'Material-Design-Iconic-Font' !important;
        }
        .widget_2.big_icon.users::after {
            content: '\f207' !important; /* accounts / users */
            font-family: 'Material-Design-Iconic-Font' !important;
        }
        .widget_2.big_icon.invoices::after {
            content: '\f224' !important; /* file-text / invoices */
            font-family: 'Material-Design-Iconic-Font' !important;
        }
        .widget_2.big_icon.collections::after {
            content: '\f19a' !important; /* balance-wallet / collections */
            font-family: 'Material-Design-Iconic-Font' !important;
        }
    </style>
@endsection

@section('content')
@php
    $collectionsPercent = 0;
    if ($kpis['collections']['previous'] > 0) {
        $collectionsPercent = min(100, intval(($kpis['collections']['current'] * 100) / $kpis['collections']['previous']));
    } else if ($kpis['collections']['current'] > 0) {
        $collectionsPercent = 100;
    }
@endphp

<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-6 col-sm-12">
            <h2>Dashboard</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row clearfix">
        <!-- 1. Rentable Units occupied vs vacant -->
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="card widget_2 big_icon units">
                <div class="body">
                    <h6>Units Occupied</h6>
                    <h2>{{ $kpis['units']['occupied'] }} <small class="info">of {{ $kpis['units']['total'] }}</small></h2>
                    <small>{{ $kpis['units']['vacant'] }} vacant units remaining</small>
                    <div class="progress">
                        <div class="progress-bar l-amber" role="progressbar" aria-valuenow="{{ $kpis['units']['percent'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $kpis['units']['percent'] }}%;"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 2. App Users tenants vs other users -->
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="card widget_2 big_icon users">
                <div class="body">
                    <h6>App Users</h6>
                    <h2>{{ $kpis['users']['tenants'] }} <small class="info">tenants</small></h2>
                    <small>{{ $kpis['users']['others'] }} staff users</small>
                    <div class="progress">
                        <div class="progress-bar l-blue" role="progressbar" aria-valuenow="{{ $kpis['users']['percent'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $kpis['users']['percent'] }}%;"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 3. Invoices paid vs overdue -->
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="card widget_2 big_icon invoices">
                <div class="body">
                    <h6>Invoices Issued</h6>
                    <h2>{{ $kpis['invoices']['paid'] }} <small class="info">paid</small></h2>
                    <small>{{ $kpis['invoices']['overdue'] }} overdue invoices pending</small>
                    <div class="progress">
                        <div class="progress-bar l-purple" role="progressbar" aria-valuenow="{{ $kpis['invoices']['percent'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $kpis['invoices']['percent'] }}%;"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 4. Total Collections current vs previous -->
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="card widget_2 big_icon collections">
                <div class="body">
                    <h6>Total Collections</h6>
                    <h2>{{ number_format($kpis['collections']['current']) }} <small class="info">UGX</small></h2>
                    <small>
                        @if($kpis['collections']['change_percent'] >= 0)
                            <i class="zmdi zmdi-trending-up text-success"></i> {{ number_format($kpis['collections']['change_percent'], 1) }}% higher than last month
                        @else
                            <i class="zmdi zmdi-trending-down text-danger"></i> {{ number_format(abs($kpis['collections']['change_percent']), 1) }}% lower than last month
                        @endif
                    </small>
                    <div class="progress">
                        <div class="progress-bar l-green" role="progressbar" aria-valuenow="{{ $collectionsPercent }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $collectionsPercent }}%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card">
                <div class="header">
                    <h2><strong><i class="zmdi zmdi-chart"></i> Sales</strong> Report</h2>
                </div>
                <div class="body mb-2">
                    <div id="m_area_chart" class="morris" style="height: 290px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/bundles/jvectormap.bundle.js') }}"></script>
    <script src="{{ asset('assets/bundles/sparkline.bundle.js') }}"></script>
    <script src="{{ asset('assets/bundles/c3.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/pages/index.js') }}"></script>
@endsection