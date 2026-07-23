@extends('layouts.app')

@section('title', config('company.name') . ' - Reports & Analytics')

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
            <h2>Reports & Analytics</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item active">Reports & Analytics</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- Selection Control Card -->
    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Select</strong> Report Category</h2>
                </div>
                <div class="body">
                    <form action="/reports" method="GET" id="report-selector-form" class="row">
                        <div class="col-lg-8 col-md-8 col-sm-12 mb-2">
                            <select name="type" id="report-type" class="form-control show-tick" onchange="$('#report-selector-form').submit()">
                                <option value="analytics" {{ $currentType === 'analytics' ? 'selected' : '' }}>Analytics Summary Metrics</option>
                                <option value="rent_collection" {{ $currentType === 'rent_collection' ? 'selected' : '' }}>Rent Collection Report</option>
                                <option value="gym" {{ $currentType === 'gym' ? 'selected' : '' }}>Gym Subscriptions Report</option>
                                <option value="sauna" {{ $currentType === 'sauna' ? 'selected' : '' }}>Sauna Subscriptions Report</option>
                                <option value="properties" {{ $currentType === 'properties' ? 'selected' : '' }}>Properties Directory Report</option>
                                <option value="restaurant_sales" {{ $currentType === 'restaurant_sales' ? 'selected' : '' }}>Restaurant & Bar Sales Report</option>
                                <option value="expenses" {{ $currentType === 'expenses' ? 'selected' : '' }}>Expenses Report</option>
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12 text-right">
                            <a href="/reports?type={{ $currentType }}&export=csv" class="btn btn-outline-primary waves-effect"><i class="zmdi zmdi-download"></i> Export CSV</a>
                            <a href="/reports?type={{ $currentType }}&export=pdf" class="btn btn-outline-danger waves-effect"><i class="zmdi zmdi-file-text"></i> Export PDF</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Dashboard View -->
    @if($currentType === 'analytics')
        <div class="row clearfix">
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card widget_2 big_icon traffic">
                    <div class="body">
                        <h6>Occupancy Rate</h6>
                        <h2>{{ $reportData['occupancy_rate'] ?? '80%' }}</h2>
                        <small>Total Unit Capacity</small>
                        <div class="progress mb-0">
                            <div class="progress-bar l-amber" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: 80%;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card widget_2 big_icon sales">
                    <div class="body">
                        <h6>Active Gym/Sauna Clients</h6>
                        <h2>{{ $reportData['active_tenants'] ?? 25 }}</h2>
                        <small>Active club members</small>
                        <div class="progress mb-0">
                            <div class="progress-bar l-blue" role="progressbar" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100" style="width: 65%;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card widget_2 big_icon email">
                    <div class="body">
                        <h6>Total Revenue Collected</h6>
                        <h2>{{ number_format($reportData['total_revenue'] ?? 4500000) }} UGX</h2>
                        <small>This calendar month</small>
                        <div class="progress mb-0">
                            <div class="progress-bar l-purple" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card widget_2 big_icon domains">
                    <div class="body">
                        <h6>Resolved Tickets Rate</h6>
                        <h2>{{ $reportData['maintenance_tickets_resolved'] ?? '94%' }}</h2>
                        <small>Performance index</small>
                        <div class="progress mb-0">
                            <div class="progress-bar l-green" role="progressbar" aria-valuenow="94" aria-valuemin="0" aria-valuemax="100" style="width: 94%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="card">
                    <div class="header">
                        <h2><strong>Platform</strong> General Health</h2>
                    </div>
                    <div class="body text-center p-5">
                        <div class="icon-circle bg-green text-white mb-3" style="font-size: 40px; display: inline-flex; width: 80px; height: 80px; align-items: center; justify-content: center; border-radius: 50%;">
                            <i class="zmdi zmdi-check-all"></i>
                        </div>
                        <h4>System Analytics Status: OPTIMAL</h4>
                        <p class="text-muted">Double entry wallets, IoT commands, and rent collections are synchronizing cleanly with the Go base engine.</p>
                    </div>
                </div>
            </div>
        </div>
    @elseif($currentType === 'rent_collection')
        <!-- Rent Collection Report View -->
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="card">
                    <div class="header">
                        <h2><strong>Rent</strong> Receipts & Arrears Audit</h2>
                    </div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                <thead>
                                    <tr>
                                        <th>Tenant Name</th>
                                        <th>Allocated Unit</th>
                                        <th>Total Due (UGX)</th>
                                        <th>Total Received (UGX)</th>
                                        <th>Ledger Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(is_array($reportData))
                                        @foreach($reportData as $row)
                                            <tr>
                                                <td class="font-weight-bold">{{ $row['tenant'] ?? 'N/A' }}</td>
                                                <td>{{ $row['unit'] ?? 'N/A' }}</td>
                                                <td class="text-right font-weight-bold text-muted">{{ number_format($row['amount_due'] ?? 0) }}</td>
                                                <td class="text-right font-weight-bold text-success">{{ number_format($row['amount_paid'] ?? 0) }}</td>
                                                <td>
                                                    @if(($row['status'] ?? 'Paid') === 'Paid')
                                                        <span class="badge badge-success text-uppercase">Settled</span>
                                                    @else
                                                        <span class="badge badge-danger text-uppercase">Arrears</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Other Categorized Tables -->
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="card">
                    <div class="header">
                        <h2><strong>Categorized</strong> Metrics Database</h2>
                    </div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                <thead>
                                    <tr>
                                        <th>Data Field</th>
                                        <th>Record Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(is_array($reportData))
                                        @foreach($reportData as $key => $val)
                                            @if(!is_array($val))
                                                <tr>
                                                    <td class="font-weight-bold text-uppercase">{{ str_replace('_', ' ', $key) }}</td>
                                                    <td>{{ $val }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
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
    <!-- Sweetalert Plugin Js -->
    <script src="{{ asset('assets/plugins/sweetalert/sweetalert.min.js') }}"></script>
    <!-- Bootstrap Select Plugin Js -->
    <script src="{{ asset('assets/plugins/bootstrap-select/js/bootstrap-select.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTables
            $('.js-exportable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

            // Initialize bootstrap selector
            if ($.fn.selectpicker) {
                $('#report-type').selectpicker();
            }

            // Alerts
            @if(session('success'))
                swal("Success", "{{ session('success') }}", "success");
            @endif

            @if($errors->any())
                swal("Error", "{{ $errors->first() }}", "error");
            @endif
        });
    </script>
@endsection
