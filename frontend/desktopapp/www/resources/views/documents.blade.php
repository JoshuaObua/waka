@extends('layouts.app')

@section('title', config('company.name') . ' - File Manager')

@section('styles')
    <!-- JQuery DataTable Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-datatable/dataTables.bootstrap4.min.css') }}">
    <style>
        .file-box {
            border: 1px solid #eef2f5;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #fff;
            transition: all 0.2s ease-in-out;
        }
        .file-box:hover {
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            transform: translateY(-2px);
        }
        .file-icon {
            font-size: 40px;
            margin-bottom: 10px;
            display: inline-block;
        }
        .file-pdf { color: #f25a5a; }
        .file-excel { color: #2ecc71; }
        .file-word { color: #3498db; }
        .file-image { color: #9b59b6; }
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
            <h2>File Manager (Documents)</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item active">File Manager</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
            <a href="/documents/create" class="btn btn-info float-right mr-2"><i class="zmdi zmdi-upload"></i> Upload Document</a>
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

    <!-- Storage KPI widgets -->
    <div class="row clearfix">
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="card shadow-sm border-0">
                <div class="body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1 text-uppercase">Total Storage</h6>
                            <h4 class="mb-0 font-weight-bold text-dark">1.70 MB</h4>
                        </div>
                        <div class="text-primary"><i class="zmdi zmdi-storage zmdi-hc-3x opacity-20"></i></div>
                    </div>
                    <div class="progress m-t-15" style="height: 6px;">
                        <div class="progress-bar l-blue" role="progressbar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100" style="width: 10%;"></div>
                    </div>
                    <small class="text-muted d-block m-t-5">10% of 100MB quota used</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="card shadow-sm border-0">
                <div class="body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1 text-uppercase">Documents</h6>
                            <h4 class="mb-0 font-weight-bold text-dark">2 Files</h4>
                        </div>
                        <div class="text-success"><i class="zmdi zmdi-file-text zmdi-hc-3x opacity-20"></i></div>
                    </div>
                    <div class="progress m-t-15" style="height: 6px;">
                        <div class="progress-bar l-green" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                    </div>
                    <small class="text-muted d-block m-t-5">Secure database storage</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <!-- Folder Navigation Sidebar -->
        <div class="col-lg-3 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Folder</strong> Navigation</h2>
                </div>
                <div class="body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-light font-weight-bold">
                            <span><i class="zmdi zmdi-folder m-r-10"></i> All Folders</span>
                            <span class="badge badge-primary">2</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="zmdi zmdi-folder-outline m-r-10 text-muted"></i> Leases & Contracts</span>
                            <span class="badge badge-neutral text-muted">1</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="zmdi zmdi-folder-outline m-r-10 text-muted"></i> KYC Submissions</span>
                            <span class="badge badge-neutral text-muted">0</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="zmdi zmdi-folder-outline m-r-10 text-muted"></i> Finance Receipts</span>
                            <span class="badge badge-neutral text-muted">1</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Files DataTable Grid -->
        <div class="col-lg-9 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Onboarded</strong> Files Directory</h2>
                </div>
                <div class="body">
                    <div class="row clearfix m-b-20">
                        @foreach($documents as $doc)
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="file-box text-center shadow-sm">
                                    <div class="file-icon">
                                        @if(($doc['file_type'] ?? '') === 'pdf')
                                            <i class="zmdi zmdi-file-text file-pdf"></i>
                                        @elseif(($doc['file_type'] ?? '') === 'excel')
                                            <i class="zmdi zmdi-grid file-excel"></i>
                                        @else
                                            <i class="zmdi zmdi-file file-word"></i>
                                        @endif
                                    </div>
                                    <h6 class="text-truncate text-dark font-weight-bold mb-1">{{ $doc['name'] }}</h6>
                                    <small class="text-muted d-block mb-2 text-mono">
                                        @if(($doc['file_size'] ?? 0) > 1048576)
                                            {{ number_format(($doc['file_size'] ?? 0) / 1048576, 2) }} MB
                                        @else
                                            {{ number_format(($doc['file_size'] ?? 0) / 1024, 0) }} KB
                                        @endif
                                    </small>
                                    <button class="btn btn-sm btn-outline-primary waves-effect" onclick="Swal.fire('File Download', 'Mock URL generation completed successfully.', 'success')">
                                        <i class="zmdi zmdi-download"></i> Get Link
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                            <thead>
                                <tr>
                                    <th>Document File Name</th>
                                    <th>File Format</th>
                                    <th class="text-right">File Size</th>
                                    <th>Uploaded Date</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documents as $doc)
                                    <tr>
                                        <td class="font-weight-bold">{{ $doc['name'] }}</td>
                                        <td class="text-uppercase text-muted text-mono">{{ $doc['file_type'] }}</td>
                                        <td class="text-right text-mono font-weight-bold">
                                            @if(($doc['file_size'] ?? 0) > 1048576)
                                                {{ number_format(($doc['file_size'] ?? 0) / 1048576, 2) }} MB
                                            @else
                                                {{ number_format(($doc['file_size'] ?? 0) / 1024, 0) }} KB
                                            @endif
                                        </td>
                                        <td>{{ date('Y-m-d H:i', strtotime($doc['created_at'] ?? 'now')) }}</td>
                                        <td class="text-center">
                                            <a href="javascript:void(0);" class="btn btn-sm btn-primary" onclick="Swal.fire('Download Initiated', 'Retrieving dynamic storage handle...', 'success')">
                                                <i class="zmdi zmdi-download"></i>
                                            </a>
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
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('.js-exportable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        });
    </script>
@endsection
