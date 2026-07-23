@extends('layouts.app')

@section('title', config('company.name') . ' - Register Utility Meter')

@section('styles')
    <!-- Bootstrap Select Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-select/css/bootstrap-select.css') }}">
    <!-- Sweetalert Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert/sweetalert.css') }}">
@endsection

@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-6 col-sm-12">
            <h2>Register Utility Meter</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="/utility-meters">Meters</a></li>
                <li class="breadcrumb-item active">Register Meter</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
            <a href="/utility-meters" class="btn btn-default btn-icon btn-round float-right m-r-10">
                <i class="zmdi zmdi-arrow-left"></i> <span>Back to Meters</span>
            </a>
        </div>
    </div>
</div>

<div class="container-fluid">
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <i class="zmdi zmdi-alert-circle"></i> {{ $errors->first() }}
        </div>
    @endif

    <div class="row clearfix">
        <div class="col-lg-8 col-md-12 offset-lg-2">
            <div class="card">
                <div class="header">
                    <h2><strong>Register</strong> Utility Meter</h2>
                    <small>Fill in the details below to map a utility meter serial number to a rental unit.</small>
                </div>
                <div class="body">
                    <form action="/utility-meters" method="POST" id="meterForm">
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

                        <div class="row clearfix">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="meter_number">Meter Serial Number <span class="text-danger">*</span></label>
                                    <input type="text" id="meter_number" name="meter_number" class="form-control" placeholder="e.g. MTR-90081" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type">Utility Type <span class="text-danger">*</span></label>
                                    <select id="type" name="type" class="form-control show-tick" required>
                                        <option value="electricity" selected>Electricity (Power)</option>
                                        <option value="water">Water</option>
                                        <option value="gas">Gas</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="last_reading">Initial Meter Reading <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" id="last_reading" name="last_reading" class="form-control" value="0.00" required>
                        </div>

                        <div class="text-right">
                            <a href="/utility-meters" class="btn btn-default waves-effect m-r-10">Cancel</a>
                            <button type="submit" class="btn btn-primary waves-effect" id="submit-btn">
                                <i class="zmdi zmdi-plus"></i> Register Meter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <!-- Bootstrap Select Plugin Js -->
    <script src="{{ asset('assets/plugins/bootstrap-select/js/bootstrap-select.js') }}"></script>
    <!-- Sweetalert Plugin Js -->
    <script src="{{ asset('assets/plugins/sweetalert/sweetalert.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize Bootstrap Selectpicker
            if ($.fn.selectpicker) {
                $('#unit_id, #type').selectpicker();
            }

            // Prevent double-clicking and convert submit button into loading spinner
            $('#meterForm').on('submit', function() {
                var $btn = $('#submit-btn');
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
