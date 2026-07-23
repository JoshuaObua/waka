@extends('layouts.app')

@section('title', config('company.name') . ' - Schedule Work Order')

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
            <h2>Schedule Work Order</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="/work-orders">Work Orders</a></li>
                <li class="breadcrumb-item active">Schedule</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
            <a href="/work-orders" class="btn btn-default btn-icon btn-round float-right m-r-10">
                <i class="zmdi zmdi-arrow-left"></i> <span>Back to Work Orders</span>
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
                    <h2><strong>Schedule</strong> Work Order</h2>
                    <small>Fill in the details below to assign maintenance contractors to a logged defect request.</small>
                </div>
                <div class="body">
                    <form action="/work-orders" method="POST" id="workOrderForm">
                        @csrf
                        
                        <div class="form-group">
                            <label for="request_id">Select Unassigned Request <span class="text-danger">*</span></label>
                            <select id="request_id" name="request_id" class="form-control show-tick" required>
                                <option value="" disabled selected>-- Choose Request --</option>
                                @foreach($requests as $req)
                                    <option value="{{ $req['id'] }}">
                                        {{ $req['description'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="vendor_id">Select Maintenance Contractor <span class="text-danger">*</span></label>
                            <select id="vendor_id" name="vendor_id" class="form-control show-tick" required>
                                <option value="" disabled selected>-- Choose Vendor --</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor['id'] }}">{{ $vendor['business_name'] }} ({{ $vendor['category'] }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row clearfix">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="estimated_cost">Estimated Repair Cost (UGX) <span class="text-danger">*</span></label>
                                    <input type="number" id="estimated_cost" name="estimated_cost" class="form-control" placeholder="e.g. 150000" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="scheduled_date">Schedule Execution Date</label>
                                    <input type="date" id="scheduled_date" name="scheduled_date" class="form-control" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sla_completion_time">SLA Completion Limit</label>
                                    <input type="date" id="sla_completion_time" name="sla_completion_time" class="form-control" value="{{ date('Y-m-d', strtotime('+3 days')) }}">
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <a href="/work-orders" class="btn btn-default waves-effect m-r-10">Cancel</a>
                            <button type="submit" class="btn btn-primary waves-effect" id="submit-btn">
                                <i class="zmdi zmdi-calendar-check"></i> Assign Work Order
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
                $('#request_id, #vendor_id').selectpicker();
            }

            // Prevent double-clicking and convert submit button into loading spinner
            $('#workOrderForm').on('submit', function() {
                var $btn = $('#submit-btn');
                if ($btn.data('submitting')) {
                    return false;
                }
                $btn.data('submitting', true);
                $btn.prop('disabled', true);
                var originalHtml = $btn.html();
                $btn.data('original-html', originalHtml);
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Writing order records...');
            });
        });
    </script>
@endsection
