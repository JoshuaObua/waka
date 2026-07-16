@extends('layouts.app')

@section('title', config('company.name') . ' - Leases & Contracts')

@section('styles')
    <!-- JQuery DataTable Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-datatable/dataTables.bootstrap4.min.css') }}">
    <!-- Sweetalert Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert/sweetalert.css') }}">
    <style>
        .badge-approved {
            background-color: #28a745;
            color: #fff;
        }
        .badge-pending {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-terminated {
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
            <h2>Lease Agreements & Contracts</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">Tenants & Leases</a></li>
                <li class="breadcrumb-item active">Lease List</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <a href="/leases/create" class="btn btn-primary float-right waves-effect m-l-10">
                <i class="zmdi zmdi-plus"></i> Create New Lease
            </a>
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

    <!-- Lease Summary Row -->
    <div class="row clearfix">
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card state_w1">
                <div class="body d-flex justify-content-between align-items-center">
                    <div>
                        <h5>{{ count($leases) }}</h5>
                        <span>Total Agreements</span>
                    </div>
                    <div class="spark_icon bg-blue text-white" style="padding: 10px; border-radius: 50%;">
                        <i class="zmdi zmdi-file-text zmdi-hc-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card state_w1">
                <div class="body d-flex justify-content-between align-items-center">
                    <div>
                        @php
                            $activeCount = collect($leases)->where('status', 'approved')->count();
                        @endphp
                        <h5>{{ $activeCount }}</h5>
                        <span>Active Contracts</span>
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
                            $pendingCount = collect($leases)->where('status', 'pending')->count();
                        @endphp
                        <h5>{{ $pendingCount }}</h5>
                        <span>Pending Approval</span>
                    </div>
                    <div class="spark_icon bg-amber text-white" style="padding: 10px; border-radius: 50%;">
                        <i class="zmdi zmdi-time zmdi-hc-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leases DataTable List -->
    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Contracts</strong> Directory</h2>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-hover js-exportable dataTable mb-0">
                            <thead>
                                <tr>
                                    <th>Lease Target</th>
                                    <th>Dates</th>
                                    <th>Cycle</th>
                                    <th class="text-right">Rent (UGX)</th>
                                    <th class="text-right">Deposit (UGX)</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leases as $lease)
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold">
                                                {{ $lease['tenant_profile']['user']['first_name'] ?? 'Tenant' }}
                                                {{ $lease['tenant_profile']['user']['last_name'] ?? 'User' }}
                                            </div>
                                            <small class="text-muted"><i class="zmdi zmdi-city-alt"></i> Unit: {{ $lease['unit']['unit_number'] ?? 'N/A' }} ({{ $lease['unit']['property_name'] ?? 'Acme Plaza' }})</small>
                                        </td>
                                        <td>
                                            <div><strong>Start:</strong> {{ date('Y-m-d', strtotime($lease['start_date'])) }}</div>
                                            <small class="text-muted"><strong>End:</strong> {{ date('Y-m-d', strtotime($lease['end_date'])) }}</small>
                                        </td>
                                        <td>
                                            <span class="text-uppercase text-mono">{{ $lease['billing_cycle'] ?? 'monthly' }}</span>
                                        </td>
                                        <td class="text-right font-weight-bold text-dark">
                                            {{ number_format($lease['rent_amount']) }}
                                        </td>
                                        <td class="text-right text-muted font-weight-bold">
                                            {{ number_format($lease['deposit_amount'] ?? 0) }}
                                        </td>
                                        <td>
                                            @if(($lease['status'] ?? 'pending') === 'approved')
                                                <span class="badge badge-approved">Approved</span>
                                            @elseif(($lease['status'] ?? 'pending') === 'pending')
                                                <span class="badge badge-pending">Pending</span>
                                            @else
                                                <span class="badge badge-terminated">{{ ucfirst($lease['status'] ?? 'terminated') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if(($lease['status'] ?? 'pending') === 'pending')
                                                <form action="/leases/{{ $lease['id'] }}/status" method="POST" class="d-inline status-form">
                                                    @csrf
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="btn btn-sm btn-success waves-effect" title="Approve Lease">
                                                        <i class="zmdi zmdi-check"></i> Approve
                                                    </button>
                                                </form>
                                            @elseif(($lease['status'] ?? 'pending') === 'approved')
                                                <form action="/leases/{{ $lease['id'] }}/status" method="POST" class="d-inline status-form">
                                                    @csrf
                                                    <input type="hidden" name="status" value="terminated">
                                                    <button type="submit" class="btn btn-sm btn-danger waves-effect" title="Terminate Lease">
                                                        <i class="zmdi zmdi-close"></i> Terminate
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted"><i class="zmdi zmdi-info"></i> Inactive</span>
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

            // Prevent double click and turn button into loading indicator
            $('.status-form').on('submit', function() {
                var $form = $(this);
                var $btn = $form.find('button[type="submit"]');
                
                if ($btn.data('submitting')) {
                    return false;
                }
                
                $btn.data('submitting', true);
                $btn.prop('disabled', true);
                
                var originalHtml = $btn.html();
                $btn.data('original-html', originalHtml);
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Saving...');
            });
        });
    </script>
@endsection
