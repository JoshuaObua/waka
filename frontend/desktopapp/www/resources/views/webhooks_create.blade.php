@extends('layouts.app')

@section('title', config('company.name') . ' - Create Webhook Subscription')

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
            <h2>Subscribe Webhook Target</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="/webhooks">Webhooks</a></li>
                <li class="breadcrumb-item active">Subscribe</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
            <a href="/webhooks" class="btn btn-default btn-icon btn-round float-right m-r-10">
                <i class="zmdi zmdi-arrow-left"></i> <span>Back to Webhooks</span>
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
                    <h2><strong>Subscribe</strong> Webhook Target</h2>
                    <small>Fill in the details below to register an event subscription target URL.</small>
                </div>
                <div class="body">
                    <form action="/webhooks" method="POST" id="webhookForm">
                        @csrf
                        
                        <div class="form-group">
                            <label for="target_url">Target Listener URL <span class="text-danger">*</span></label>
                            <input type="url" id="target_url" name="target_url" class="form-control" placeholder="https://api.yourdomain.com/webhooks" required>
                        </div>

                        <div class="form-group">
                            <label for="event_type">Target Notification Event <span class="text-danger">*</span></label>
                            <select id="event_type" name="event_type" class="form-control show-tick" required>
                                <option value="invoice.paid" selected>invoice.paid</option>
                                <option value="invoice.overdue">invoice.overdue</option>
                                <option value="lease.approved">lease.approved</option>
                                <option value="lease.terminated">lease.terminated</option>
                                <option value="maintenance.request_created">maintenance.request_created</option>
                            </select>
                        </div>

                        <div class="text-right">
                            <a href="/webhooks" class="btn btn-default waves-effect m-r-10">Cancel</a>
                            <button type="submit" class="btn btn-primary waves-effect" id="submit-btn">
                                <i class="zmdi zmdi-notifications-active"></i> Create Subscription
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
                $('#event_type').selectpicker();
            }

            // Prevent double-clicking and convert submit button into loading spinner
            $('#webhookForm').on('submit', function() {
                var $btn = $('#submit-btn');
                if ($btn.data('submitting')) {
                    return false;
                }
                $btn.data('submitting', true);
                $btn.prop('disabled', true);
                var originalHtml = $btn.html();
                $btn.data('original-html', originalHtml);
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Saving subscription...');
            });
        });
    </script>
@endsection
