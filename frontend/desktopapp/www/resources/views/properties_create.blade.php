@extends('layouts.app')

@section('title', config('company.name') . ' - Register New Property')

@section('styles')
    <!-- Bootstrap Select Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-select/css/bootstrap-select.css') }}">
    <!-- Sweetalert Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert/sweetalert.css') }}">
    <style>
        .section-title {
            border-bottom: 2px solid #eaeaea;
            padding-bottom: 8px;
            margin-bottom: 20px;
            color: #495057;
            font-weight: 700;
        }
    </style>
@endsection

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

    <form action="/properties" method="POST" id="create-property-form">
        @csrf
        <div class="row clearfix">
            <!-- Left Column: Form Sections -->
            <div class="col-lg-12 col-md-12">
                <!-- Section 1: Basic Information -->
                <div class="card">
                    <div class="header">
                        <h2><strong>Basic</strong> & Pricing Information</h2>
                    </div>
                    <div class="body">
                        <div class="row clearfix">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Listing Title <span class="text-danger">*</span></label>
                                    <input type="text" id="title" name="title" class="form-control" placeholder="e.g. Modern 3-Bedroom Penthouse with Panoramic Views" value="{{ old('title') }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="property_status">Property Status <span class="text-danger">*</span></label>
                                    <select id="property_status" name="property_status" class="form-control show-tick" required>
                                        <option value="For Rent" {{ old('property_status') === 'For Rent' ? 'selected' : '' }}>For Rent</option>
                                        <option value="For Sale" {{ old('property_status') === 'For Sale' ? 'selected' : '' }}>For Sale</option>
                                        <option value="Lease" {{ old('property_status') === 'Lease' ? 'selected' : '' }}>Lease</option>
                                        <option value="Sold" {{ old('property_status') === 'Sold' ? 'selected' : '' }}>Sold</option>
                                        <option value="Rented" {{ old('property_status') === 'Rented' ? 'selected' : '' }}>Rented</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="property_type">Property Type <span class="text-danger">*</span></label>
                                    <select id="property_type" name="property_type" class="form-control show-tick" required>
                                        <option value="Apartment" {{ old('property_type') === 'Apartment' ? 'selected' : '' }}>Apartment</option>
                                        <option value="House" {{ old('property_type') === 'House' ? 'selected' : '' }}>House</option>
                                        <option value="Condo" {{ old('property_type') === 'Condo' ? 'selected' : '' }}>Condo</option>
                                        <option value="Commercial" {{ old('property_type') === 'Commercial' ? 'selected' : '' }}>Commercial Office</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="listing_price">Listing Price <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" id="listing_price" name="listing_price" class="form-control" placeholder="e.g. 450000" value="{{ old('listing_price') }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="currency">Currency <span class="text-danger">*</span></label>
                                    <select id="currency" name="currency" class="form-control show-tick" required>
                                        <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>USD ($)</option>
                                        <option value="UGX" {{ old('currency') === 'UGX' || !old('currency') ? 'selected' : '' }}>UGX (Shs)</option>
                                        <option value="KES" {{ old('currency') === 'KES' ? 'selected' : '' }}>KES (Ksh)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="price_period">Price Period</label>
                                    <select id="price_period" name="price_period" class="form-control show-tick">
                                        <option value="Per Month" {{ old('price_period') === 'Per Month' ? 'selected' : '' }}>Per Month</option>
                                        <option value="Per Year" {{ old('price_period') === 'Per Year' ? 'selected' : '' }}>Per Year</option>
                                        <option value="One Time" {{ old('price_period') === 'One Time' ? 'selected' : '' }}>One Time</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="year_built">Year Built <span class="text-danger">*</span></label>
                                    <input type="number" id="year_built" name="year_built" class="form-control" placeholder="e.g. 2024" value="{{ old('year_built', 2024) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Detailed Description <span class="text-danger">*</span></label>
                            <textarea id="description" name="description" rows="4" class="form-control" placeholder="Describe the property highlights, chef kitchen, elevator access, views, security, etc." required>{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Location Information -->
                <div class="card">
                    <div class="header">
                        <h2><strong>Location</strong> & Coordinates</h2>
                    </div>
                    <div class="body">
                        <div class="row clearfix">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="street_address">Street Address <span class="text-danger">*</span></label>
                                    <input type="text" id="street_address" name="street_address" class="form-control" placeholder="e.g. Plot 24, Kampala Road" value="{{ old('street_address') }}" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="unit_number">Unit Number</label>
                                    <input type="text" id="unit_number" name="unit_number" class="form-control" placeholder="e.g. Suite 4B" value="{{ old('unit_number') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="city">City <span class="text-danger">*</span></label>
                                    <input type="text" id="city" name="city" class="form-control" placeholder="e.g. Kampala" value="{{ old('city') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="state_region">State / Region <span class="text-danger">*</span></label>
                                    <input type="text" id="state_region" name="state_region" class="form-control" placeholder="e.g. Central Region" value="{{ old('state_region') }}" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="postal_code">Postal Code</label>
                                    <input type="text" id="postal_code" name="postal_code" class="form-control" placeholder="e.g. 00256" value="{{ old('postal_code') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="country">Country <span class="text-danger">*</span></label>
                                    <input type="text" id="country" name="country" class="form-control" placeholder="e.g. Uganda" value="{{ old('country', 'Uganda') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="latitude">Latitude <span class="text-danger">*</span></label>
                                    <input type="number" step="0.000001" id="latitude" name="latitude" class="form-control" placeholder="e.g. 0.3476" value="{{ old('latitude', '0.000000') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="longitude">Longitude <span class="text-danger">*</span></label>
                                    <input type="number" step="0.000001" id="longitude" name="longitude" class="form-control" placeholder="e.g. 32.5825" value="{{ old('longitude', '0.000000') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Layouts & Sizes -->
                <div class="card">
                    <div class="header">
                        <h2><strong>Sizing</strong> & Floor Details</h2>
                    </div>
                    <div class="body">
                        <div class="row clearfix">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="bedrooms">Bedrooms <span class="text-danger">*</span></label>
                                    <input type="number" id="bedrooms" name="bedrooms" class="form-control" placeholder="e.g. 3" value="{{ old('bedrooms', 0) }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="bathrooms">Bathrooms <span class="text-danger">*</span></label>
                                    <input type="number" step="0.1" id="bathrooms" name="bathrooms" class="form-control" placeholder="e.g. 2.5" value="{{ old('bathrooms', 0) }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="indoor_area">Indoor Area <span class="text-danger">*</span></label>
                                    <input type="number" step="0.1" id="indoor_area" name="indoor_area" class="form-control" placeholder="e.g. 185.5" value="{{ old('indoor_area', 0.0) }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="square_units">Area Units <span class="text-danger">*</span></label>
                                    <select id="square_units" name="square_units" class="form-control show-tick" required>
                                        <option value="Square Feet" {{ old('square_units') === 'Square Feet' ? 'selected' : '' }}>Square Feet (sq ft)</option>
                                        <option value="Square Meters" {{ old('square_units') === 'Square Meters' ? 'selected' : '' }}>Square Meters (sq m)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lot_size">Lot Size</label>
                                    <input type="number" step="0.1" id="lot_size" name="lot_size" class="form-control" placeholder="e.g. 450.0" value="{{ old('lot_size') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="floors_total">Total Floors</label>
                                    <input type="number" id="floors_total" name="floors_total" class="form-control" placeholder="e.g. 2" value="{{ old('floors_total', 1) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="floor_number">Floor Number</label>
                                    <input type="number" id="floor_number" name="floor_number" class="form-control" placeholder="e.g. 14" value="{{ old('floor_number', 0) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Media, virtual Tours & Amenities -->
                <div class="card">
                    <div class="header">
                        <h2><strong>Media</strong> & Virtual Tours</h2>
                    </div>
                    <div class="body">
                        <div class="row clearfix">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="primary_image_url">Primary Image URL <span class="text-danger">*</span></label>
                                    <input type="url" id="primary_image_url" name="primary_image_url" class="form-control" placeholder="https://cdn.example.com/listings/img01.jpg" value="{{ old('primary_image_url') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="video_tour_url">Video Tour URL</label>
                                    <input type="url" id="video_tour_url" name="video_tour_url" class="form-control" placeholder="https://youtube.com/watch?v=xyz" value="{{ old('video_tour_url') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="floor_plan_url">Floor Plan URL</label>
                                    <input type="url" id="floor_plan_url" name="floor_plan_url" class="form-control" placeholder="https://cdn.example.com/plans/floor1.pdf" value="{{ old('floor_plan_url') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="has_virtual_tour">Has Virtual Tour?</label>
                                    <select id="has_virtual_tour" name="has_virtual_tour" class="form-control show-tick">
                                        <option value="0" {{ old('has_virtual_tour') === '0' ? 'selected' : '' }}>No</option>
                                        <option value="1" {{ old('has_virtual_tour') === '1' ? 'selected' : '' }}>Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="virtual_tour_url">Virtual Tour URL</label>
                                    <input type="url" id="virtual_tour_url" name="virtual_tour_url" class="form-control" placeholder="https://waka-pms.io/tours/example/index.html" value="{{ old('virtual_tour_url') }}">
                                </div>
                            </div>
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
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
    <!-- Bootstrap Select Plugin Js -->
    <script src="{{ asset('assets/plugins/bootstrap-select/js/bootstrap-select.js') }}"></script>
    <!-- Sweetalert Plugin Js -->
    <script src="{{ asset('assets/plugins/sweetalert/sweetalert.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize selectpickers
            if ($.fn.selectpicker) {
                $('#property_status').selectpicker();
                $('#property_type').selectpicker();
                $('#currency').selectpicker();
                $('#price_period').selectpicker();
                $('#square_units').selectpicker();
                $('#has_virtual_tour').selectpicker();
            }

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
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Registering property portfolio...');
            });
        });
    </script>
@endsection
