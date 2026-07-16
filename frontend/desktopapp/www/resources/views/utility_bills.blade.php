@extends('layouts.app')

@section('title', config('company.name') . ' - Bills & Invoices')

@section('styles')
    <!-- JQuery DataTable Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-datatable/dataTables.bootstrap4.min.css') }}">
    <!-- Bootstrap Select Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-select/css/bootstrap-select.css') }}">
    <style>
        .badge-paid { background-color: #28a745; color: #fff; }
        .badge-unpaid { background-color: #dc3545; color: #fff; }
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
            <h2>Utility Invoices & Bills</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">Utility Billing</a></li>
                <li class="breadcrumb-item active">Bills & Invoices</li>
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

    <div class="row clearfix">
        <!-- Log Bill Form -->
        <div class="col-lg-4 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Generate</strong> Utility Bill</h2>
                </div>
                <div class="body">
                    <form action="/utility-bills" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="meter_id">Select Utility Meter <span class="text-danger">*</span></label>
                            <select id="meter_id" name="meter_id" class="form-control show-tick" required>
                                <option value="" disabled selected>-- Choose Meter --</option>
                                @foreach($meters as $m)
                                    <option value="{{ $m['id'] }}">
                                        {{ $m['meter_number'] }} ({{ ucfirst($m['type']) }} - Last Reading: {{ number_format($m['last_reading'] ?? 0, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="tariff_id">Select Billing Tariff Plan <span class="text-danger">*</span></label>
                            <select id="tariff_id" name="tariff_id" class="form-control show-tick" required>
                                <option value="" disabled selected>-- Choose Tariff --</option>
                                @foreach($tariffs as $t)
                                    <option value="{{ $t['id'] }}">
                                        {{ $t['name'] }} ({{ number_format($t['rate_per_unit']) }} UGX/unit)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="current_reading">Current Meter Reading <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" id="current_reading" name="current_reading" class="form-control" placeholder="Must exceed previous reading" required>
                        </div>

                        <div class="form-group">
                            <label for="due_date">Due Date <span class="text-danger">*</span></label>
                            <input type="date" id="due_date" name="due_date" class="form-control" value="{{ date('Y-m-d', strtotime('+7 days')) }}" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block waves-effect">
                            <i class="zmdi zmdi-file-text"></i> Compute & Invoice Bill
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Bills list DataTable -->
        <div class="col-lg-8 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Active</strong> Consumption Invoices</h2>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                            <thead>
                                <tr>
                                    <th>Meter Target</th>
                                    <th>Tariff Applied</th>
                                    <th class="text-right">Readings (Prev / Curr)</th>
                                    <th class="text-right">Consumption</th>
                                    <th class="text-right">Total (UGX)</th>
                                    <th>Status</th>
                                    <th>Due Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bills as $bill)
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold text-mono">{{ $bill['meter']['meter_number'] ?? 'N/A' }}</div>
                                            <small class="text-muted text-uppercase">{{ $bill['meter']['type'] ?? 'Electricity' }}</small>
                                        </td>
                                        <td>{{ $bill['tariff']['name'] ?? 'Umeme Standard Commercial' }}</td>
                                        <td class="text-right font-weight-bold text-mono">
                                            {{ number_format($bill['previous_reading'] ?? 0, 2) }} / {{ number_format($bill['current_reading'] ?? 0, 2) }}
                                        </td>
                                        <td class="text-right text-success font-weight-bold text-mono">
                                            {{ number_format($bill['units_consumed'] ?? 0, 2) }} Units
                                        </td>
                                        <td class="text-right font-weight-bold text-dark">
                                            {{ number_format($bill['total_amount'] ?? 0) }}
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $bill['status'] ?? 'unpaid' }}">{{ ucfirst($bill['status'] ?? 'unpaid') }}</span>
                                        </td>
                                        <td>{{ date('Y-m-d', strtotime($bill['due_date'] ?? 'now')) }}</td>
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
    <script src="{{ asset('assets/plugins/jquery-datatable/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-datatable/buttons/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-datatable/buttons/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-datatable/buttons/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-datatable/buttons/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-datatable/buttons/buttons.print.min.js') }}"></script>
    <!-- Bootstrap Select Plugin Js -->
    <script src="{{ asset('assets/plugins/bootstrap-select/js/bootstrap-select.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('.js-exportable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                order: [[6, "desc"]]
            });

            // Initialize Bootstrap Selectpicker
            if ($.fn.selectpicker) {
                $('#meter_id, #tariff_id').selectpicker();
            }

            // Prevent double-clicking and convert submit button into loading spinner
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
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Writing bill records...');
            });
        });
    </script>
@endsection
