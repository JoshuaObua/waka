@extends('layouts.app')

@section('title', config('company.name') . ' - Expense Management')

@section('styles')
    <!-- JQuery DataTable Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-datatable/dataTables.bootstrap4.min.css') }}">
@endsection

@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-6 col-sm-12">
            <h2>Expense Management</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item active">Expense Management</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
            <a href="/expenses/create" class="btn btn-info float-right mr-2"><i class="zmdi zmdi-plus"></i> Record Expense</a>
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
                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#expenses-tab">Expenses</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#categories-tab">Categories</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-content">
        <!-- Expenses Tab -->
        <div class="tab-pane active" id="expenses-tab">
            <div class="row clearfix">
                <!-- Expenses Table -->
                <div class="col-lg-12 col-md-12">
                    <div class="card">
                        <div class="header">
                            <h2><strong>Expense</strong> Ledger Entries</h2>
                        </div>
                        <div class="body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                    <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th>Category</th>
                                            <th>Amount (UGX)</th>
                                            <th>Status</th>
                                            <th>Date Recorded</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($expenses as $exp)
                                            <tr>
                                                <td class="font-weight-bold">{{ $exp['description'] }}</td>
                                                <td>{{ $exp['category']['name'] ?? 'General' }}</td>
                                                <td class="text-right font-weight-bold text-danger">{{ number_format($exp['amount']) }}</td>
                                                <td>
                                                    @if(($exp['status'] ?? 'pending') === 'approved')
                                                        <span class="badge badge-success text-uppercase">Approved</span>
                                                    @elseif(($exp['status'] ?? 'pending') === 'rejected')
                                                        <span class="badge badge-danger text-uppercase">Rejected</span>
                                                    @else
                                                        <span class="badge badge-warning text-uppercase">Pending</span>
                                                    @endif
                                                </td>
                                                <td class="text-mono small">{{ date('Y-m-d H:i', strtotime($exp['created_at'])) }}</td>
                                                <td>
                                                    @if(($exp['status'] ?? 'pending') === 'pending')
                                                        <div class="d-flex">
                                                            <form action="/expenses/{{ $exp['id'] }}/status" method="POST" class="mr-1 status-form">
                                                                @csrf
                                                                <input type="hidden" name="status" value="approved">
                                                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                                            </form>
                                                            <form action="/expenses/{{ $exp['id'] }}/status" method="POST" class="status-form">
                                                                @csrf
                                                                <input type="hidden" name="status" value="rejected">
                                                                <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                                            </form>
                                                        </div>
                                                    @else
                                                        <span class="text-muted small">Closed</span>
                                                    @endif
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

        <!-- Categories Tab -->
        <div class="tab-pane" id="categories-tab">
            <div class="row clearfix">
                <!-- Categories Table -->
                <div class="col-lg-12 col-md-12">
                    <div class="card">
                        <div class="header">
                            <h2><strong>Expense</strong> Classifications</h2>
                        </div>
                        <div class="body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                    <thead>
                                        <tr>
                                            <th>Category Name</th>
                                            <th>Description</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($categories as $cat)
                                            <tr>
                                                <td class="font-weight-bold">{{ $cat['name'] }}</td>
                                                <td class="text-muted small">{{ $cat['description'] }}</td>
                                                <td>
                                                    <form action="/expenses/categories/{{ $cat['id'] }}/delete" method="POST" class="d-inline delete-form">
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
