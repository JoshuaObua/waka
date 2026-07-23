@extends('layouts.app')

@section('title', config('company.name') . ' - Register Smart Device')

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
            <h2>Register Smart Device</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="/smart-devices">Smart Devices</a></li>
                <li class="breadcrumb-item active">Register Device</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
            <a href="/smart-devices" class="btn btn-default btn-icon btn-round float-right m-r-10">
                <i class="zmdi zmdi-arrow-left"></i> <span>Back to Devices</span>
            </a>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row clearfix">
        <div class="col-lg-8 col-md-12 offset-lg-2">
            <div class="card">
                <div class="header">
                    <h2><strong>Register</strong> Smart Device</h2>
                </div>
                <div class="body">
                    <form action="/smart-devices" method="POST" id="device-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Device Label / Name <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Main Gate Controller" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="device_type">Device Type <span class="text-danger">*</span></label>
                                    <select id="device_type" name="device_type" class="form-control show-tick" required>
                                        <option value="gateway" selected>Gateway / Controller</option>
                                        <option value="lock">Smart Lock</option>
                                        <option value="meter">Smart Utility Meter</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="parameters">Operational Parameters (JSON) <span class="text-danger">*</span></label>
                            <textarea id="parameters" name="parameters" rows="4" class="form-control no-resize font-weight-bold text-mono" required>{"mode":"auto","lock_delay":10}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block btn-round waves-effect">
                            <i class="zmdi zmdi-plus"></i> Register Device
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <!-- Sweetalert Plugin Js -->
    <script src="{{ asset('assets/plugins/sweetalert/sweetalert.min.js') }}"></script>
    <!-- Bootstrap Select Plugin Js -->
    <script src="{{ asset('assets/plugins/bootstrap-select/js/bootstrap-select.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize selectpickers
            if ($.fn.selectpicker) {
                $('#device_type').selectpicker();
            }

            // Double-click prevention with loading spinner
            $('#device-form').on('submit', function() {
                var $form = $(this);
                var $btn = $form.find('button[type="submit"]');
                
                if ($btn.data('submitting')) {
                    return false;
                }
                
                $btn.data('submitting', true);
                $btn.prop('disabled', true);
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Registering device...');
            });

            // SweetAlert notifications
            @if(session('success'))
                swal("Success", "{{ session('success') }}", "success");
            @endif

            @if($errors->any())
                swal("Error", "{{ $errors->first() }}", "error");
            @endif
        });
    </script>
@endsection
