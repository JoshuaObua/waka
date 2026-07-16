@extends('layouts.app')

@section('title', config('company.name') . ' - Invoices')

@section('styles')
    <!-- JQuery DataTable Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-datatable/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-datatable/fixedeader/dataTables.fixedcolumns.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-datatable/fixedeader/dataTables.fixedheader.bootstrap4.min.css') }}">
    <!-- Sweetalert Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert/sweetalert.css') }}">
    <style>
        .badge-paid {
            background-color: #28a745;
            color: #fff;
        }
        .badge-unpaid {
            background-color: #6c757d;
            color: #fff;
        }
        .badge-overdue {
            background-color: #dc3545;
            color: #fff;
        }
        .badge-partial {
            background-color: #ffc107;
            color: #212529;
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
            <h2>Rent Invoices</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">Billing & Invoices</a></li>
                <li class="breadcrumb-item active">Invoices</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <a href="/invoices/create" class="btn btn-primary float-right waves-effect m-l-10">
                <i class="zmdi zmdi-plus"></i> Create New Invoice
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

    <!-- Invoices Summary Row -->
    <div class="row clearfix">
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card state_w1">
                <div class="body d-flex justify-content-between align-items-center">
                    <div>
                        <h5>{{ count($invoices) }}</h5>
                        <span>Total Invoices</span>
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
                            $paidCount = collect($invoices)->where('status', 'paid')->count();
                        @endphp
                        <h5>{{ $paidCount }}</h5>
                        <span>Settled / Paid Invoices</span>
                    </div>
                    <div class="spark_icon bg-green text-white" style="padding: 10px; border-radius: 50%;">
                        <i class="zmdi zmdi-check-circle zmdi-hc-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card state_w1">
                <div class="body d-flex justify-content-between align-items-center">
                    <div>
                        @php
                            $overdueCount = collect($invoices)->where('status', 'overdue')->count();
                        @endphp
                        <h5>{{ $overdueCount }}</h5>
                        <span>Overdue / Unpaid Invoices</span>
                    </div>
                    <div class="spark_icon bg-red text-white" style="padding: 10px; border-radius: 50%;">
                        <i class="zmdi zmdi-alert-triangle zmdi-hc-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoices List DataTable -->
    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Active</strong> Invoices List</h2>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-hover js-exportable dataTable mb-0">
                            <thead>
                                <tr>
                                    <th>Invoice Ref</th>
                                    <th>Tenant / Unit</th>
                                    <th>Issue Date</th>
                                    <th>Due Date</th>
                                    <th class="text-right">Total (UGX)</th>
                                    <th class="text-right">Paid (UGX)</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoices as $inv)
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold text-mono">{{ $inv['invoice_number'] ?? 'N/A' }}</div>
                                            <small class="text-muted text-mono">{{ $inv['id'] }}</small>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold">
                                                {{ $inv['lease']['tenant_profile']['user']['first_name'] ?? 'Guest' }} 
                                                {{ $inv['lease']['tenant_profile']['user']['last_name'] ?? 'User' }}
                                            </div>
                                            <small class="text-muted"><i class="zmdi zmdi-city-alt"></i> Unit: {{ $inv['lease']['unit']['unit_number'] ?? 'N/A' }}</small>
                                        </td>
                                        <td>{{ date('Y-m-d', strtotime($inv['issue_date'])) }}</td>
                                        <td>{{ date('Y-m-d', strtotime($inv['due_date'])) }}</td>
                                        <td class="text-right font-weight-bold text-dark">{{ number_format($inv['total_amount']) }}</td>
                                        <td class="text-right text-success font-weight-bold">{{ number_format($inv['paid_amount']) }}</td>
                                        <td>
                                            @if(($inv['status'] ?? '') === 'paid')
                                                <span class="badge badge-paid">Paid</span>
                                            @elseif(($inv['status'] ?? '') === 'overdue')
                                                <span class="badge badge-overdue">Overdue</span>
                                            @elseif(($inv['status'] ?? '') === 'partially_paid')
                                                <span class="badge badge-partial">Partially Paid</span>
                                            @else
                                                <span class="badge badge-unpaid">Unpaid</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if(($inv['status'] ?? '') !== 'paid')
                                                <button type="button" class="btn btn-sm btn-success waves-effect pay-btn" data-id="{{ $inv['id'] }}" data-number="{{ $inv['invoice_number'] }}">
                                                    <i class="zmdi zmdi-money"></i> Collect MM
                                                </button>
                                            @else
                                                <span class="text-muted"><i class="zmdi zmdi-check-all"></i> Settled</span>
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

<!-- Pay via Gateway Modal -->
<div class="modal fade" id="payModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Collect Mobile Money Payment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="payForm" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="zmdi zmdi-info"></i> Initiating instant collections via **ioTec Pay v1**. Payer will receive an instant payment request prompt on their mobile device.
                    </div>
                    <div class="form-group">
                        <label for="pay_invoice_number">Invoice Reference</label>
                        <input type="text" id="pay_invoice_number" class="form-control text-mono" readonly>
                    </div>
                    <div class="form-group">
                        <label for="phone">Payer Mobile Money Number <span class="text-danger">*</span></label>
                        <input type="text" id="phone" name="phone" class="form-control" placeholder="e.g. 0111777771" required>
                        <small class="text-muted">Use sandbox target <code class="text-success">0111777771</code> to trigger instant success.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-neutral waves-effect" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success waves-effect">
                        <i class="zmdi zmdi-phone-setting"></i> Collect Payment
                    </button>
                </div>
            </form>
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
                order: [[2, "desc"]],
                pageLength: 10
            });

            // Open pay modal and map target parameters
            $('.pay-btn').on('click', function() {
                var invId = $(this).data('id');
                var invNum = $(this).data('number');
                
                $('#payForm').attr('action', '/invoices/' + invId + '/pay');
                $('#pay_invoice_number').val(invNum);
                $('#payModal').modal('show');
            });

            // Prevent double click and turn button into loading indicator
            $('#payForm').on('submit', function() {
                var $form = $(this);
                var $btn = $form.find('button[type="submit"]');
                
                if ($btn.data('submitting')) {
                    return false;
                }
                
                $btn.data('submitting', true);
                $btn.prop('disabled', true);
                
                var originalHtml = $btn.html();
                $btn.data('original-html', originalHtml);
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Processing collection request...');
            });
        });
    </script>
@endsection
