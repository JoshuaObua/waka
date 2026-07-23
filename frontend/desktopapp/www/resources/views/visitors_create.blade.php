@extends('layouts.app')

@section('title', config('company.name') . ' - Pre-Register Visitor')

@section('styles')
    <!-- Sweetalert Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert/sweetalert.css') }}">
@endsection

@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-6 col-sm-12">
            <h2>Pre-Register Visitor</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="/visitors">Visitor Directory</a></li>
                <li class="breadcrumb-item active">Pre-Register</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
            <a href="/visitors" class="btn btn-default btn-icon btn-round float-right m-r-10">
                <i class="zmdi zmdi-arrow-left"></i> <span>Back to Visitors</span>
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
                    <h2><strong>Pre-Register</strong> Visitor</h2>
                    <small>Pre-register a visitor to streamline security entry on their arrival.</small>
                </div>
                <div class="body">
                    <form action="/visitors" method="POST" id="visitorForm">
                        @csrf
                        
                        <div class="form-group">
                            <label for="full_name">Visitor Full Name <span class="text-danger">*</span></label>
                            <input type="text" id="full_name" name="full_name" class="form-control" placeholder="e.g. John Doe" required>
                        </div>

                        <div class="row clearfix">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Visitor Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" id="phone" name="phone" class="form-control" placeholder="e.g. +256701122334" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Visitor Email Address <span class="text-danger">*</span></label>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="e.g. john.doe@gmail.com" required>
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="host_name">Host Label / Unit Owner <span class="text-danger">*</span></label>
                                    <input type="text" id="host_name" name="host_name" class="form-control" placeholder="e.g. Jane Mugisha" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="purpose">Purpose of Visit <span class="text-danger">*</span></label>
                                    <input type="text" id="purpose" name="purpose" class="form-control" placeholder="e.g. Delivery, Repairs, Meeting" required>
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <a href="/visitors" class="btn btn-default waves-effect m-r-10">Cancel</a>
                            <button type="submit" class="btn btn-primary waves-effect" id="submit-btn">
                                <i class="zmdi zmdi-walk"></i> Pre-Register
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
    <!-- Sweetalert Plugin Js -->
    <script src="{{ asset('assets/plugins/sweetalert/sweetalert.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Prevent double-clicking and convert submit button into loading spinner
            $('#visitorForm').on('submit', function() {
                var $btn = $('#submit-btn');
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
