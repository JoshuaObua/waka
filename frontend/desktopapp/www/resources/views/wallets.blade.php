@extends('layouts.app')

@section('title', config('company.name') . ' - Wallets & Ledgers')

@section('styles')
    <!-- JQuery DataTable Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-datatable/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-datatable/fixedeader/dataTables.fixedcolumns.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-datatable/fixedeader/dataTables.fixedheader.bootstrap4.min.css') }}">
    <style>
        .badge-deposit {
            background-color: #28a745;
            color: #fff;
        }
        .badge-rent {
            background-color: #007bff;
            color: #fff;
        }
        .badge-disburse {
            background-color: #dc3545;
            color: #fff;
        }
        .text-mono {
            font-family: monospace;
            font-size: 13px;
        }
    </style>
@endsection

@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-6 col-sm-12">
            <h2>Wallets & Ledgers</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">Financials & Wallets</a></li>
                <li class="breadcrumb-item active">Wallets & Ledgers</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
            <a href="/wallets/create" class="btn btn-info float-right mr-2"><i class="zmdi zmdi-money"></i> New Transaction</a>
        </div>
    </div>
</div>

<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <i class="zmdi zmdi-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <i class="zmdi zmdi-alert-circle"></i> {{ $errors->first() }}
        </div>
    @endif

    <!-- Financial Summary Cards -->
    <div class="row clearfix">
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card state_w1">
                <div class="body d-flex justify-content-between align-items-center">
                    <div>
                        <h5>{{ number_format($gatewayBalance['balance'] ?? 0) }} UGX</h5>
                        <span>ioTec Gateway Cash Pool</span>
                    </div>
                    <div class="spark_icon bg-green text-white" style="padding: 10px; border-radius: 50%;">
                        <i class="zmdi zmdi-cloud-upload zmdi-hc-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card state_w1">
                <div class="body d-flex justify-content-between align-items-center">
                    <div>
                        @php
                            $landlordWallet = collect($wallets)->firstWhere('owner_type', 'landlord');
                        @endphp
                        <h5>{{ number_format($landlordWallet['balance'] ?? 0) }} UGX</h5>
                        <span>Landlord Escrow Wallet</span>
                    </div>
                    <div class="spark_icon bg-blue text-white" style="padding: 10px; border-radius: 50%;">
                        <i class="zmdi zmdi-balance zmdi-hc-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card state_w1">
                <div class="body d-flex justify-content-between align-items-center">
                    <div>
                        @php
                            $tenantWalletsSum = collect($wallets)->where('owner_type', 'tenant')->sum('balance');
                        @endphp
                        <h5>{{ number_format($tenantWalletsSum) }} UGX</h5>
                        <span>Aggregated Tenant Wallets</span>
                    </div>
                    <div class="spark_icon bg-amber text-white" style="padding: 10px; border-radius: 50%;">
                        <i class="zmdi zmdi-accounts-list-alt zmdi-hc-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ledger Entries Table -->
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Double-Entry</strong> Financial Ledger</h2>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-hover js-exportable dataTable mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Reference Description</th>
                                    <th>Type</th>
                                    <th class="text-right">Amount (UGX)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ledgerEntries as $entry)
                                    <tr>
                                        <td>{{ date('Y-m-d H:i', strtotime($entry['created_at'])) }}</td>
                                        <td>
                                            <div class="font-weight-bold">{{ $entry['description'] }}</div>
                                            <span class="text-muted text-mono">{{ $entry['id'] }}</span>
                                        </td>
                                        <td>
                                            @if(($entry['entry_type'] ?? '') === 'wallet_topup')
                                                <span class="badge badge-deposit">Wallet Topup</span>
                                            @elseif(($entry['entry_type'] ?? '') === 'rent_payment')
                                                <span class="badge badge-rent">Rent Invoice</span>
                                            @else
                                                <span class="badge badge-disburse">{{ ucfirst(str_replace('_', ' ', $entry['entry_type'] ?? 'Transaction')) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-right font-weight-bold text-success">
                                            {{ number_format($entry['amount']) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <!-- Jquery DataTable Plugin Js -->
    <script src="{{ asset('assets/bundles/datatablescripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-datatable/buttons/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-datatable/buttons/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-datatable/buttons/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-datatable/buttons/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-datatable/buttons/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-datatable/buttons/buttons.print.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable with Export Capabilities
            $('.js-exportable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                order: [[0, "desc"]],
                pageLength: 10
            });
        });
    </script>
@endsection
