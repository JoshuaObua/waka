@extends('layouts.app')

@section('title', config('company.name') . ' - Onboard Rentable Unit')

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
            <h2>Onboard Rentable Unit</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="/units">Properties & Units</a></li>
                <li class="breadcrumb-item active">Create Unit</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
            <a href="/units" class="btn btn-default btn-icon btn-round float-right m-r-10">
                <i class="zmdi zmdi-arrow-left"></i> <span>Back to Units</span>
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

    <form id="unitForm" action="" method="POST">
        @csrf
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12">
                <!-- Section 1: Property Association & Listing Info -->
                <div class="card">
                    <div class="header">
                        <h2><strong>Listing</strong> & Property Association</h2>
                    </div>
                    <div class="body">
                        <div class="row clearfix">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="property_id">Belongs To Property <span class="text-danger">*</span></label>
                                    <select id="property_id" name="property_id" class="form-control show-tick" required>
                                        <option value="" disabled selected>-- Choose Property --</option>
                                        @foreach($properties as $prop)
                                            <option value="{{ $prop['id'] }}">{{ $prop['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="unit_number">Unit / Suite Number <span class="text-danger">*</span></label>
                                    <input type="text" id="unit_number" name="unit_number" class="form-control" placeholder="e.g. Suite 4B-Unit 1" required>
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="property_status">Unit Status <span class="text-danger">*</span></label>
                                    <select id="property_status" name="property_status" class="form-control show-tick" required>
                                        <option value="For Rent">For Rent</option>
                                        <option value="For Sale">For Sale</option>
                                        <option value="Rented">Rented</option>
                                        <option value="Sold">Sold</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="property_type">Unit Type <span class="text-danger">*</span></label>
                                    <select id="property_type" name="property_type" class="form-control show-tick" required>
                                        <option value="Apartment">Apartment</option>
                                        <option value="House">House</option>
                                        <option value="Condo">Condo</option>
                                        <option value="Commercial">Commercial Office</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="listing_price">Listing Price <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" id="listing_price" name="listing_price" class="form-control" placeholder="e.g. 2000" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="currency">Currency <span class="text-danger">*</span></label>
                                    <select id="currency" name="currency" class="form-control show-tick" required>
                                        <option value="USD">USD ($)</option>
                                        <option value="UGX" selected>UGX (Shs)</option>
                                        <option value="KES">KES (Ksh)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price_period">Price Period</label>
                                    <select id="price_period" name="price_period" class="form-control show-tick">
                                        <option value="Per Month" selected>Per Month</option>
                                        <option value="Per Year">Per Year</option>
                                        <option value="One Time">One Time</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Unit Description</label>
                            <textarea id="description" name="description" rows="4" class="form-control" placeholder="Individual unit details, highlights, features, or notes..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Size & Layout Specifications -->
                <div class="card">
                    <div class="header">
                        <h2><strong>Sizing</strong> & Physical attributes</h2>
                    </div>
                    <div class="body">
                        <div class="row clearfix">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="bedrooms">Bedrooms</label>
                                    <input type="number" id="bedrooms" name="bedrooms" class="form-control" placeholder="e.g. 1" value="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="bathrooms">Bathrooms</label>
                                    <input type="number" step="0.1" id="bathrooms" name="bathrooms" class="form-control" placeholder="e.g. 1.0" value="0.0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="indoor_area">Indoor Area</label>
                                    <input type="number" step="0.1" id="indoor_area" name="indoor_area" class="form-control" placeholder="e.g. 55.0" value="0.0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="square_units">Area Units</label>
                                    <select id="square_units" name="square_units" class="form-control show-tick">
                                        <option value="Square Feet" selected>Square Feet (sq ft)</option>
                                        <option value="Square Meters">Square Meters (sq m)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="lot_size">Lot Size</label>
                                    <input type="number" step="0.1" id="lot_size" name="lot_size" class="form-control" placeholder="e.g. 100.0" value="0.0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="year_built">Year Built</label>
                                    <input type="number" id="year_built" name="year_built" class="form-control" placeholder="e.g. 2024" value="2024">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="floors_total">Total Floors</label>
                                    <input type="number" id="floors_total" name="floors_total" class="form-control" placeholder="e.g. 1" value="1">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="floor_number">Floor Number</label>
                                    <input type="number" id="floor_number" name="floor_number" class="form-control" placeholder="e.g. 14" value="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Location Details (Overrides property location if specific) -->
                <div class="card">
                    <div class="header">
                        <h2><strong>Specific</strong> Unit Location (Optional Override)</h2>
                    </div>
                    <div class="body">
                        <div class="row clearfix">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="street_address">Street Address</label>
                                    <input type="text" id="street_address" name="street_address" class="form-control" placeholder="e.g. Plot 24, Kampala Road">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text" id="city" name="city" class="form-control" placeholder="e.g. Kampala">
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="state_region">State / Region</label>
                                    <input type="text" id="state_region" name="state_region" class="form-control" placeholder="e.g. Central Region">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="postal_code">Postal Code</label>
                                    <input type="text" id="postal_code" name="postal_code" class="form-control" placeholder="e.g. 00256">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="country">Country</label>
                                    <input type="text" id="country" name="country" class="form-control" placeholder="e.g. Uganda">
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="latitude">Latitude</label>
                                    <input type="number" step="0.000001" id="latitude" name="latitude" class="form-control" placeholder="e.g. 0.3476" value="0.000000">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="longitude">Longitude</label>
                                    <input type="number" step="0.000001" id="longitude" name="longitude" class="form-control" placeholder="e.g. 32.5825" value="0.000000">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Media & Virtual Tours -->
                <div class="card">
                    <div class="header">
                        <h2><strong>Media</strong> & Virtual Tours</h2>
                    </div>
                    <div class="body">
                        <div class="row clearfix">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="primary_image_url">Primary Image URL <span class="text-danger">*</span></label>
                                    <input type="url" id="primary_image_url" name="primary_image_url" class="form-control" placeholder="https://cdn.example.com/listings/unit01.jpg" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="video_tour_url">Video Tour URL</label>
                                    <input type="url" id="video_tour_url" name="video_tour_url" class="form-control" placeholder="https://youtube.com/watch?v=xyz">
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="floor_plan_url">Floor Plan URL</label>
                                    <input type="url" id="floor_plan_url" name="floor_plan_url" class="form-control" placeholder="https://cdn.example.com/plans/floor_unit.pdf">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="has_virtual_tour">Has Virtual Tour?</label>
                                    <select id="has_virtual_tour" name="has_virtual_tour" class="form-control show-tick">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="virtual_tour_url">Virtual Tour URL</label>
                                    <input type="url" id="virtual_tour_url" name="virtual_tour_url" class="form-control" placeholder="https://waka-pms.io/tours/example/unit.html">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="text-right">
                            <a href="/units" class="btn btn-default waves-effect m-r-10">
                                <i class="zmdi zmdi-close"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary waves-effect" id="submit-btn">
                                <i class="zmdi zmdi-plus"></i> Onboard Unit
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
                $('#property_id').selectpicker();
                $('#property_status').selectpicker();
                $('#property_type').selectpicker();
                $('#currency').selectpicker();
                $('#price_period').selectpicker();
                $('#square_units').selectpicker();
                $('#has_virtual_tour').selectpicker();
            }

            // Dynamically update form action when property selection changes
            $('#property_id').on('change', function() {
                var propId = $(this).val();
                $('#unitForm').attr('action', '/properties/' + propId + '/units');
            });

            // Prevent double-clicking on submit and turn button into spinner loading indicator
            $('#unitForm').on('submit', function() {
                var $btn = $('#submit-btn');
                
                if ($btn.data('submitting')) {
                    return false;
                }
                
                $btn.data('submitting', true);
                $btn.prop('disabled', true);
                
                var originalHtml = $btn.html();
                $btn.data('original-html', originalHtml);
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Writing unit records...');
            });
        });
    </script>
@endsection
