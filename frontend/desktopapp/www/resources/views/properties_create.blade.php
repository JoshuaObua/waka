@extends('layouts.app')

@section('title', config('company.name') . ' - Register New Property')

@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-6 col-sm-12">
            <h2>Register New Property</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="/properties">Properties & Units</a></li>
                <li class="breadcrumb-item active">Create Property</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
            <a href="/properties" class="btn btn-default btn-icon btn-round float-right m-r-10">
                <i class="zmdi zmdi-arrow-left"></i> <span>Back to Properties</span>
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
                    <h2><strong>Property</strong> Registration Form</h2>
                    <small>Fill in the details below to register a new property under your organization.</small>
                </div>
                <div class="body">
                    <form action="/properties" method="POST" id="create-property-form">
                        @csrf
                        <div class="row clearfix">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Property Name <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Acme Plaza" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address">Address <span class="text-danger">*</span></label>
                                    <input type="text" id="address" name="address" class="form-control" placeholder="e.g. Plot 14, Kampala Road" required>
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gps_coordinates">GPS Coordinates</label>
                                    <input type="text" id="gps_coordinates" name="gps_coordinates" class="form-control" placeholder="e.g. 0.3125, 32.5811">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="land_title_number">Land Title Number</label>
                                    <input type="text" id="land_title_number" name="land_title_number" class="form-control" placeholder="e.g. FRVOL-29910-44">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="4" class="form-control" placeholder="Optional property details, features, or notes..."></textarea>
                        </div>

                        <hr>

                        <div class="text-right">
                            <a href="/properties" class="btn btn-default waves-effect m-r-10">
                                <i class="zmdi zmdi-close"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary waves-effect" id="submit-btn">
                                <i class="zmdi zmdi-plus"></i> Register Property
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
    <script>
        $(document).ready(function() {
            // Prevent double-clicking on submit and turn button into spinner loading indicator
            $('#create-property-form').on('submit', function() {
                var $btn = $('#submit-btn');
                
                if ($btn.data('submitting')) {
                    return false;
                }
                
                $btn.data('submitting', true);
                $btn.prop('disabled', true);
                
                var originalHtml = $btn.html();
                $btn.data('original-html', originalHtml);
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Registering property...');
            });
        });
    </script>
@endsection
