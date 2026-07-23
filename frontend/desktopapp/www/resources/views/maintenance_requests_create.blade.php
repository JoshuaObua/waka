@extends('layouts.app')

@section('title', config('company.name') . ' - Log Maintenance Request')

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
            <h2>Log Maintenance Request</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="/maintenance-requests">Requests</a></li>
                <li class="breadcrumb-item active">Log Request</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
            <a href="/maintenance-requests" class="btn btn-default btn-icon btn-round float-right m-r-10">
                <i class="zmdi zmdi-arrow-left"></i> <span>Back to Requests</span>
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
                    <h2><strong>Log</strong> Maintenance Request</h2>
                    <small>Fill in the details below to request maintenance for a specific unit or the building.</small>
                </div>
                <div class="body">
                    <form action="/maintenance-requests" method="POST" id="requestForm">
                        @csrf
                        
                        <div class="row clearfix">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="unit_id">Select Unit / Property</label>
                                    <select id="unit_id" name="unit_id" class="form-control show-tick">
                                        <option value="" selected>-- Choose Unit (Optional) --</option>
                                        @foreach($units as $u)
                                            <option value="{{ $u['id'] }}">{{ $u['unit_number'] }} ({{ $u['property_name'] ?? 'Property' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tenant_profile_id">Select Tenant Profile</label>
                                    <select id="tenant_profile_id" name="tenant_profile_id" class="form-control show-tick">
                                        <option value="" selected>-- Choose Tenant (Optional) --</option>
                                        @foreach($tenants as $t)
                                            <option value="{{ $t['id'] }}">
                                                {{ $t['user']['first_name'] ?? 'Tenant' }} {{ $t['user']['last_name'] ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category">Category <span class="text-danger">*</span></label>
                                    <select id="category" name="category" class="form-control show-tick" required>
                                        <option value="Plumbing" selected>Plumbing</option>
                                        <option value="Electrical">Electrical</option>
                                        <option value="Carpentry">Carpentry</option>
                                        <option value="HVAC">HVAC</option>
                                        <option value="Appliance">Appliance</option>
                                        <option value="General">General</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="priority">Priority Level <span class="text-danger">*</span></label>
                                    <select id="priority" name="priority" class="form-control show-tick" required>
                                        <option value="low">Low</option>
                                        <option value="medium" selected>Medium</option>
                                        <option value="high">High</option>
                                        <option value="emergency">Emergency</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Issue Description <span class="text-danger">*</span></label>
                            <textarea id="description" name="description" rows="4" class="form-control" placeholder="Describe the defect details here..." required></textarea>
                        </div>

                        <div class="text-right">
                            <a href="/maintenance-requests" class="btn btn-default waves-effect m-r-10">Cancel</a>
                            <button type="submit" class="btn btn-primary waves-effect" id="submit-btn">
                                <i class="zmdi zmdi-plus"></i> Log Request
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
                $('#unit_id, #tenant_profile_id, #category, #priority').selectpicker();
            }

            // Prevent double-clicking and convert submit button into loading spinner
            $('#requestForm').on('submit', function() {
                var $btn = $('#submit-btn');
                if ($btn.data('submitting')) {
                    return false;
                }
                $btn.data('submitting', true);
                $btn.prop('disabled', true);
                var originalHtml = $btn.html();
                $btn.data('original-html', originalHtml);
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Writing request records...');
            });
        });
    </script>
@endsection
