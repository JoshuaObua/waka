@extends('layouts.app')

@section('title', config('company.name') . ' - Generate Utility Bill')

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
            <h2>Generate Utility Bill</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="/utility-bills">Bills</a></li>
                <li class="breadcrumb-item active">Generate Bill</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
            <a href="/utility-bills" class="btn btn-default btn-icon btn-round float-right m-r-10">
                <i class="zmdi zmdi-arrow-left"></i> <span>Back to Bills</span>
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
                    <h2><strong>Generate</strong> Utility Bill</h2>
                    <small>Fill in the details below to compute consumption and log a utility bill invoice.</small>
                </div>
                <div class="body">
                    <form action="/utility-bills" method="POST" id="billForm">
                        @csrf
                        
                        <div class="form-group">
                            <label for="meter_id">Select Utility Meter <span class="text-danger">*</span></label>
                            <select id="meter_id" name="meter_id" class="form-control show-tick" required>
                                <option value="" disabled selected>-- Choose Meter --</option>
                                @foreach($meters as $m)
                                    <option value="{{ $m['id'] }}">
                                        {{ $m['meter_number'] }} ({{ ucfirst($m['type']) }} - Last Reading: {{ number_format($m['last_reading'] ?? 0, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="tariff_id">Select Billing Tariff Plan <span class="text-danger">*</span></label>
                            <select id="tariff_id" name="tariff_id" class="form-control show-tick" required>
                                <option value="" disabled selected>-- Choose Tariff --</option>
                                @foreach($tariffs as $t)
                                    <option value="{{ $t['id'] }}">
                                        {{ $t['name'] }} ({{ number_format($t['rate_per_unit']) }} UGX/unit)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row clearfix">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="current_reading">Current Meter Reading <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" id="current_reading" name="current_reading" class="form-control" placeholder="Must exceed previous reading" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="due_date">Due Date <span class="text-danger">*</span></label>
                                    <input type="date" id="due_date" name="due_date" class="form-control" value="{{ date('Y-m-d', strtotime('+7 days')) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <a href="/utility-bills" class="btn btn-default waves-effect m-r-10">Cancel</a>
                            <button type="submit" class="btn btn-primary waves-effect" id="submit-btn">
                                <i class="zmdi zmdi-file-text"></i> Compute & Invoice Bill
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
                $('#meter_id, #tariff_id').selectpicker();
            }

            // Prevent double-clicking and convert submit button into loading spinner
            $('#billForm').on('submit', function() {
                var $btn = $('#submit-btn');
                if ($btn.data('submitting')) {
                    return false;
                }
                $btn.data('submitting', true);
                $btn.prop('disabled', true);
                var originalHtml = $btn.html();
                $btn.data('original-html', originalHtml);
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Writing bill records...');
            });
        });
    </script>
@endsection
