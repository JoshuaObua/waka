@extends('layouts.app')

@section('title', config('company.name') . ' - Rentable Units')

@section('styles')
    <!-- JQuery DataTable Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-datatable/dataTables.bootstrap4.min.css') }}">
    <!-- Bootstrap Select Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-select/css/bootstrap-select.css') }}">
    <style>
        .badge-occupied {
            background-color: #28a745;
            color: #fff;
        }
        .badge-vacant {
            background-color: #dc3545;
            color: #fff;
        }
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
            <h2>Rentable Units Directory</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">Properties & Units</a></li>
                <li class="breadcrumb-item active">Rentable Units</li>
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
        <!-- Onboard Rentable Unit Form -->
        <div class="col-lg-4 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Onboard</strong> Rentable Unit</h2>
                </div>
                <div class="body">
                    <form id="unitForm" action="" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="property_id">Belongs To Property <span class="text-danger">*</span></label>
                            <select id="property_id" name="property_id" class="form-control show-tick" required>
                                <option value="" disabled selected>-- Choose Property --</option>
                                @foreach($properties as $prop)
                                    <option value="{{ $prop['id'] }}">{{ $prop['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="unit_number">Unit / Suite Number <span class="text-danger">*</span></label>
                            <input type="text" id="unit_number" name="unit_number" class="form-control" placeholder="e.g. Suite 101" required>
                        </div>

                        <div class="form-group">
                            <label for="floor_number">Floor Number</label>
                            <input type="number" id="floor_number" name="floor_number" class="form-control" placeholder="e.g. 1" min="0" value="0">
                        </div>

                        <div class="form-group">
                            <label for="category">Category <span class="text-danger">*</span></label>
                            <select id="category" name="category" class="form-control show-tick" required>
                                <option value="commercial" selected>Commercial</option>
                                <option value="residential">Residential</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="type">Unit Type <span class="text-danger">*</span></label>
                            <input type="text" id="type" name="type" class="form-control" placeholder="e.g. office, apartment, shop" required>
                        </div>

                        <div class="form-group">
                            <label for="rent_amount">Monthly Rent (UGX) <span class="text-danger">*</span></label>
                            <input type="number" id="rent_amount" name="rent_amount" class="form-control" placeholder="e.g. 1200000" min="100" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block waves-effect">
                            <i class="zmdi zmdi-plus"></i> Onboard Unit
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Rentable Units DataTable -->
        <div class="col-lg-8 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Active</strong> Rentable Units</h2>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                            <thead>
                                <tr>
                                    <th>Unit Number</th>
                                    <th>Property Name</th>
                                    <th>Floor</th>
                                    <th>Category</th>
                                    <th>Type</th>
                                    <th class="text-right">Rent (UGX)</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($units as $unit)
                                    <tr>
                                        <td class="font-weight-bold text-mono">{{ $unit['unit_number'] }}</td>
                                        <td class="font-weight-bold">{{ $unit['property_name'] ?? 'Acme Plaza' }}</td>
                                        <td>Floor {{ $unit['floor_number'] ?? '0' }}</td>
                                        <td class="text-uppercase">{{ $unit['category'] ?? 'Commercial' }}</td>
                                        <td class="text-capitalize">{{ $unit['type'] ?? 'Office' }}</td>
                                        <td class="text-right font-weight-bold text-dark">{{ number_format($unit['rent_amount']) }}</td>
                                        <td>
                                            @if(($unit['status'] ?? 'vacant') === 'occupied')
                                                <span class="badge badge-occupied">Occupied</span>
                                            @else
                                                <span class="badge badge-vacant">Vacant</span>
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
                $('#property_id, #category').selectpicker();
            }

            // Dynamically update form action when property selection shifts
            $('#property_id').on('change', function() {
                var propId = $(this).val();
                $('#unitForm').attr('action', '/properties/' + propId + '/units');
            });

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
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Writing unit records...');
            });
        });
    </script>
@endsection
