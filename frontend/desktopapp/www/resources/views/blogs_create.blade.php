@extends('layouts.app')

@section('title', config('company.name') . ' - Create Blog Post')

@section('styles')
    <!-- Bootstrap Select Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-select/css/bootstrap-select.css') }}">
    <!-- Sweetalert Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert/sweetalert.css') }}">
@endsection

@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-6 col-sm-12">
            <h2>Create Blog Post</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="/blogs">Blogs</a></li>
                <li class="breadcrumb-item active">Create Post</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
            <a href="/blogs" class="btn btn-default btn-icon btn-round float-right m-r-10">
                <i class="zmdi zmdi-arrow-left"></i> <span>Back to Blogs</span>
            </a>
        </div>
    </div>
</div>

<div class="container-fluid">
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <i class="zmdi zmdi-alert-circle"></i> {{ $errors->first() }}
        </div>
    @endif

    <div class="row clearfix">
        <div class="col-lg-8 col-md-12 offset-lg-2">
            <div class="card">
                <div class="header">
                    <h2><strong>Create</strong> Blog Post</h2>
                    <small>Fill in the details below to compose and publish an announcement or news post.</small>
                </div>
                <div class="body">
                    <form action="/blogs" method="POST" id="blogForm">
                        @csrf
                        
                        <div class="form-group">
                            <label for="title">Post Title <span class="text-danger">*</span></label>
                            <input type="text" id="title" name="title" class="form-control" placeholder="e.g. Easter Holiday Notice" required>
                        </div>

                        <div class="form-group">
                            <label for="content">Post Content <span class="text-danger">*</span></label>
                            <textarea id="content" name="content" rows="6" class="form-control no-resize" placeholder="Write post body here..." required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select id="status" name="status" class="form-control show-tick" required>
                                <option value="draft" selected>Draft</option>
                                <option value="published">Published</option>
                            </select>
                        </div>

                        <div class="text-right">
                            <a href="/blogs" class="btn btn-default waves-effect m-r-10">Cancel</a>
                            <button type="submit" class="btn btn-primary waves-effect" id="submit-btn">
                                <i class="zmdi zmdi-plus"></i> Save Blog Post
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <!-- Bootstrap Select Plugin Js -->
    <script src="{{ asset('assets/plugins/bootstrap-select/js/bootstrap-select.js') }}"></script>
    <!-- Sweetalert Plugin Js -->
    <script src="{{ asset('assets/plugins/sweetalert/sweetalert.min.js') }}"></script>
    <script>
        $(document).ready(font => {
            // Initialize Bootstrap Selectpicker
            if ($.fn.selectpicker) {
                $('#status').selectpicker();
            }

            // Prevent double-clicking and convert submit button into loading spinner
            $('#blogForm').on('submit', function() {
                var $btn = $('#submit-btn');
                if ($btn.data('submitting')) {
                    return false;
                }
                $btn.data('submitting', true);
                $btn.prop('disabled', true);
                var originalHtml = $btn.html();
                $btn.data('original-html', originalHtml);
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Saving...');
            });
        });
    </script>
@endsection
