@extends('layouts.app')

@section('title', config('company.name') . ' - Onboard Rentable Unit')

@section('styles')
    <!-- Bootstrap Select Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-select/css/bootstrap-select.css') }}">
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

    <div class="row clearfix">
        <div class="col-lg-8 col-md-12 offset-lg-2">
            <div class="card">
                <div class="header">
                    <h2><strong>Unit</strong> Registration Form</h2>
                    <small>Fill in the details below to onboard a new rentable unit under a registered property.</small>
                </div>
                <div class="body">
                    <form id="unitForm" action="" method="POST">
                        @csrf
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
                                    <input type="text" id="unit_number" name="unit_number" class="form-control" placeholder="e.g. Suite 101" required>
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="floor_number">Floor Number</label>
                                    <input type="number" id="floor_number" name="floor_number" class="form-control" placeholder="e.g. 1" min="0" value="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="category">Category <span class="text-danger">*</span></label>
                                    <select id="category" name="category" class="form-control show-tick" required>
                                        <option value="commercial" selected>Commercial</option>
                                        <option value="residential">Residential</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="type">Unit Type <span class="text-danger">*</span></label>
                                    <input type="text" id="type" name="type" class="form-control" placeholder="e.g. office, apartment, shop" required>
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rent_amount">Monthly Rent (UGX) <span class="text-danger">*</span></label>
                                    <input type="number" id="rent_amount" name="rent_amount" class="form-control" placeholder="e.g. 1200000" min="100" required>
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
    <script>
        $(document).ready(function() {
            // Initialize Bootstrap Selectpicker
            if ($.fn.selectpicker) {
                $('#property_id, #category').selectpicker();
            }

            // Dynamically update form action when property selection changes
            $('#property_id').on('change', function() {
                var propId = $(this).val();
                $('#unitForm').attr('action', '/properties/' + propId + '/units');
            });

            // Prevent double-clicking and convert submit button into loading spinner
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
