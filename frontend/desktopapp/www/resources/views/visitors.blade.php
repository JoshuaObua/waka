@extends('layouts.app')

@section('title', config('company.name') . ' - Visitor Management')

@section('styles')
    <!-- JQuery DataTable Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-datatable/dataTables.bootstrap4.min.css') }}">
    <style>
        .badge-checkin { background-color: #28a745; color: #fff; }
        .badge-checkout { background-color: #6c757d; color: #fff; }
        .badge-registered { background-color: #ffc107; color: #212529; }
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
            <h2>Visitor Logs & Management</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">Visitor Management</a></li>
                <li class="breadcrumb-item active">Logs</li>
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
        <!-- Pre-Register Form -->
        <div class="col-lg-4 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Pre-Register</strong> Visitor</h2>
                </div>
                <div class="body">
                    <form action="/visitors" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="full_name">Visitor Full Name <span class="text-danger">*</span></label>
                            <input type="text" id="full_name" name="full_name" class="form-control" placeholder="e.g. John Doe" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Visitor Phone Number <span class="text-danger">*</span></label>
                            <input type="text" id="phone" name="phone" class="form-control" placeholder="e.g. +256701122334" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Visitor Email Address <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="e.g. john.doe@gmail.com" required>
                        </div>

                        <div class="form-group">
                            <label for="host_name">Host Label / Unit Owner <span class="text-danger">*</span></label>
                            <input type="text" id="host_name" name="host_name" class="form-control" placeholder="e.g. Jane Mugisha" required>
                        </div>

                        <div class="form-group">
                            <label for="purpose">Purpose of Visit <span class="text-danger">*</span></label>
                            <input type="text" id="purpose" name="purpose" class="form-control" placeholder="e.g. Delivery, Repairs, Meeting" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block waves-effect">
                            <i class="zmdi zmdi-walk"></i> Pre-Register
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Visitors list DataTable -->
        <div class="col-lg-8 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Visitor</strong> Records Logs</h2>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                            <thead>
                                <tr>
                                    <th>Visitor Detail</th>
                                    <th>Purpose</th>
                                    <th>Host Name</th>
                                    <th>Status / Timeline</th>
                                    <th class="text-center" style="width: 130px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($visitors as $v)
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold">{{ $v['full_name'] }}</div>
                                            <small class="text-mono text-muted">{{ $v['phone'] }} | {{ $v['email'] }}</small>
                                        </td>
                                        <td class="text-capitalize">{{ $v['purpose'] }}</td>
                                        <td class="font-weight-bold text-dark">{{ $v['host_name'] }}</td>
                                        <td>
                                            @if($v['check_out_time'])
                                                <span class="badge badge-checkout">Checked Out</span>
                                                <small class="d-block text-muted">Out: {{ date('H:i Y-m-d', strtotime($v['check_out_time'])) }}</small>
                                            @elseif($v['check_in_time'])
                                                <span class="badge badge-checkin">Checked In</span>
                                                <small class="d-block text-muted">In: {{ date('H:i Y-m-d', strtotime($v['check_in_time'])) }}</small>
                                            @else
                                                <span class="badge badge-registered">Pre-Registered</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if(!$v['check_in_time'] && !$v['check_out_time'])
                                                <form action="/visitors/{{ $v['id'] }}/check-in" method="POST" class="d-inline status-form">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success waves-effect" title="Check-In Visitor">
                                                        <i class="zmdi zmdi-sign-in"></i> In
                                                    </button>
                                                </form>
                                            @elseif($v['check_in_time'] && !$v['check_out_time'])
                                                <form action="/visitors/{{ $v['id'] }}/check-out" method="POST" class="d-inline status-form">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger waves-effect" title="Check-Out Visitor">
                                                        <i class="zmdi zmdi-sign-out"></i> Out
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted"><i class="zmdi zmdi-check-all"></i> Completed</span>
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
                ]
            });

            // Prevent double click and show circular progress spinner
            $('form, .status-form').on('submit', function() {
                var $form = $(this);
                var $btn = $form.find('button[type="submit"]');
                
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
