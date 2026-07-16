@extends('layouts.app')

@section('title', config('company.name') . ' - Audit Logs')

@section('styles')
    <!-- JQuery DataTable Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-datatable/dataTables.bootstrap4.min.css') }}">
    <style>
        .badge-success-custom { background-color: #28a745; color: #fff; }
        .badge-danger-custom { background-color: #dc3545; color: #fff; }
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
            <h2>Audit Trail logs</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">System & Integrations</a></li>
                <li class="breadcrumb-item active">Audit Logs</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Security</strong> Audit Trail</h2>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                            <thead>
                                <tr>
                                    <th>Action Logged</th>
                                    <th>Actor Email</th>
                                    <th>IP Address</th>
                                    <th>Status</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                    <tr>
                                        <td class="font-weight-bold text-dark text-mono">{{ $log['action'] }}</td>
                                        <td>{{ $log['actor_email'] }}</td>
                                        <td class="text-mono">{{ $log['ip_address'] }}</td>
                                        <td>
                                            @if(($log['status'] ?? 'success') === 'success')
                                                <span class="badge badge-success-custom">Success</span>
                                            @else
                                                <span class="badge badge-danger-custom">Failed</span>
                                            @endif
                                        </td>
                                        <td>{{ date('Y-m-d H:i:s', strtotime($log['timestamp'])) }}</td>
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
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('.js-exportable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                order: [[4, "desc"]]
            });
        });
    </script>
@endsection
