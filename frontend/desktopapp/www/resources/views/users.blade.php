@extends('layouts.app')

@section('title', config('company.name') . ' - Users List')

@section('styles')
    <!-- JQuery DataTable Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-datatable/dataTables.bootstrap4.min.css') }}">
@endsection

@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-6 col-sm-12">
            <h2>User Management</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">User Management</a></li>
                <li class="breadcrumb-item active">Users List</li>
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
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Registered</strong> Users</h2>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                            <thead>
                                <tr>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Status</th>
                                    <th>Roles</th>
                                    <th class="text-center" style="width: 150px;">Actions</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Status</th>
                                    <th>Roles</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user['first_name'] ?? '' }}</td>
                                        <td>{{ $user['last_name'] ?? '' }}</td>
                                        <td>{{ $user['email'] ?? '' }}</td>
                                        <td>{{ $user['phone_number'] ?? '' }}</td>
                                        <td>
                                            @if(($user['status'] ?? '') === 'active')
                                                <span class="badge badge-success">Active</span>
                                            @elseif(($user['status'] ?? '') === 'suspended')
                                                <span class="badge badge-warning">Suspended</span>
                                            @else
                                                <span class="badge badge-danger">Deleted</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($user['roles']) && is_array($user['roles']))
                                                {{ implode(', ', array_column($user['roles'], 'name')) }}
                                            @else
                                                Guest
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <!-- View Profile -->
                                            <a href="/users/{{ $user['id'] }}" class="btn btn-sm btn-info" title="View Profile"><i class="zmdi zmdi-eye"></i></a>
                                            
                                            <!-- Toggle Status -->
                                            <form action="/users/{{ $user['id'] }}/status" method="POST" style="display:inline-block;">
                                                @csrf
                                                @if(($user['status'] ?? '') === 'active')
                                                    <input type="hidden" name="status" value="suspended">
                                                    <button type="submit" class="btn btn-sm btn-warning" title="Suspend User"><i class="zmdi zmdi-block"></i></button>
                                                @else
                                                    <input type="hidden" name="status" value="active">
                                                    <button type="submit" class="btn btn-sm btn-success" title="Activate User"><i class="zmdi zmdi-check-circle"></i></button>
                                                @endif
                                            </form>
                                            
                                            <!-- Reset Password Trigger -->
                                            <button type="button" class="btn btn-sm btn-primary reset-pwd-btn" 
                                                    data-toggle="modal" data-target="#resetPasswordModal" 
                                                    data-id="{{ $user['id'] }}" data-name="{{ $user['first_name'] }} {{ $user['last_name'] }}" 
                                                    title="Reset Password">
                                                <i class="zmdi zmdi-key"></i>
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

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" role="dialog" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="reset-pwd-form" method="POST" action="">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resetPasswordModalLabel">Reset User Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Reset password for user: <strong id="modal-user-name">User</strong></p>
                    <div class="form-group">
                        <label for="modal-password">New Password</label>
                        <input type="password" class="form-control" name="password" id="modal-password" placeholder="Enter new password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
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
    
    <script src="{{ asset('assets/js/pages/tables/jquery-datatable.js') }}"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Handle Reset Password Modal dynamic user mapping
        $('.reset-pwd-btn').on('click', function () {
            const userId = $(this).data('id');
            const userName = $(this).data('name');
            
            $('#modal-user-name').text(userName);
            $('#reset-pwd-form').attr('action', '/users/' + userId + '/reset-password');
        });
    });
    </script>
@endsection
