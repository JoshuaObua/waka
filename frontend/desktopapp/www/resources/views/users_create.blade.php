@extends('layouts.app')

@section('title', config('company.name') . ' - Add New User')

@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-6 col-sm-12">
            <h2>Add New User</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="/users">User Management</a></li>
                <li class="breadcrumb-item active">Add User</li>
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
                <div class="header">
                    <h2><strong>New User</strong> Registration Form</h2>
                </div>
                <div class="body">
                    @if($errors->has('create'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ $errors->first('create') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form action="/users" method="POST">
                        @csrf
                        
                        <div class="row clearfix">
                            <!-- First Name -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="first_name">First Name</label>
                                    <input type="text" name="first_name" id="first_name" class="form-control" placeholder="Enter first name" value="{{ old('first_name') }}" required>
                                </div>
                            </div>
                            
                            <!-- Last Name -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Enter last name" value="{{ old('last_name') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix">
                            <!-- Email -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" name="email" id="email" class="form-control" placeholder="Enter email address" value="{{ old('email') }}" required>
                                </div>
                            </div>
                            
                            <!-- Phone Number -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone_number">Phone Number</label>
                                    <input type="text" name="phone_number" id="phone_number" class="form-control" placeholder="Enter phone number (e.g. +256701234567)" value="{{ old('phone_number') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix">
                            <!-- Password -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter temporary password" required>
                                </div>
                            </div>
                        </div>

                        <!-- Roles Section -->
                        <div class="row clearfix">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><strong>Assign System Roles</strong></label>
                                    <div class="mt-2">
                                        @foreach($roles as $role)
                                            <div class="checkbox inlineblock mr-3">
                                                <input id="role_{{ $role['id'] }}" name="role_ids[]" type="checkbox" value="{{ $role['id'] }}">
                                                <label for="role_{{ $role['id'] }}">{{ $role['name'] }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary waves-effect mr-2">Create User</button>
                            <a href="/users" class="btn btn-secondary waves-effect">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
