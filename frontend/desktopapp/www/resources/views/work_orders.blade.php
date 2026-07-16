@extends('layouts.app')

@section('title', config('company.name') . ' - Work Orders')

@section('styles')
    <!-- JQuery DataTable Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-datatable/dataTables.bootstrap4.min.css') }}">
    <!-- Bootstrap Select Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-select/css/bootstrap-select.css') }}">
    <style>
        .badge-scheduled { background-color: #ffc107; color: #212529; }
        .badge-in_progress { background-color: #007bff; color: #fff; }
        .badge-completed { background-color: #28a745; color: #fff; }
        .text-mono {
            font-family: monospace;
            font-size: 13px;
        }
    </style>
@endsection

@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-6 col-sm-12">
            <h2>Maintenance Work Orders</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">Maintenance & Vendors</a></li>
                <li class="breadcrumb-item active">Work Orders</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
        </div>
    </div>
</div>

<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <i class="zmdi zmdi-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <i class="zmdi zmdi-alert-circle"></i> {{ $errors->first() }}
        </div>
    @endif

    <div class="row clearfix">
        <!-- Schedule Work Order Form -->
        <div class="col-lg-4 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Schedule</strong> Work Order</h2>
                </div>
                <div class="body">
                    <form action="/work-orders" method="POST">
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

                        <div class="form-group">
                            <label for="estimated_cost">Estimated Repair Cost (UGX) <span class="text-danger">*</span></label>
                            <input type="number" id="estimated_cost" name="estimated_cost" class="form-control" placeholder="e.g. 150000" min="0" required>
                        </div>

                        <div class="form-group">
                            <label for="scheduled_date">Schedule Execution Date</label>
                            <input type="date" id="scheduled_date" name="scheduled_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="form-group">
                            <label for="sla_completion_time">SLA Completion Limit</label>
                            <input type="date" id="sla_completion_time" name="sla_completion_time" class="form-control" value="{{ date('Y-m-d', strtotime('+3 days')) }}">
                        </div>

                        <button type="submit" class="btn btn-primary btn-block waves-effect">
                            <i class="zmdi zmdi-calendar-check"></i> Assign Work Order
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Work Orders DataTable -->
        <div class="col-lg-8 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Scheduled</strong> Repair Orders</h2>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                            <thead>
                                <tr>
                                    <th>Repair Request</th>
                                    <th>Assigned Vendor</th>
                                    <th class="text-right">Estimated Cost (UGX)</th>
                                    <th>Scheduled Date</th>
                                    <th>SLA Target</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($workOrders as $wo)
                                    <tr>
                                        <td>{{ $wo['request_desc'] ?? 'General repair order' }}</td>
                                        <td class="font-weight-bold">{{ $wo['vendor_name'] ?? 'Unassigned' }}</td>
                                        <td class="text-right text-dark font-weight-bold">{{ number_format($wo['estimated_cost']) }}</td>
                                        <td>{{ date('Y-m-d', strtotime($wo['scheduled_date'] ?? 'now')) }}</td>
                                        <td>{{ date('Y-m-d', strtotime($wo['sla_completion_time'] ?? '+3 days')) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $wo['status'] ?? 'scheduled' }}">{{ ucfirst($wo['status'] ?? 'scheduled') }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <!-- Jquery DataTable Plugin Js -->
    <script src="{{ asset('assets/bundles/datatablescripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-datatable/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-datatable/buttons/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-datatable/buttons/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-datatable/buttons/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-datatable/buttons/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-datatable/buttons/buttons.print.min.js') }}"></script>
    <!-- Bootstrap Select Plugin Js -->
    <script src="{{ asset('assets/plugins/bootstrap-select/js/bootstrap-select.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('.js-exportable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

            // Initialize Bootstrap Selectpicker
            if ($.fn.selectpicker) {
                $('#request_id, #vendor_id').selectpicker();
            }

            // Prevent double-clicking and convert submit button into loading spinner
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
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Writing order records...');
            });
        });
    </script>
@endsection
