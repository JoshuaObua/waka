@extends('layouts.app')

@section('title', config('company.name') . ' - Blogs CMS')

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
            <h2>Blogs CMS</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item active">Blogs CMS</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
            <a href="/blogs/create" class="btn btn-info float-right mr-2"><i class="zmdi zmdi-plus"></i> Create Blog Post</a>
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
        <!-- Blog Posts DataTable -->
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Manage</strong> Blog Articles</h2>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Excerpt</th>
                                    <th>Status</th>
                                    <th>Date Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($blogs as $blog)
                                    <tr>
                                        <td class="font-weight-bold">{{ $blog['title'] }}</td>
                                        <td class="text-muted">{{ Str::limit($blog['content'], 60) }}</td>
                                        <td>
                                            @if(($blog['status'] ?? 'draft') === 'published')
                                                <span class="badge badge-success text-uppercase">Published</span>
                                            @else
                                                <span class="badge badge-warning text-uppercase">Draft</span>
                                            @endif
                                        </td>
                                        <td class="text-mono">{{ date('Y-m-d H:i', strtotime($blog['created_at'])) }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <button class="btn btn-sm btn-icon btn-outline-primary mr-1" 
                                                        onclick="editPost('{{ $blog['id'] }}', '{{ addslashes($blog['title']) }}', '{{ addslashes($blog['content']) }}', '{{ $blog['status'] }}')">
                                                    <i class="zmdi zmdi-edit"></i>
                                                </button>
                                                <form action="/blogs/{{ $blog['id'] }}/delete" method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-icon btn-outline-danger">
                                                        <i class="zmdi zmdi-delete"></i>
                                                    </button>
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

<!-- Edit Post Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><strong>Edit</strong> Blog Post</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_title">Post Title <span class="text-danger">*</span></label>
                        <input type="text" id="edit_title" name="title" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_content">Post Content <span class="text-danger">*</span></label>
                        <textarea id="edit_content" name="content" rows="6" class="form-control no-resize" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="edit_status">Status <span class="text-danger">*</span></label>
                        <select id="edit_status" name="status" class="form-control show-tick" required>
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary waves-effect" id="edit-submit-btn">Save Changes</button>
                </div>
            </form>
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
            // Initialize DataTable
            $('.js-exportable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

            // Prevent double-clicking and convert submit button into loading spinner
            $('#editForm').on('submit', function() {
                var $btn = $('#edit-submit-btn');
                if ($btn.data('submitting')) {
                    return false;
                }
                $btn.data('submitting', true);
                $btn.prop('disabled', true);
                var originalHtml = $btn.html();
                $btn.data('original-html', originalHtml);
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Saving...');
            });

            $('.delete-form').on('submit', function() {
                var $btn = $(this).find('button[type="submit"]');
                if ($btn.data('submitting')) {
                    return false;
                }
                $btn.data('submitting', true);
                $btn.prop('disabled', true);
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i>');
            });
        });

        function editPost(id, title, content, status) {
            var $modal = $('#editModal');
            $modal.find('#editForm').attr('action', '/blogs/' + id + '/update');
            $modal.find('#edit_title').val(title);
            $modal.find('#edit_content').val(content);
            $modal.find('#edit_status').val(status);
            if ($.fn.selectpicker) {
                $modal.find('#edit_status').selectpicker('refresh');
            }
            $modal.modal('show');
        }
    </script>
@endsection
