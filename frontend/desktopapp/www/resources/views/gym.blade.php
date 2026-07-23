@extends('layouts.app')

@section('title', config('company.name') . ' - Gym Subscriptions')

@section('styles')
    <!-- JQuery DataTable Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-datatable/dataTables.bootstrap4.min.css') }}">
    <!-- Sweetalert Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert/sweetalert.css') }}">
    <!-- Bootstrap Select Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-select/css/bootstrap-select.css') }}">
@endsection

@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-6 col-sm-12">
            <h2>Gym Subscriptions</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item active">Gym Subscriptions</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
            <a href="/gym/create" class="btn btn-info float-right mr-2"><i class="zmdi zmdi-plus"></i> Create Gym Record</a>
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

    <!-- Tab Navigation -->
    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card">
                <div class="body">
                    <ul class="nav nav-tabs p-0 mb-3">
                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#subscriptions-tab">Client Subscriptions</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#plans-tab">Gym Plans</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-content">
        <!-- Subscriptions Tab -->
        <div class="tab-pane active" id="subscriptions-tab">
            <div class="row clearfix">
                <!-- Subscriptions Table -->
                <div class="col-lg-12 col-md-12">
                    <div class="card">
                        <div class="header">
                            <h2><strong>Gym</strong> Memberships</h2>
                        </div>
                        <div class="body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                    <thead>
                                        <tr>
                                            <th>Client Name</th>
                                            <th>Gym Plan</th>
                                            <th>Status</th>
                                            <th>Validity</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subscriptions as $sub)
                                            <tr>
                                                <td class="font-weight-bold">{{ $sub['client_name'] }}</td>
                                                <td>{{ $sub['plan_name'] }}</td>
                                                <td>
                                                    @if(($sub['status'] ?? 'active') === 'active')
                                                        <span class="badge badge-success text-uppercase">Active</span>
                                                    @else
                                                        <span class="badge badge-warning text-uppercase">{{ $sub['status'] }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-mono small">{{ $sub['start_date'] }} to {{ $sub['end_date'] }}</td>
                                                <td>
                                                    <form action="/gym/subscriptions/{{ $sub['id'] }}/status" method="POST" class="d-inline status-form">
                                                        @csrf
                                                        <input type="hidden" name="status" value="{{ ($sub['status'] ?? 'active') === 'active' ? 'expired' : 'active' }}">
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                            Toggle Status
                                                        </button>
                                                    </form>
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

        <!-- Plans Tab -->
        <div class="tab-pane" id="plans-tab">
            <div class="row clearfix">
                <!-- Plans Table -->
                <div class="col-lg-12 col-md-12">
                    <div class="card">
                        <div class="header">
                            <h2><strong>Available</strong> Membership Packages</h2>
                        </div>
                        <div class="body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                    <thead>
                                        <tr>
                                            <th>Plan Name</th>
                                            <th>Price (UGX)</th>
                                            <th>Duration</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($plans as $plan)
                                            <tr>
                                                <td class="font-weight-bold">{{ $plan['name'] }}</td>
                                                <td class="font-weight-bold text-success">{{ number_format($plan['price']) }}</td>
                                                <td>{{ $plan['duration_days'] }} Days</td>
                                                <td>
                                                    <form action="/gym/plans/{{ $plan['id'] }}/delete" method="POST" class="d-inline delete-form">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger">
                                                            <i class="zmdi zmdi-delete"></i>
                                                        </button>
                                                    </form>
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
    </div>
</div>
@endsection

@section('scripts')
    <!-- Jquery DataTable Plugin Js -->
    <script src="{{ asset('assets/bundles/datatablescripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-datatable/buttons/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-datatable/buttons/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-datatable/buttons/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-datatable/buttons/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-datatable/buttons/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-datatable/buttons/buttons.print.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTables
            $('.js-exportable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

            // Prevent double-clicking and convert submit button into loading spinner on inline post-back forms
            $('.status-form, .delete-form').on('submit', function() {
                var $form = $(this);
                var $btn = $form.find('button[type="submit"]');
                
                if ($btn.data('submitting')) {
                    return false;
                }
                
                $btn.data('submitting', true);
                $btn.prop('disabled', true);
                var originalHtml = $btn.html();
                $btn.data('original-html', originalHtml);
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i>');
            });
        });
    </script>
@endsection
