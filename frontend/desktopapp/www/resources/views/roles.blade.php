@extends('layouts.app')

@section('title', config('company.name') . ' - Roles & Permissions')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert/sweetalert.css') }}">
    <style>
        .category-header {
            background-color: #f4f6f9;
            font-weight: 700;
            color: #495057;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }
        .perm-code {
            font-family: monospace;
            color: #e83e8c;
            font-size: 12px;
        }
        .table-responsive {
            max-height: 700px;
            overflow-y: auto;
        }
        .matrix-table th {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 10;
            box-shadow: 0 2px 2px -1px rgba(0,0,0,0.1);
        }
    </style>
@endsection

@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-6 col-sm-12">
            <h2>Roles & Permissions Matrix</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">User Management</a></li>
                <li class="breadcrumb-item active">Roles & Permissions</li>
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
        <!-- Left Panel: Role Creation -->
        <div class="col-lg-4 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Create</strong> Custom Role</h2>
                </div>
                <div class="body">
                    <form action="/roles" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="role_name">Role Name <span class="text-danger">*</span></label>
                            <input type="text" id="role_name" name="name" class="form-control" placeholder="e.g. Property Manager" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="role_desc">Description</label>
                            <textarea id="role_desc" name="description" rows="3" class="form-control" placeholder="Brief explanation of access level..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block waves-effect">
                            <i class="zmdi zmdi-plus"></i> Create Role
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="header">
                    <h2><strong>Information</strong> Summary</h2>
                </div>
                <div class="body">
                    <p class="text-muted">Predefined system roles (such as <strong class="text-primary">Super Admin</strong> and <strong class="text-primary">Tenant</strong>) cannot be deleted as they govern core tenancy features.</p>
                    <p class="text-muted mb-0">Checking/unchecking boxes in the matrix updates the role permissions dynamically on the Go backend using active context credentials.</p>
                </div>
            </div>
        </div>

        <!-- Right Panel: Permission Matrix -->
        <div class="col-lg-8 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Access Control</strong> Matrix</h2>
                </div>
                <div class="body">
                    @php
                        $groupedPermissions = [];
                        foreach ($permissions as $perm) {
                            $cat = $perm['category'] ?? 'General';
                            $groupedPermissions[$cat][] = $perm;
                        }
                    @endphp

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered matrix-table mb-0">
                            <thead>
                                <tr>
                                    <th>Permission Modules</th>
                                    @foreach($roles as $role)
                                        <th class="text-center">
                                            <div>{{ $role['name'] }}</div>
                                            @if($role['tenant_id'] !== null)
                                                <form action="/roles/{{ $role['id'] }}/delete" method="POST" class="d-inline delete-role-form">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-icon btn-neutral text-danger p-0 m-0" title="Delete custom role">
                                                        <i class="zmdi zmdi-delete"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groupedPermissions as $category => $perms)
                                    <tr>
                                        <td colspan="{{ count($roles) + 1 }}" class="category-header">
                                            <i class="zmdi zmdi-folder-outline"></i> {{ $category }}
                                        </td>
                                    </tr>
                                    @foreach($perms as $perm)
                                        <tr>
                                            <td>
                                                <div class="font-weight-bold">{{ $perm['description'] }}</div>
                                                <span class="perm-code">{{ $perm['code'] }}</span>
                                            </td>
                                            @foreach($roles as $role)
                                                <td class="text-center">
                                                    <div class="checkbox">
                                                        <input type="checkbox" 
                                                               id="chk_{{ $role['id'] }}_{{ $perm['id'] }}" 
                                                               class="matrix-checkbox"
                                                               data-role-id="{{ $role['id'] }}"
                                                               data-permission-id="{{ $perm['id'] }}"
                                                               {{ in_array($perm['id'], $rolePermissions[$role['id']] ?? []) ? 'checked' : '' }}>
                                                        <label for="chk_{{ $role['id'] }}_{{ $perm['id'] }}"></label>
                                                    </div>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
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
    <script src="{{ asset('assets/plugins/sweetalert/sweetalert.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Prevent double-clicking on standard submit forms and show dynamic spinner
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
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Processing...');
            });

            // Prevent custom role delete double click
            $('.delete-role-form').on('submit', function(e) {
                e.preventDefault();
                var form = this;
                
                swal({
                    title: "Are you sure?",
                    text: "Once deleted, this custom role cannot be recovered!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        form.submit();
                    }
                });
            });

            // Matrix Checkbox Change AJAX handler
            $('.matrix-checkbox').on('change', function() {
                var $checkbox = $(this);
                var roleId = $checkbox.data('role-id');
                
                // Get all checked permission IDs for this role column
                var checkedPerms = [];
                $('.matrix-checkbox[data-role-id="' + roleId + '"]:checked').each(function() {
                    checkedPerms.push($(this).data('permission-id'));
                });
                
                // Disable all checkboxes for this role during submission to avoid duplicate actions
                var $roleCheckboxes = $('.matrix-checkbox[data-role-id="' + roleId + '"]');
                $roleCheckboxes.prop('disabled', true);
                
                $.ajax({
                    url: '/roles/' + roleId + '/permissions',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        permission_ids: checkedPerms
                    },
                    success: function(response) {
                        $roleCheckboxes.prop('disabled', false);
                        swal({
                            title: "Success!",
                            text: response.message,
                            icon: "success",
                            button: "OK"
                        });
                    },
                    error: function(xhr) {
                        $roleCheckboxes.prop('disabled', false);
                        // Revert check state on error
                        $checkbox.prop('checked', !$checkbox.prop('checked'));
                        
                        var errMsg = "Failed to update role permissions.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }
                        swal({
                            title: "Error!",
                            text: errMsg,
                            icon: "error",
                            button: "OK"
                        });
                    }
                });
            });
        });
    </script>
@endsection
