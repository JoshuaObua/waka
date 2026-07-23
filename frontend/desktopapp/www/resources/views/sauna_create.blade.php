@extends('layouts.app')

@section('title', config('company.name') . ' - Create Sauna Record')

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
            <h2>Add Sauna Record</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="/sauna">Sauna Management</a></li>
                <li class="breadcrumb-item active">Add Record</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
            <a href="/sauna" class="btn btn-default btn-icon btn-round float-right m-r-10">
                <i class="zmdi zmdi-arrow-left"></i> <span>Back to Sauna</span>
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
                    <h2><strong>Add</strong> Sauna Entry</h2>
                    <ul class="nav nav-tabs p-0 mb-3">
                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#subscribe-client-tab">Subscribe Client</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#add-plan-tab">Add Sauna Plan</a></li>
                    </ul>
                </div>
                <div class="body">
                    <div class="tab-content">
                        <!-- Subscribe Client Form -->
                        <div class="tab-pane active" id="subscribe-client-tab">
                            <form action="/sauna/subscriptions" method="POST" id="subscriptionForm">
                                @csrf
                                <div class="form-group">
                                    <label for="client_name">Client Full Name <span class="text-danger">*</span></label>
                                    <input type="text" id="client_name" name="client_name" class="form-control" placeholder="e.g. Charles Lwanga" required>
                                </div>

                                <div class="form-group">
                                    <label for="plan_id">Select Sauna Plan <span class="text-danger">*</span></label>
                                    <select id="plan_id" name="plan_id" class="form-control show-tick" required>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan['id'] }}">{{ $plan['name'] }} ({{ number_format($plan['price']) }} UGX)</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="text-right">
                                    <a href="/sauna" class="btn btn-default waves-effect m-r-10">Cancel</a>
                                    <button type="submit" class="btn btn-primary waves-effect" id="sub-submit-btn">
                                        <i class="zmdi zmdi-plus"></i> Subscribe Client
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Add Plan Form -->
                        <div class="tab-pane" id="add-plan-tab">
                            <form action="/sauna/plans" method="POST" id="planForm">
                                @csrf
                                <div class="form-group">
                                    <label for="name">Plan Name <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Daily Pass" required>
                                </div>

                                <div class="row clearfix">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="price">Price (UGX) <span class="text-danger">*</span></label>
                                            <input type="number" id="price" name="price" class="form-control" placeholder="e.g. 20000" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="duration_days">Duration (Days) <span class="text-danger">*</span></label>
                                            <input type="number" id="duration_days" name="duration_days" class="form-control" placeholder="e.g. 1" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <a href="/sauna" class="btn btn-default waves-effect m-r-10">Cancel</a>
                                    <button type="submit" class="btn btn-primary waves-effect" id="plan-submit-btn">
                                        <i class="zmdi zmdi-plus"></i> Create Plan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
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
            // Initialize selectpicker
            if ($.fn.selectpicker) {
                $('#plan_id').selectpicker();
            }

            // Prevent double-clicking and convert submit button into loading spinner
            $('#subscriptionForm').on('submit', function() {
                var $btn = $('#sub-submit-btn');
                if ($btn.data('submitting')) {
                    return false;
                }
                $btn.data('submitting', true);
                $btn.prop('disabled', true);
                var originalHtml = $btn.html();
                $btn.data('original-html', originalHtml);
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Saving...');
            });

            $('#planForm').on('submit', function() {
                var $btn = $('#plan-submit-btn');
                if ($btn.data('submitting')) {
                    return false;
                }
                $btn.data('submitting', true);
                $btn.prop('disabled', true);
                var originalHtml = $btn.html();
                $btn.data('original-html', originalHtml);
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Saving...');
            });
        });
    </script>
@endsection
