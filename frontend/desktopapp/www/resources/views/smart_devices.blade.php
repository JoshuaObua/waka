@extends('layouts.app')

@section('title', config('company.name') . ' - Smart Devices')

@section('styles')
    <!-- JQuery DataTable Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-datatable/dataTables.bootstrap4.min.css') }}">
@endsection

@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-6 col-sm-12">
            <h2>Smart Devices (IoT)</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item active">Smart Devices</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
            <a href="/smart-devices/create" class="btn btn-info float-right mr-2"><i class="zmdi zmdi-plus"></i> Register Device</a>
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
        <!-- Devices List -->
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Connected</strong> Smart IoT Devices</h2>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                            <thead>
                                <tr>
                                    <th>Device Name</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>JSON Configuration</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($devices as $dev)
                                    <tr>
                                        <td class="font-weight-bold">{{ $dev['name'] }}</td>
                                        <td class="text-uppercase small">{{ $dev['device_type'] }}</td>
                                        <td>
                                            @if(($dev['status'] ?? 'offline') === 'online')
                                                <span class="badge badge-success text-uppercase">Online</span>
                                            @else
                                                <span class="badge badge-danger text-uppercase">Offline</span>
                                            @endif
                                        </td>
                                        <td class="text-mono small">{{ $dev['parameters'] }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <button class="btn btn-sm btn-icon btn-outline-primary mr-1" 
                                                        onclick="editDevice('{{ $dev['id'] }}', '{{ addslashes($dev['name']) }}', '{{ addslashes($dev['parameters']) }}')">
                                                    <i class="zmdi zmdi-edit"></i>
                                                </button>
                                                <form action="/smart-devices/{{ $dev['id'] }}/delete" method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-icon btn-outline-danger">
                                                        <i class="zmdi zmdi-delete"></i>
                                                    </button>
                                                </form>
                                            </div>
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

<!-- Edit Device Modal -->
<div class="modal fade" id="editDeviceModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editDeviceForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><strong>Edit</strong> Smart Device Settings</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_name">Device Label <span class="text-danger">*</span></label>
                        <input type="text" id="edit_name" name="name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_parameters">Parameters (JSON) <span class="text-danger">*</span></label>
                        <textarea id="edit_parameters" name="parameters" rows="4" class="form-control no-resize text-mono" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary waves-effect">Save Settings</button>
                </div>
            </form>
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
            $('.delete-form, #editDeviceForm').on('submit', function() {
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

        function editDevice(id, name, parameters) {
            var $modal = $('#editDeviceModal');
            $modal.find('#editDeviceForm').attr('action', '/smart-devices/' + id + '/update');
            $modal.find('#edit_name').val(name);
            $modal.find('#edit_parameters').val(parameters);
            $modal.modal('show');
        }
    </script>
@endsection
