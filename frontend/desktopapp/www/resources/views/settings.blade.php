@extends('layouts.app')

@section('title', config('company.name') . ' - Settings & Security')

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
            <h2>Settings & Security Center</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item active">Settings & Security</li>
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
                <div class="body">
                    <ul class="nav nav-tabs p-0 mb-3">
                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#security-tab">Security & PIN</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#services-tab">Running Services</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#config-tab">Dynamic Config</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#webhooks-tab">Webhooks</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#notifications-tab">Notifications</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#audit-tab">Audit Trail</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-content">
        <!-- Security Tab (PIN / MFA / Preferences / Active Sessions) -->
        <div class="tab-pane active" id="security-tab">
            <div class="row clearfix">
                <!-- Change PIN & MFA -->
                <div class="col-lg-4 col-md-12">
                    <div class="card">
                        <div class="header">
                            <h2><strong>Security</strong> PIN & 2FA</h2>
                        </div>
                        <div class="body">
                            <form action="/settings/pin" method="POST" class="mb-4">
                                @csrf
                                <div class="form-group">
                                    <label for="old_pin">Current Security PIN <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" id="old_pin" name="old_pin" class="form-control" placeholder="Old PIN" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text toggle-password" onclick="togglePasswordVisibility('old_pin')" style="cursor: pointer;"><i class="zmdi zmdi-eye"></i></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="new_pin">New Security PIN <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" id="new_pin" name="new_pin" class="form-control" placeholder="New PIN" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text toggle-password" onclick="togglePasswordVisibility('new_pin')" style="cursor: pointer;"><i class="zmdi zmdi-eye"></i></span>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block waves-effect">
                                    Update Security PIN
                                </button>
                            </form>

                            <hr>

                            <form action="/settings/mfa" method="POST" class="mt-4">
                                @csrf
                                <h6>MFA Authentication Check</h6>
                                <p class="text-muted small">Force Multi-Factor Authentication token verification for logins.</p>
                                <div class="form-group">
                                    <select name="enabled" class="form-control show-tick">
                                        <option value="0">Disabled</option>
                                        <option value="1">Enabled (Require OTP Token)</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-outline-primary btn-block waves-effect">
                                    Save MFA Policy
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Active Sessions List -->
                <div class="col-lg-8 col-md-12">
                    <div class="card">
                        <div class="header">
                            <h2><strong>Active</strong> User Sessions</h2>
                        </div>
                        <div class="body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>IP Address</th>
                                            <th>Client Device</th>
                                            <th>Last Active</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sessions as $session)
                                            <tr>
                                                <td class="font-weight-bold text-mono">{{ $session['ip_address'] }}</td>
                                                <td class="small">{{ $session['user_agent'] }}</td>
                                                <td class="text-mono small">{{ date('Y-m-d H:i', strtotime($session['last_active'])) }}</td>
                                                <td>
                                                    @if($session['is_current'] ?? false)
                                                        <span class="badge badge-success text-uppercase">Current Session</span>
                                                    @else
                                                        <span class="badge badge-warning text-uppercase">Active</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(!($session['is_current'] ?? false))
                                                        <form action="/settings/sessions/revoke" method="POST" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" name="session_id" value="{{ $session['id'] }}">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">Revoke</button>
                                                        </form>
                                                    @else
                                                        <span class="text-muted small">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <form action="/settings/sessions/revoke-all" method="POST" class="text-right mt-3">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm waves-effect">
                                    <i class="zmdi zmdi-close-circle"></i> Terminate All Other Sessions
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services Tab (Command Center) -->
        <div class="tab-pane" id="services-tab">
            <div class="row clearfix">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="header">
                            <h2><strong>Command</strong> Center Service Controls</h2>
                        </div>
                        <div class="body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Service Daemon</th>
                                            <th>Health Status</th>
                                            <th>Uptime</th>
                                            <th>CPU</th>
                                            <th>Memory</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($serviceStatus as $svc)
                                            <tr>
                                                <td class="font-weight-bold text-primary">{{ $svc['name'] }}</td>
                                                <td>
                                                    @if(($svc['status'] ?? 'stopped') === 'running')
                                                        <span class="badge badge-success text-uppercase">Active</span>
                                                    @else
                                                        <span class="badge badge-danger text-uppercase">Stopped</span>
                                                    @endif
                                                </td>
                                                <td class="text-mono">{{ $svc['uptime'] }}</td>
                                                <td class="text-mono">{{ $svc['cpu'] }}</td>
                                                <td class="text-mono">{{ $svc['memory'] }}</td>
                                                <td>
                                                    <div class="d-flex">
                                                        <form action="/settings/services/action" method="POST" class="mr-1">
                                                            @csrf
                                                            <input type="hidden" name="service" value="{{ $svc['name'] }}">
                                                            <input type="hidden" name="action" value="start">
                                                            <button type="submit" class="btn btn-sm btn-success" {{ ($svc['status'] ?? 'stopped') === 'running' ? 'disabled' : '' }}>Start</button>
                                                        </form>
                                                        <form action="/settings/services/action" method="POST" class="mr-1">
                                                            @csrf
                                                            <input type="hidden" name="service" value="{{ $svc['name'] }}">
                                                            <input type="hidden" name="action" value="stop">
                                                            <button type="submit" class="btn btn-sm btn-danger" {{ ($svc['status'] ?? 'stopped') === 'stopped' ? 'disabled' : '' }}>Stop</button>
                                                        </form>
                                                        <form action="/settings/services/action" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="service" value="{{ $svc['name'] }}">
                                                            <input type="hidden" name="action" value="restart">
                                                            <button type="submit" class="btn btn-sm btn-warning">Restart</button>
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

        <!-- Dynamic Config Tab -->
        <div class="tab-pane" id="config-tab">
            <div class="row clearfix">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="header">
                            <h2><strong>Update</strong> Dynamic Configuration JSON File</h2>
                        </div>
                        <div class="body">
                            <form action="/settings/config" method="POST">
                                @csrf
                                <div class="form-group">
                                    <textarea name="config" rows="12" class="form-control text-mono font-weight-bold" style="background-color: #2b2b2b; color: #a9b7c6; font-size: 14px; padding: 15px; border-radius: 4px;" required>{{ $configContent }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary waves-effect">
                                    Save Configuration Parameters
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Webhooks Tab -->
        <div class="tab-pane" id="webhooks-tab">
            <div class="row clearfix">
                <!-- Add Webhook -->
                <div class="col-lg-4 col-md-12">
                    <div class="card">
                        <div class="header">
                            <h2><strong>Register</strong> Webhook Target</h2>
                        </div>
                        <div class="body">
                            <form action="/settings/webhooks" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="url">Endpoint URL <span class="text-danger">*</span></label>
                                    <input type="url" id="url" name="url" class="form-control" placeholder="https://external-ledger.com/webhook" required>
                                </div>

                                <div class="form-group">
                                    <label for="events">Subscribed Event Triggers (comma-separated) <span class="text-danger">*</span></label>
                                    <input type="text" id="events" name="events" class="form-control" placeholder="payment.received,lease.approved" required>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block waves-effect">
                                    Subscribe to Webhook
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Webhooks Table -->
                <div class="col-lg-8 col-md-12">
                    <div class="card">
                        <div class="header">
                            <h2><strong>Active</strong> Webhook Subscriptions</h2>
                        </div>
                        <div class="body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Webhook Target URL</th>
                                            <th>Subscribed Events</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($webhooks as $wh)
                                            <tr>
                                                <td class="font-weight-bold text-mono">{{ $wh['url'] }}</td>
                                                <td>
                                                    @foreach(explode(',', $wh['events']) as $evt)
                                                        <span class="badge badge-info text-uppercase">{{ trim($evt) }}</span>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    <form action="/settings/webhooks/{{ $wh['id'] }}/delete" method="POST" class="d-inline">
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

        <!-- Notifications Tab -->
        <div class="tab-pane" id="notifications-tab">
            <div class="row clearfix">
                <!-- Template Editor -->
                <div class="col-lg-4 col-md-12">
                    <div class="card">
                        <div class="header">
                            <h2><strong>Design</strong> Message Template</h2>
                        </div>
                        <div class="body">
                            <form action="/settings/notifications" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="channel">Channel <span class="text-danger">*</span></label>
                                    <select id="channel" name="channel" class="form-control show-tick" required>
                                        <option value="sms" selected>SMS Text</option>
                                        <option value="email">Email Address</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="template_name">Template Identifier <span class="text-danger">*</span></label>
                                    <input type="text" id="template_name" name="template_name" class="form-control" placeholder="e.g. Rent Reminder" required>
                                </div>

                                <div class="form-group">
                                    <label for="template_content">Template Body (Variables allowed: {name}, {unit}) <span class="text-danger">*</span></label>
                                    <textarea id="template_content" name="content" rows="4" class="form-control no-resize" required></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block waves-effect">
                                    Save Notification Template
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Template list -->
                <div class="col-lg-8 col-md-12">
                    <div class="card">
                        <div class="header">
                            <h2><strong>Active</strong> Notification Templates</h2>
                        </div>
                        <div class="body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Template Name</th>
                                            <th>Channel</th>
                                            <th>Template Body Preview</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($notifications as $ntf)
                                            <tr>
                                                <td class="font-weight-bold">{{ $ntf['template_name'] }}</td>
                                                <td><span class="badge badge-primary text-uppercase">{{ $ntf['channel'] }}</span></td>
                                                <td class="text-muted small">{{ $ntf['content'] }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-icon btn-outline-primary"
                                                            onclick="editTemplate('{{ $ntf['channel'] }}', '{{ addslashes($ntf['template_name']) }}', '{{ addslashes($ntf['content']) }}')">
                                                        <i class="zmdi zmdi-edit"></i>
                                                    </button>
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

        <!-- Audit Trail Tab -->
        <div class="tab-pane" id="audit-tab">
            <div class="row clearfix">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="header">
                            <h2><strong>Audit</strong> Log Trail</h2>
                        </div>
                        <div class="body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                    <thead>
                                        <tr>
                                            <th>Actor Profile</th>
                                            <th>Operation Action</th>
                                            <th>Target Category</th>
                                            <th>Operation Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($auditLogs as $log)
                                            <tr>
                                                <td class="font-weight-bold">{{ $log['actor'] }}</td>
                                                <td class="text-uppercase text-mono">{{ $log['action'] }}</td>
                                                <td class="text-muted">{{ $log['target'] }}</td>
                                                <td class="text-mono small">{{ date('Y-m-d H:i:s', strtotime($log['created_at'])) }}</td>
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

            // Initialize bootstrap selectors
            if ($.fn.selectpicker) {
                $('#channel').selectpicker();
            }

            // Spinner submit buttons
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
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Saving settings...');
            });

            // Alerts
            @if(session('success'))
                swal("Success", "{{ session('success') }}", "success");
            @endif

            @if($errors->any())
                swal("Error", "{{ $errors->first() }}", "error");
            @endif
        });

        function togglePasswordVisibility(id) {
            var input = document.getElementById(id);
            var icon = $(input).closest('.input-group').find('.toggle-password i');
            if (input.type === "password") {
                input.type = "text";
                icon.removeClass('zmdi-eye').addClass('zmdi-eye-off');
            } else {
                input.type = "password";
                icon.removeClass('zmdi-eye-off').addClass('zmdi-eye');
            }
        }

        function editTemplate(channel, name, content) {
            $('#channel').val(channel);
            if ($.fn.selectpicker) {
                $('#channel').selectpicker('refresh');
            }
            $('#template_name').val(name);
            $('#template_content').val(content);
        }
    </script>
@endsection
