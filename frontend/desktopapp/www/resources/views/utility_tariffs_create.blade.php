@extends('layouts.app')

@section('title', config('company.name') . ' - Create Utility Tariff')

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
            <h2>Create Utility Tariff</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="/utility-tariffs">Tariffs</a></li>
                <li class="breadcrumb-item active">Create Tariff</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
            <a href="/utility-tariffs" class="btn btn-default btn-icon btn-round float-right m-r-10">
                <i class="zmdi zmdi-arrow-left"></i> <span>Back to Tariffs</span>
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
                    <h2><strong>Create</strong> Tariff Rate</h2>
                    <small>Fill in the details below to define a new consumption tariff rate structure.</small>
                </div>
                <div class="body">
                    <form action="/utility-tariffs" method="POST" id="tariffForm">
                        @csrf
                        
                        <div class="form-group">
                            <label for="name">Tariff Plan Label <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Umeme Commercial Grade" required>
                        </div>

                        <div class="row clearfix">
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rate_per_unit">Rate Per Unit (UGX) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" id="rate_per_unit" name="rate_per_unit" class="form-control" placeholder="e.g. 750" min="1" required>
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <a href="/utility-tariffs" class="btn btn-default waves-effect m-r-10">Cancel</a>
                            <button type="submit" class="btn btn-primary waves-effect" id="submit-btn">
                                <i class="zmdi zmdi-plus"></i> Create Tariff
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
                $('#type').selectpicker();
            }

            // Prevent double-clicking and convert submit button into loading spinner
            $('#tariffForm').on('submit', function() {
                var $btn = $('#submit-btn');
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
