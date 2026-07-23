@extends('layouts.app')

@section('title', config('company.name') . ' - Upload Document')

@section('styles')
    <!-- Sweetalert Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert/sweetalert.css') }}">
@endsection

@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-6 col-sm-12">
            <h2>Upload Document</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="/documents">File Manager</a></li>
                <li class="breadcrumb-item active">Upload Document</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
            <a href="/documents" class="btn btn-default btn-icon btn-round float-right m-r-10">
                <i class="zmdi zmdi-arrow-left"></i> <span>Back to Files</span>
            </a>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row clearfix">
        <div class="col-lg-8 col-md-12 offset-lg-2">
            <div class="card">
                <div class="header">
                    <h2><strong>Upload</strong> Document</h2>
                </div>
                <div class="body">
                    <form action="/documents/upload" method="POST" enctype="multipart/form-data" id="upload-form">
                        @csrf
                        <div class="form-group">
                            <label for="doc-name">File Name <span class="text-danger">*</span></label>
                            <input type="text" id="doc-name" name="name" class="form-control" placeholder="e.g. ID_Photocopy_Tenant" required>
                        </div>

                        <div class="form-group">
                            <label for="doc-folder">Target Folder</label>
                            <select id="doc-folder" name="folder" class="form-control">
                                <option value="general">General</option>
                                <option value="leases">Leases & Contracts</option>
                                <option value="kyc">KYC Submissions</option>
                                <option value="finance">Finance Receipts</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="doc-file">Select File <span class="text-danger">*</span></label>
                            <input type="file" id="doc-file" name="file" class="form-control-file border p-2 rounded" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block btn-round waves-effect" id="upload-btn">
                            <i class="zmdi zmdi-upload"></i> Upload File
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <!-- Sweetalert Plugin Js -->
    <script src="{{ asset('assets/plugins/sweetalert/sweetalert.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Double-click prevention with loading spinner
            $('#upload-form').on('submit', function() {
                var $btn = $('#upload-btn');
                
                if ($btn.data('submitting')) {
                    return false;
                }
                
                $btn.data('submitting', true);
                $btn.prop('disabled', true);
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Uploading...');
            });

            // SweetAlert notifications
            @if(session('success'))
                swal("Success", "{{ session('success') }}", "success");
            @endif

            @if($errors->any())
                swal("Error", "{{ $errors->first() }}", "error");
            @endif
        });
    </script>
@endsection
