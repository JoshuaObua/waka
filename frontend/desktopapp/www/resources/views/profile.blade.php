@extends('layouts.app')

@section('title', config('company.name') . ' - User Profile')

@section('content')
@php
    $isTenant = false;
    foreach ($user['roles'] ?? [] as $role) {
        if (strtolower($role['name'] ?? '') === 'tenant') {
            $isTenant = true;
            break;
        }
    }
@endphp

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
                        @if($isTenant)
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#kyc" role="tab">KYC Details</a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#transactions" role="tab">Transactions</a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#invoices" role="tab">Invoices</a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#bills" role="tab">Bills</a></li>
                        @endif
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
                        
                        @if($isTenant)
                            <!-- Tab 2: KYC Details (Dummy Data) -->
                            <div class="tab-pane" id="kyc" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <tbody>
                                            <tr>
                                                <td><strong>ID Type</strong></td>
                                                <td>National ID Card</td>
                                            </tr>
                                            <tr>
                                                <td><strong>ID Number</strong></td>
                                                <td>CM95018128XJ</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Verification Status</strong></td>
                                                <td><span class="badge badge-success">Verified</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Guarantor Name</strong></td>
                                                <td>Peter Mugisha</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Guarantor Phone</strong></td>
                                                <td>+256 772 999999</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Bank Name</strong></td>
                                                <td>Stanbic Bank</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Bank Account Number</strong></td>
                                                <td>9080001234567</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Tab 3: Transactions (Dummy Data) -->
                            <div class="tab-pane" id="transactions" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Transaction ID</th>
                                                <th>Type</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>2026-07-16</td>
                                                <td>TXN-90812</td>
                                                <td>Wallet Top-up</td>
                                                <td>45,000 UGX</td>
                                                <td><span class="badge badge-success">Completed</span></td>
                                            </tr>
                                            <tr>
                                                <td>2026-07-16</td>
                                                <td>TXN-90813</td>
                                                <td>Rent Invoice Payment</td>
                                                <td>60,000 UGX</td>
                                                <td><span class="badge badge-success">Completed</span></td>
                                            </tr>
                                            <tr>
                                                <td>2026-07-15</td>
                                                <td>TXN-90814</td>
                                                <td>Utility Bill Payout</td>
                                                <td>15,000 UGX</td>
                                                <td><span class="badge badge-success">Completed</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Tab 4: Invoices (Dummy Data) -->
                            <div class="tab-pane" id="invoices" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Invoice ID</th>
                                                <th>Billing Period</th>
                                                <th>Due Date</th>
                                                <th>Total Amount</th>
                                                <th>Paid Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>INV-10029</td>
                                                <td>June 2026</td>
                                                <td>2026-07-20</td>
                                                <td>60,000 UGX</td>
                                                <td>60,000 UGX</td>
                                                <td><span class="badge badge-success">Paid</span></td>
                                            </tr>
                                            <tr>
                                                <td>INV-10030</td>
                                                <td>July 2026</td>
                                                <td>2026-08-20</td>
                                                <td>60,000 UGX</td>
                                                <td>0 UGX</td>
                                                <td><span class="badge badge-danger">Unpaid</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Tab 5: Bills (Dummy Data) -->
                            <div class="tab-pane" id="bills" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Bill ID</th>
                                                <th>Meter Number</th>
                                                <th>Type</th>
                                                <th>Previous Reading</th>
                                                <th>Current Reading</th>
                                                <th>Rate / Unit</th>
                                                <th>Total Charge</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>BILL-502</td>
                                                <td>ELEC-90812</td>
                                                <td>Electricity</td>
                                                <td>12,500.00</td>
                                                <td>12,750.00</td>
                                                <td>12.50 UGX</td>
                                                <td>3,125 UGX</td>
                                                <td><span class="badge badge-success">Paid</span></td>
                                            </tr>
                                            <tr>
                                                <td>BILL-503</td>
                                                <td>WAT-90812</td>
                                                <td>Water</td>
                                                <td>450.00</td>
                                                <td>480.00</td>
                                                <td>5.00 UGX</td>
                                                <td>150 UGX</td>
                                                <td><span class="badge badge-danger">Unpaid</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Tab: Admin Actions -->
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
