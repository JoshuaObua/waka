@extends('layouts.app')

@section('title', config('company.name') . ' - Vendors Directory')

@section('styles')
    <!-- JQuery DataTable Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-datatable/dataTables.bootstrap4.min.css') }}">
    <!-- Bootstrap Select Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-select/css/bootstrap-select.css') }}">
@endsection

@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-6 col-sm-12">
            <h2>Vendors Directory</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">Maintenance & Vendors</a></li>
                <li class="breadcrumb-item active">Vendors Directory</li>
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
        <!-- Onboard Vendor Form -->
        <div class="col-lg-4 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Onboard</strong> Maintenance Vendor</h2>
                </div>
                <div class="body">
                    <form action="/vendors" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="business_name">Business Name <span class="text-danger">*</span></label>
                            <input type="text" id="business_name" name="business_name" class="form-control" placeholder="e.g. Kampala Plumbing Masters Ltd" required>
                        </div>

                        <div class="form-group">
                            <label for="contact_name">Primary Contact Name <span class="text-danger">*</span></label>
                            <input type="text" id="contact_name" name="contact_name" class="form-control" placeholder="e.g. Andrew Mukasa" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" id="phone" name="phone" class="form-control" placeholder="e.g. +256772112233" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="e.g. contact@kplumbing.com" required>
                        </div>

                        <div class="form-group">
                            <label for="category">Specialty Category <span class="text-danger">*</span></label>
                            <select id="category" name="category" class="form-control show-tick" required>
                                <option value="Plumbing" selected>Plumbing</option>
                                <option value="Electrical">Electrical</option>
                                <option value="Carpentry">Carpentry</option>
                                <option value="HVAC">HVAC</option>
                                <option value="Roofing">Roofing</option>
                                <option value="General">General Maintenance</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block waves-effect">
                            <i class="zmdi zmdi-plus"></i> Onboard Vendor
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Vendors DataTable -->
        <div class="col-lg-8 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Active</strong> Service Providers</h2>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                            <thead>
                                <tr>
                                    <th>Business Name</th>
                                    <th>Specialty</th>
                                    <th>Contact Person</th>
                                    <th>Phone Number</th>
                                    <th>Email Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vendors as $vendor)
                                    <tr>
                                        <td class="font-weight-bold">{{ $vendor['business_name'] }}</td>
                                        <td class="text-uppercase text-muted">{{ $vendor['category'] }}</td>
                                        <td>{{ $vendor['contact_name'] }}</td>
                                        <td class="font-weight-bold text-mono">{{ $vendor['phone'] }}</td>
                                        <td class="text-mono">{{ $vendor['email'] }}</td>
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
                $('#category').selectpicker();
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
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Writing vendor records...');
            });
        });
    </script>
@endsection
