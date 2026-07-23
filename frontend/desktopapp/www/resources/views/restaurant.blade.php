@extends('layouts.app')

@section('title', config('company.name') . ' - Restaurant & Bar')

@section('styles')
    <!-- JQuery DataTable Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-datatable/dataTables.bootstrap4.min.css') }}">
@endsection

@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-6 col-sm-12">
            <h2>Restaurant & Bar</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item active">Restaurant & Bar</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
            <a href="/restaurant/create" class="btn btn-info float-right mr-2"><i class="zmdi zmdi-plus"></i> Add Menu Item</a>
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
                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#orders-tab">Active Orders</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#menu-tab">Menu Items</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-content">
        <!-- Orders Tab -->
        <div class="tab-pane active" id="orders-tab">
            <div class="row clearfix">
                <!-- Orders Table -->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="header">
                            <h2><strong>Active</strong> Service Orders</h2>
                        </div>
                        <div class="body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Location / Table</th>
                                            <th>Items Details</th>
                                            <th>Bill (UGX)</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($orders as $order)
                                            <tr>
                                                <td class="font-weight-bold text-mono">{{ $order['id'] }}</td>
                                                <td class="font-weight-bold">{{ $order['table_number'] }}</td>
                                                <td>{{ $order['items'] }}</td>
                                                <td class="text-right font-weight-bold text-success">{{ number_format($order['total_amount']) }}</td>
                                                <td>
                                                    @if(($order['status'] ?? 'pending') === 'completed')
                                                        <span class="badge badge-success text-uppercase">Completed</span>
                                                    @else
                                                        <span class="badge badge-warning text-uppercase">Pending</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <form action="/restaurant/orders/{{ $order['id'] }}/status" method="POST" class="d-inline status-form">
                                                        @csrf
                                                        <input type="hidden" name="status" value="{{ ($order['status'] ?? 'pending') === 'pending' ? 'completed' : 'pending' }}">
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

        <!-- Menu Tab -->
        <div class="tab-pane" id="menu-tab">
            <div class="row clearfix">
                <!-- Menu Items Table -->
                <div class="col-lg-12 col-md-12">
                    <div class="card">
                        <div class="header">
                            <h2><strong>Available</strong> Foods & Beverages</h2>
                        </div>
                        <div class="body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                    <thead>
                                        <tr>
                                            <th>Item Name</th>
                                            <th>Description</th>
                                            <th>Price (UGX)</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $item)
                                            <tr>
                                                <td class="font-weight-bold">{{ $item['name'] }}</td>
                                                <td class="text-muted small">{{ $item['description'] }}</td>
                                                <td class="font-weight-bold text-success text-right">{{ number_format($item['price']) }}</td>
                                                <td>
                                                    <form action="/restaurant/items/{{ $item['id'] }}/delete" method="POST" class="d-inline delete-form">
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

            // Prevent double-clicking on inline action forms
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
