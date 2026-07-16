@extends('layouts.app')

@section('title', config('company.name') . ' - Gateway Transactions')

@section('styles')
    <!-- JQuery DataTable Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-datatable/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-datatable/fixedeader/dataTables.fixedcolumns.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-datatable/fixedeader/dataTables.fixedheader.bootstrap4.min.css') }}">
    <!-- Sweetalert Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert/sweetalert.css') }}">
    <style>
        .badge-success-custom {
            background-color: #28a745;
            color: #fff;
        }
        .badge-pending-custom {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-danger-custom {
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
            <h2>Payment Gateway Transactions</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">Financials & Wallets</a></li>
                <li class="breadcrumb-item active">Gateway Transactions</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
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

    <!-- Transaction Summary Stats -->
    <div class="row clearfix">
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card state_w1">
                <div class="body d-flex justify-content-between align-items-center">
                    <div>
                        <h5>{{ count($transactions) }}</h5>
                        <span>Total Gateway Logs</span>
                    </div>
                    <div class="spark_icon bg-blue text-white" style="padding: 10px; border-radius: 50%;">
                        <i class="zmdi zmdi-receipt zmdi-hc-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card state_w1">
                <div class="body d-flex justify-content-between align-items-center">
                    <div>
                        @php
                            $successSum = collect($transactions)->where('status', 'completed')->sum('amount');
                        @endphp
                        <h5>{{ number_format($successSum) }} UGX</h5>
                        <span>Successful Collections</span>
                    </div>
                    <div class="spark_icon bg-green text-white" style="padding: 10px; border-radius: 50%;">
                        <i class="zmdi zmdi-check-all zmdi-hc-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card state_w1">
                <div class="body d-flex justify-content-between align-items-center">
                    <div>
                        @php
                            $pendingCount = collect($transactions)->where('status', 'pending')->count();
                        @endphp
                        <h5>{{ $pendingCount }}</h5>
                        <span>Pending Verification</span>
                    </div>
                    <div class="spark_icon bg-amber text-white" style="padding: 10px; border-radius: 50%;">
                        <i class="zmdi zmdi-time-restore zmdi-hc-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions List -->
    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Gateway</strong> Ledger Logs</h2>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-hover js-exportable dataTable mb-0">
                            <thead>
                                <tr>
                                    <th>Transaction Ref</th>
                                    <th>Date</th>
                                    <th>Method</th>
                                    <th class="text-right">Amount (UGX)</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $txn)
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold text-mono">{{ $txn['provider_reference'] ?? 'N/A' }}</div>
                                            <small class="text-muted text-mono">{{ $txn['id'] }}</small>
                                        </td>
                                        <td>{{ date('Y-m-d H:i:s', strtotime($txn['payment_date'] ?? ($txn['created_at'] ?? 'now'))) }}</td>
                                        <td>
                                            <span class="text-uppercase text-mono">{{ str_replace('_', ' ', $txn['payment_method'] ?? 'mobile_money') }}</span>
                                        </td>
                                        <td class="text-right font-weight-bold text-dark">
                                            {{ number_format($txn['amount']) }}
                                        </td>
                                        <td>
                                            @if(($txn['status'] ?? '') === 'completed')
                                                <span class="badge badge-success-custom">Success</span>
                                            @elseif(($txn['status'] ?? '') === 'pending')
                                                <span class="badge badge-pending-custom">Pending</span>
                                            @else
                                                <span class="badge badge-danger-custom">{{ ucfirst($txn['status'] ?? 'failed') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if(($txn['status'] ?? '') === 'pending')
                                                <form action="/transactions/{{ $txn['id'] }}/sync" method="POST" class="d-inline sync-form">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-info waves-effect" title="Force check and sync state">
                                                        <i class="zmdi zmdi-refresh"></i> Sync Status
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted"><i class="zmdi zmdi-check"></i> Settled</span>
                                            @endif
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
    <!-- Sweetalert Plugin Js -->
    <script src="{{ asset('assets/plugins/sweetalert/sweetalert.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('.js-exportable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                order: [[1, "desc"]],
                pageLength: 10
            });

            // Prevent double click and show circular progress spinner
            $('form').on('submit', function() {
                var $form = $(this);
                var $btn = $form.find('button[type="submit"]');
                
                if ($btn.data('submitting')) {
                    return false;
                }
                
                $btn.data('submitting', true);
                $btn.prop('disabled', true);
                
                var originalHtml = $btn.html();
                $btn.data('original-html', originalHtml);
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Syncing with ioTec...');
            });
        });
    </script>
@endsection
