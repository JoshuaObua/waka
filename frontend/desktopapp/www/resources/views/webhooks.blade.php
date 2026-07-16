@extends('layouts.app')

@section('title', config('company.name') . ' - Webhook Subscriptions')

@section('styles')
    <!-- JQuery DataTable Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-datatable/dataTables.bootstrap4.min.css') }}">
    <!-- Bootstrap Select Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-select/css/bootstrap-select.css') }}">
    <style>
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
            <h2>Webhook Subscriptions</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">System & Integrations</a></li>
                <li class="breadcrumb-item active">Webhooks</li>
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
        <!-- Subscribe Form -->
        <div class="col-lg-4 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Subscribe</strong> Webhook Target</h2>
                </div>
                <div class="body">
                    <form action="/webhooks" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="target_url">Target Listener URL <span class="text-danger">*</span></label>
                            <input type="url" id="target_url" name="target_url" class="form-control" placeholder="https://api.yourdomain.com/webhooks" required>
                        </div>

                        <div class="form-group">
                            <label for="event_type">Target Notification Event <span class="text-danger">*</span></label>
                            <select id="event_type" name="event_type" class="form-control show-tick" required>
                                <option value="invoice.paid" selected>invoice.paid</option>
                                <option value="invoice.overdue">invoice.overdue</option>
                                <option value="lease.approved">lease.approved</option>
                                <option value="lease.terminated">lease.terminated</option>
                                <option value="maintenance.request_created">maintenance.request_created</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block waves-effect">
                            <i class="zmdi zmdi-notifications-active"></i> Create Subscription
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Subscriptions DataTable -->
        <div class="col-lg-8 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Active</strong> Webhook Hooks</h2>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                            <thead>
                                <tr>
                                    <th>Event Hook</th>
                                    <th>Target Endpoint URL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subscriptions as $sub)
                                    <tr>
                                        <td class="font-weight-bold text-success text-mono">{{ $sub['event_type'] }}</td>
                                        <td class="text-mono">{{ $sub['target_url'] }}</td>
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
                $('#event_type').selectpicker();
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
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Saving subscription...');
            });
        });
    </script>
@endsection
