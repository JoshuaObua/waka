@extends('layouts.app')

@section('title', config('company.name') . ' - Onboard Maintenance Vendor')

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
            <h2>Onboard Maintenance Vendor</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="/vendors">Vendors</a></li>
                <li class="breadcrumb-item active">Onboard Vendor</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
            <a href="/vendors" class="btn btn-default btn-icon btn-round float-right m-r-10">
                <i class="zmdi zmdi-arrow-left"></i> <span>Back to Vendors</span>
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
                    <h2><strong>Onboard</strong> Maintenance Vendor</h2>
                    <small>Fill in the contractor profile details to index them for work orders scheduling.</small>
                </div>
                <div class="body">
                    <form action="/vendors" method="POST" id="vendorForm">
                        @csrf
                        
                        <div class="form-group">
                            <label for="business_name">Business Name <span class="text-danger">*</span></label>
                            <input type="text" id="business_name" name="business_name" class="form-control" placeholder="e.g. Kampala Plumbing Masters Ltd" required>
                        </div>

                        <div class="row clearfix">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_name">Primary Contact Name <span class="text-danger">*</span></label>
                                    <input type="text" id="contact_name" name="contact_name" class="form-control" placeholder="e.g. Andrew Mukasa" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category">Specialty Category <span class="text-danger">*</span></label>
                                    <select id="category" name="category" class="form-control show-tick" required>
                                        <option value="Plumbing" selected>Plumbing</option>
                                        <option value="Electrical">Electrical</option>
                                        <option value="Carpentry">Carpentry</option>
                                        <option value="HVAC">HVAC</option>
                                        <option value="Roofing">Roofing</option>
                                        <option value="General">General Maintenance</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" id="phone" name="phone" class="form-control" placeholder="e.g. +256772112233" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="e.g. contact@kplumbing.com" required>
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <a href="/vendors" class="btn btn-default waves-effect m-r-10">Cancel</a>
                            <button type="submit" class="btn btn-primary waves-effect" id="submit-btn">
                                <i class="zmdi zmdi-plus"></i> Onboard Vendor
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
                $('#category').selectpicker();
            }

            // Prevent double-clicking and convert submit button into loading spinner
            $('#vendorForm').on('submit', function() {
                var $btn = $('#submit-btn');
                if ($btn.data('submitting')) {
                    return false;
                }
                $btn.data('submitting', true);
                $btn.prop('disabled', true);
                var originalHtml = $btn.html();
                $btn.data('original-html', originalHtml);
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Writing vendor records...');
            });
        });
    </script>
@endsection
