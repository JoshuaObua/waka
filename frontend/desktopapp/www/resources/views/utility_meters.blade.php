@extends('layouts.app')

@section('title', config('company.name') . ' - Meter Settings')

@section('styles')
    <!-- JQuery DataTable Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-datatable/dataTables.bootstrap4.min.css') }}">
    <!-- Bootstrap Select Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-select/css/bootstrap-select.css') }}">
    <style>
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
            <h2>Utility Meter Registry</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">Utility Billing</a></li>
                <li class="breadcrumb-item active">Meter Settings</li>
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
        <!-- Register Meter Form -->
        <div class="col-lg-4 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Register</strong> Utility Meter</h2>
                </div>
                <div class="body">
                    <form action="/utility-meters" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="unit_id">Belongs To Unit <span class="text-danger">*</span></label>
                            <select id="unit_id" name="unit_id" class="form-control show-tick" required>
                                <option value="" disabled selected>-- Choose Unit --</option>
                                @foreach($units as $u)
                                    <option value="{{ $u['id'] }}">{{ $u['unit_number'] }} ({{ $u['property_name'] ?? 'Property' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="meter_number">Meter Serial Number <span class="text-danger">*</span></label>
                            <input type="text" id="meter_number" name="meter_number" class="form-control" placeholder="e.g. MTR-90081" required>
                        </div>

                        <div class="form-group">
                            <label for="type">Utility Type <span class="text-danger">*</span></label>
                            <select id="type" name="type" class="form-control show-tick" required>
                                <option value="electricity" selected>Electricity (Power)</option>
                                <option value="water">Water</option>
                                <option value="gas">Gas</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="last_reading">Initial Meter Reading <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" id="last_reading" name="last_reading" class="form-control" value="0.00" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block waves-effect">
                            <i class="zmdi zmdi-plus"></i> Register Meter
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Meters list DataTable -->
        <div class="col-lg-8 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Active</strong> Consumption Meters</h2>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                            <thead>
                                <tr>
                                    <th>Meter Number</th>
                                    <th>Target Unit</th>
                                    <th>Utility Type</th>
                                    <th class="text-right">Last Reading</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($meters as $meter)
                                    <tr>
                                        <td class="font-weight-bold text-mono">{{ $meter['meter_number'] }}</td>
                                        <td>{{ $meter['unit']['unit_number'] ?? 'Suite 101' }}</td>
                                        <td class="text-uppercase text-muted">{{ $meter['type'] }}</td>
                                        <td class="text-right font-weight-bold">{{ number_format($meter['last_reading'], 2) }}</td>
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
                ]
            });

            // Initialize Bootstrap Selectpicker
            if ($.fn.selectpicker) {
                $('#unit_id, #type').selectpicker();
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
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Writing meter records...');
            });
        });
    </script>
@endsection
