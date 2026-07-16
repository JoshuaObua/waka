@extends('layouts.app')

@section('title', config('company.name') . ' - Onboard Lease')

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
            <h2>Draft Lease Contract</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="/leases">Tenants & Leases</a></li>
                <li class="breadcrumb-item active">Create Lease</li>
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

    <div class="row clearfix justify-content-center">
        <div class="col-lg-8 col-md-10 col-sm-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Draft</strong> Lease Agreement</h2>
                </div>
                <div class="body">
                    <form action="/leases" method="POST">
                        @csrf
                        
                        <div class="row clearfix">
                            <!-- Lease Target selection -->
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="unit_id">Select Vacant Rentable Unit <span class="text-danger">*</span></label>
                                    <select id="unit_id" name="unit_id" class="form-control show-tick" required>
                                        <option value="" disabled selected>-- Choose Unit --</option>
                                        @foreach($units as $u)
                                            <option value="{{ $u['id'] }}" data-rent="{{ $u['rent_amount'] }}">
                                                {{ $u['unit_number'] }} ({{ $u['property_name'] ?? 'Property' }} - {{ number_format($u['rent_amount']) }} UGX/mo)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="tenant_profile_id">Select Tenant Profile <span class="text-danger">*</span></label>
                                    <select id="tenant_profile_id" name="tenant_profile_id" class="form-control show-tick" required>
                                        <option value="" disabled selected>-- Choose Tenant --</option>
                                        @foreach($tenants as $t)
                                            <option value="{{ $t['id'] }}">
                                                {{ $t['user']['first_name'] ?? 'N/A' }} {{ $t['user']['last_name'] ?? '' }} ({{ $t['user']['email'] ?? '' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix">
                            <!-- Dates picker -->
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="start_date">Lease Commencement Date <span class="text-danger">*</span></label>
                                    <input type="date" id="start_date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="end_date">Lease Termination Date <span class="text-danger">*</span></label>
                                    <input type="date" id="end_date" name="end_date" class="form-control" value="{{ date('Y-m-d', strtotime('+1 year')) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix">
                            <!-- Core financials -->
                            <div class="col-md-4 col-sm-12">
                                <div class="form-group">
                                    <label for="billing_cycle">Billing Cycle <span class="text-danger">*</span></label>
                                    <select id="billing_cycle" name="billing_cycle" class="form-control show-tick" required>
                                        <option value="monthly" selected>Monthly</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="quarterly">Quarterly</option>
                                        <option value="annually">Annually</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-4 col-sm-12">
                                <div class="form-group">
                                    <label for="rent_amount">Rent Amount (UGX) <span class="text-danger">*</span></label>
                                    <input type="number" id="rent_amount" name="rent_amount" class="form-control" placeholder="e.g. 1200000" min="100" required>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-12">
                                <div class="form-group">
                                    <label for="deposit_amount">Security Deposit (UGX)</label>
                                    <input type="number" id="deposit_amount" name="deposit_amount" class="form-control" placeholder="e.g. 1200000" min="0" value="0">
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h6 class="text-muted"><i class="zmdi zmdi-settings"></i> Additional Clauses & Penalties</h6>
                        
                        <div class="row clearfix">
                            <div class="col-md-4 col-sm-12">
                                <div class="form-group">
                                    <label for="escalation_rate">Escalation Rate (%)</label>
                                    <input type="number" step="0.01" id="escalation_rate" name="escalation_rate" class="form-control" value="0">
                                    <small class="text-muted">Annual rent increase percent</small>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-12">
                                <div class="form-group">
                                    <label for="late_fee_percentage">Late Fee Percentage (%)</label>
                                    <input type="number" step="0.01" id="late_fee_percentage" name="late_fee_percentage" class="form-control" value="0">
                                    <small class="text-muted">Penalty charged on overdue invoices</small>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-12">
                                <div class="form-group">
                                    <label for="late_fee_grace_days">Grace Period (Days)</label>
                                    <input type="number" id="late_fee_grace_days" name="late_fee_grace_days" class="form-control" value="0" min="0">
                                    <small class="text-muted">Days allowed before late fee accrues</small>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block waves-effect">
                            <i class="zmdi zmdi-file-text"></i> Draft Lease Agreement
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
                $('#unit_id, #tenant_profile_id, #billing_cycle').selectpicker();
            }

            // Auto-fill rent when unit selection changes
            $('#unit_id').on('change', function() {
                var selectedOption = $(this).find('option:selected');
                var rentAmt = selectedOption.data('rent');
                if (rentAmt) {
                    $('#rent_amount').val(rentAmt);
                }
            });

            // Prevent double click and turn button into loading indicator
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
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Writing agreement record...');
            });
        });
    </script>
@endsection
