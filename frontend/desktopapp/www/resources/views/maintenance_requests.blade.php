@extends('layouts.app')

@section('title', config('company.name') . ' - Maintenance Requests')

@section('styles')
    <!-- JQuery DataTable Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-datatable/dataTables.bootstrap4.min.css') }}">
    <!-- Bootstrap Select Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-select/css/bootstrap-select.css') }}">
    <style>
        .badge-emergency { background-color: #dc3545; color: #fff; }
        .badge-high { background-color: #fd7e14; color: #fff; }
        .badge-medium { background-color: #007bff; color: #fff; }
        .badge-low { background-color: #6c757d; color: #fff; }
        
        .badge-pending { background-color: #ffc107; color: #212529; }
        .badge-in_progress { background-color: #17a2b8; color: #fff; }
        .badge-completed { background-color: #28a745; color: #fff; }
    </style>
@endsection

@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-6 col-sm-12">
            <h2>Maintenance Requests</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">Maintenance & Vendors</a></li>
                <li class="breadcrumb-item active">Requests</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
            <a href="/maintenance-requests/create" class="btn btn-info float-right mr-2"><i class="zmdi zmdi-plus"></i> Log Maintenance Request</a>
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
        <!-- Requests List -->
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Logged</strong> Requests</h2>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                            <thead>
                                <tr>
                                    <th>Unit / Target</th>
                                    <th>Category</th>
                                    <th>Priority</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Date Logged</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $req)
                                    <tr>
                                        <td>
                                            @if(isset($req['unit']))
                                                <span class="font-weight-bold text-mono">{{ $req['unit']['unit_number'] }}</span>
                                            @else
                                                <span class="text-muted">General Organization</span>
                                            @endif
                                            @if(isset($req['tenant_profile']['user']))
                                                <small class="text-muted d-block">By: {{ $req['tenant_profile']['user']['first_name'] }} {{ $req['tenant_profile']['user']['last_name'] }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $req['category'] }}</td>
                                        <td>
                                            <span class="badge badge-{{ $req['priority'] }}">{{ ucfirst($req['priority']) }}</span>
                                        </td>
                                        <td>{{ $req['description'] }}</td>
                                        <td>
                                            <span class="badge badge-{{ $req['status'] }}">{{ str_replace('_', ' ', ucfirst($req['status'])) }}</span>
                                        </td>
                                        <td>{{ date('Y-m-d H:i', strtotime($req['created_at'] ?? 'now')) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('.js-exportable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                order: [[5, "desc"]]
            });
        });
    </script>
@endsection
