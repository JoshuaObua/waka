@extends('layouts.app')

@section('title', config('company.name') . ' - Tariffs Directory')

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
            <h2>Utility Tariffs</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">Utility Billing</a></li>
                <li class="breadcrumb-item active">Tariffs Directory</li>
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
        <!-- Create Tariff Form -->
        <div class="col-lg-4 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Create</strong> Tariff Rate</h2>
                </div>
                <div class="body">
                    <form action="/utility-tariffs" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="name">Tariff Plan Label <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Umeme Commercial Grade" required>
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
                            <label for="rate_per_unit">Rate Per Unit (UGX) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" id="rate_per_unit" name="rate_per_unit" class="form-control" placeholder="e.g. 750" min="1" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block waves-effect">
                            <i class="zmdi zmdi-plus"></i> Create Tariff
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tariffs list DataTable -->
        <div class="col-lg-8 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Active</strong> Billing Tariff Formulas</h2>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                            <thead>
                                <tr>
                                    <th>Tariff Name</th>
                                    <th>Utility Type</th>
                                    <th class="text-right">Price per Unit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tariffs as $tariff)
                                    <tr>
                                        <td class="font-weight-bold">{{ $tariff['name'] }}</td>
                                        <td class="text-uppercase text-muted">{{ $tariff['type'] }}</td>
                                        <td class="text-right font-weight-bold text-dark">{{ number_format($tariff['rate_per_unit'], 2) }} UGX</td>
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
                $('#type').selectpicker();
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
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Creating tariff plan...');
            });
        });
    </script>
@endsection
