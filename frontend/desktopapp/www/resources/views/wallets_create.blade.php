@extends('layouts.app')

@section('title', config('company.name') . ' - Wallet Transaction')

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
            <h2>Wallet Transaction</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="/wallets">Wallets & Ledgers</a></li>
                <li class="breadcrumb-item active">New Transaction</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
            <a href="/wallets" class="btn btn-default btn-icon btn-round float-right m-r-10">
                <i class="zmdi zmdi-arrow-left"></i> <span>Back to Wallets</span>
            </a>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- Tab Navigation -->
    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card">
                <div class="body">
                    <ul class="nav nav-tabs p-0 mb-3">
                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#topup-tab">Tenant Top-Up</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#disburse-tab">Landlord Disbursement</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-content">
        <!-- Tenant Top-Up Tab -->
        <div class="tab-pane active" id="topup-tab">
            <div class="row clearfix">
                <div class="col-lg-8 col-md-12 offset-lg-2">
                    <div class="card">
                        <div class="header">
                            <h2><strong>Tenant</strong> Wallet Top-Up (Collections)</h2>
                        </div>
                        <div class="body">
                            <form action="/wallets/top-up" method="POST" id="topup-form">
                                @csrf
                                <div class="form-group">
                                    <label for="profile_id">Select Tenant <span class="text-danger">*</span></label>
                                    <select id="profile_id" name="profile_id" class="form-control show-tick" required>
                                        <option value="" disabled selected>-- Choose Tenant Profile --</option>
                                        @foreach($tenantsList as $t)
                                            <option value="{{ $t['id'] }}">
                                                {{ $t['user']['first_name'] ?? '' }} {{ $t['user']['last_name'] ?? '' }} ({{ $t['user']['email'] ?? '' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone">Payer Phone Number <span class="text-danger">*</span></label>
                                            <input type="text" id="phone" name="phone" class="form-control" placeholder="e.g. 0111777771" required>
                                            <small class="text-muted"><i class="zmdi zmdi-info"></i> Note: Use <code class="text-success">0111777771</code> to trigger successful collections in sandbox.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="amount">Amount (UGX) <span class="text-danger">*</span></label>
                                            <input type="number" id="amount" name="amount" min="1000" class="form-control" placeholder="e.g. 250000" required>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block btn-round waves-effect">
                                    <i class="zmdi zmdi-phone-setting"></i> Initiate Collection Top-Up
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Landlord Disbursement Tab -->
        <div class="tab-pane" id="disburse-tab">
            <div class="row clearfix">
                <div class="col-lg-8 col-md-12 offset-lg-2">
                    <div class="card">
                        <div class="header">
                            <h2><strong>Landlord</strong> Disbursement (Disburse Cash)</h2>
                        </div>
                        <div class="body">
                            <form action="/wallets/disburse" method="POST" id="disburse-form">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="payee_phone">Payee Phone Number <span class="text-danger">*</span></label>
                                            <input type="text" id="payee_phone" name="payee_phone" class="form-control" placeholder="e.g. 0111777771" required>
                                            <small class="text-muted"><i class="zmdi zmdi-info"></i> Note: Escrows out of the landlord wallet to the vendor or landlord phone.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="disburse_amount">Amount (UGX) <span class="text-danger">*</span></label>
                                            <input type="number" id="disburse_amount" name="amount" min="1000" class="form-control" placeholder="e.g. 150000" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="note">Disbursement Purpose / Note <span class="text-danger">*</span></label>
                                    <input type="text" id="note" name="note" class="form-control" placeholder="e.g. Plumbing repairs settlement" required>
                                </div>

                                <button type="submit" class="btn btn-danger btn-block btn-round waves-effect">
                                    <i class="zmdi zmdi-money-off"></i> Execute Mobile Money Payout
                                </button>
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
    <!-- Sweetalert Plugin Js -->
    <script src="{{ asset('assets/plugins/sweetalert/sweetalert.min.js') }}"></script>
    <!-- Bootstrap Select Plugin Js -->
    <script src="{{ asset('assets/plugins/bootstrap-select/js/bootstrap-select.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize Bootstrap Selectpicker
            if ($.fn.selectpicker) {
                $('#profile_id').selectpicker();
            }

            // Double-click prevention with loading spinner
            $('#topup-form, #disburse-form').on('submit', function() {
                var $form = $(this);
                var $btn = $form.find('button[type="submit"]');
                
                if ($btn.data('submitting')) {
                    return false;
                }
                
                $btn.data('submitting', true);
                $btn.prop('disabled', true);
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Initiating Payment Gateway Transaction...');
            });

            // SweetAlert notifications
            @if(session('success'))
                swal("Success", "{{ session('success') }}", "success");
            @endif

            @if($errors->any())
                swal("Error", "{{ $errors->first() }}", "error");
            @endif
        });
    </script>
@endsection
