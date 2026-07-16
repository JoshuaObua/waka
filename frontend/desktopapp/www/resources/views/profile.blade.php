@extends('layouts.app')

@section('title', config('company.name') . ' - User Profile')

@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-6 col-sm-12">
            <h2>User Profile</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="/users">User Management</a></li>
                <li class="breadcrumb-item active">Profile</li>
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
        <!-- Sidebar profile card -->
        <div class="col-lg-4 col-md-12">
            <div class="card mcard_3">
                <div class="body text-center">
                    <a href="javascript:void(0);"><img src="{{ asset('assets/images/profile_av.jpg') }}" class="rounded-circle shadow mb-3" alt="profile-image" width="120"></a>
                    <h4>{{ $user['first_name'] ?? '' }} {{ $user['last_name'] ?? '' }}</h4>                            
                    <p class="text-muted mb-2">{{ implode(', ', array_column($user['roles'] ?? [], 'name')) }}</p>
                    
                    @if(($user['status'] ?? '') === 'active')
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-danger">Suspended</span>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="header">
                    <h2><strong>Contact</strong> Details</h2>
                </div>
                <div class="body">
                    <small class="text-muted">Email address: </small>
                    <p>{{ $user['email'] ?? 'N/A' }}</p>
                    <hr>
                    <small class="text-muted">Phone: </small>
                    <p>{{ $user['phone_number'] ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
        
        <!-- Main profile tabs -->
        <div class="col-lg-8 col-md-12">
            <div class="card">
                <div class="body">
                    <ul class="nav nav-tabs p-0 mb-3" role="tablist">
                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#details" role="tab">Account Info</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#actions" role="tab">Administrative Actions</a></li>
                    </ul>
                    
                    <div class="tab-content">
                        <!-- Tab 1: Info -->
                        <div class="tab-pane active" id="details" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <td><strong>User ID</strong></td>
                                            <td>{{ $user['id'] ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>First Name</strong></td>
                                            <td>{{ $user['first_name'] ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Last Name</strong></td>
                                            <td>{{ $user['last_name'] ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email</strong></td>
                                            <td>{{ $user['email'] ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phone Number</strong></td>
                                            <td>{{ $user['phone_number'] ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Account Status</strong></td>
                                            <td>
                                                @if(($user['status'] ?? '') === 'active')
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Suspended</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Tab 2: Admin Actions -->
                        <div class="tab-pane" id="actions" role="tabpanel">
                            <!-- Toggle status card -->
                            <div class="card border mb-3">
                                <div class="header">
                                    <h2><strong>Account Status</strong> Operations</h2>
                                </div>
                                <div class="body">
                                    <p>Suspend or activate this user's ability to login to the system.</p>
                                    <form action="/users/{{ $user['id'] }}/status" method="POST">
                                        @csrf
                                        @if(($user['status'] ?? '') === 'active')
                                            <input type="hidden" name="status" value="suspended">
                                            <button type="submit" class="btn btn-warning waves-effect">Suspend User Account</button>
                                        @else
                                            <input type="hidden" name="status" value="active">
                                            <button type="submit" class="btn btn-success waves-effect">Activate User Account</button>
                                        @endif
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Password reset card -->
                            <div class="card border mb-3">
                                <div class="header">
                                    <h2><strong>Reset</strong> Password</h2>
                                </div>
                                <div class="body">
                                    <p>Reset this user's password directly. The new password will take effect instantly.</p>
                                    <form action="/users/{{ $user['id'] }}/reset-password" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <label for="new_password">New Password</label>
                                            <input type="password" class="form-control" name="password" id="new_password" placeholder="Enter new password" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary waves-effect">Reset Password</button>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Delete simulated card -->
                            <div class="card border border-danger">
                                <div class="header bg-red text-white p-3">
                                    <h2 class="text-white"><strong>Danger Zone</strong> - Delete Account</h2>
                                </div>
                                <div class="body">
                                    <p>Removing this user will permanently invalidate their token sessions. Deleting sets their state to inactive.</p>
                                    <form action="/users/{{ $user['id'] }}/status" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="deleted">
                                        <button type="submit" class="btn btn-danger btn-block waves-effect" onclick="return confirm('Are you sure you want to delete this user?');">Delete User Account</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
