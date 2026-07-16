@extends('layouts.app')

@section('title', config('company.name') . ' - Create Invoice')

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
            <h2>Create Rent Invoice</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="/invoices">Billing & Invoices</a></li>
                <li class="breadcrumb-item active">Create Invoice</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
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

    <div class="row clearfix text-center justify-content-center">
        <div class="col-lg-6 col-md-8 col-sm-12 text-left">
            <div class="card">
                <div class="header">
                    <h2><strong>Onboard</strong> New Invoice</h2>
                </div>
                <div class="body">
                    <form action="/invoices" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="lease_id">Active Lease Agreement <span class="text-danger">*</span></label>
                            <select id="lease_id" name="lease_id" class="form-control show-tick" required>
                                <option value="" disabled selected>-- Choose Lease --</option>
                                @foreach($leases as $l)
                                    <option value="{{ $l['id'] }}" data-rent="{{ $l['rent_amount'] }}">
                                        {{ $l['tenant_profile']['user']['first_name'] ?? 'N/A' }} 
                                        {{ $l['tenant_profile']['user']['last_name'] ?? '' }} 
                                        (Unit: {{ $l['unit']['unit_number'] ?? 'N/A' }} - {{ number_format($l['rent_amount']) }} UGX)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="issue_date">Billing Issue Date <span class="text-danger">*</span></label>
                            <input type="date" id="issue_date" name="issue_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="due_date">Payment Due Date <span class="text-danger">*</span></label>
                            <input type="date" id="due_date" name="due_date" class="form-control" value="{{ date('Y-m-d', strtotime('+14 days')) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="total_amount">Billing Amount (UGX) <span class="text-danger">*</span></label>
                            <input type="number" id="total_amount" name="total_amount" class="form-control" placeholder="e.g. 1200000" min="100" required>
                            <small class="text-muted"><i class="zmdi zmdi-info"></i> Reconciles automatically to matching lease billing agreements.</small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block waves-effect">
                            <i class="zmdi zmdi-file-text"></i> Generate Invoice
                        </button>
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
                $('#lease_id').selectpicker();
            }

            // Auto-fill rent amount when lease changes
            $('#lease_id').on('change', function() {
                var selectedOption = $(this).find('option:selected');
                var rentAmt = selectedOption.data('rent');
                if (rentAmt) {
                    $('#total_amount').val(rentAmt);
                }
            });

            // Prevent double click and show circular progress spinner
            $('form').on('submit', function() {
                var $form = $(this);
                var $btn = $form.find('button[type="submit"]');
                
                if ($btn.data('submitting')) {
                    return false;
                }
                
                $btn.data('submitting', true);
                $btn.prop('disabled', true);
                
                var originalHtml = $btn.html();
                $btn.data('original-html', originalHtml);
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Writing invoice records...');
            });
        });
    </script>
@endsection
