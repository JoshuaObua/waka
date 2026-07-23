@extends('layouts.app')

@section('title', config('company.name') . ' - Record Expense')

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
            <h2>Record Expense</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="/expenses">Expense Management</a></li>
                <li class="breadcrumb-item active">Record Expense</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
            <a href="/expenses" class="btn btn-default btn-icon btn-round float-right m-r-10">
                <i class="zmdi zmdi-arrow-left"></i> <span>Back to Expenses</span>
            </a>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- Tab Navigation -->
    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card">
                <div class="body">
                    <ul class="nav nav-tabs p-0 mb-3">
                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#expense-tab">Record Expense</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#category-tab">Add Category</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-content">
        <!-- Record Expense Tab -->
        <div class="tab-pane active" id="expense-tab">
            <div class="row clearfix">
                <div class="col-lg-8 col-md-12 offset-lg-2">
                    <div class="card">
                        <div class="header">
                            <h2><strong>Record</strong> System Expense</h2>
                        </div>
                        <div class="body">
                            <form action="/expenses" method="POST" enctype="multipart/form-data" id="expense-form">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="category_id">Expense Category <span class="text-danger">*</span></label>
                                            <select id="category_id" name="category_id" class="form-control show-tick" required>
                                                @foreach($categories as $cat)
                                                    <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="amount">Amount (UGX) <span class="text-danger">*</span></label>
                                            <input type="number" id="amount" name="amount" class="form-control" placeholder="e.g. 450000" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="desc">Description <span class="text-danger">*</span></label>
                                    <input type="text" id="desc" name="description" class="form-control" placeholder="e.g. Umeme Office Bills" required>
                                </div>

                                <div class="form-group">
                                    <label for="voucher">Voucher / Receipt Attachment</label>
                                    <input type="file" id="voucher" name="voucher" class="form-control-file border p-2 rounded">
                                </div>

                                <button type="submit" class="btn btn-primary btn-block btn-round waves-effect">
                                    <i class="zmdi zmdi-plus"></i> Record Expense
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Category Tab -->
        <div class="tab-pane" id="category-tab">
            <div class="row clearfix">
                <div class="col-lg-8 col-md-12 offset-lg-2">
                    <div class="card">
                        <div class="header">
                            <h2><strong>Add</strong> Expense Category</h2>
                        </div>
                        <div class="body">
                            <form action="/expenses/categories" method="POST" id="category-form">
                                @csrf
                                <div class="form-group">
                                    <label for="cat_name">Category Name <span class="text-danger">*</span></label>
                                    <input type="text" id="cat_name" name="name" class="form-control" placeholder="e.g. Repairs" required>
                                </div>

                                <div class="form-group">
                                    <label for="cat_desc">Description <span class="text-danger">*</span></label>
                                    <input type="text" id="cat_desc" name="description" class="form-control" placeholder="e.g. Office renovations" required>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block btn-round waves-effect">
                                    <i class="zmdi zmdi-plus"></i> Create Category
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <!-- Sweetalert Plugin Js -->
    <script src="{{ asset('assets/plugins/sweetalert/sweetalert.min.js') }}"></script>
    <!-- Bootstrap Select Plugin Js -->
    <script src="{{ asset('assets/plugins/bootstrap-select/js/bootstrap-select.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize selectpickers
            if ($.fn.selectpicker) {
                $('#category_id').selectpicker();
            }

            // Double-click prevention with loading spinner
            $('#expense-form, #category-form').on('submit', function() {
                var $form = $(this);
                var $btn = $form.find('button[type="submit"]');
                
                if ($btn.data('submitting')) {
                    return false;
                }
                
                $btn.data('submitting', true);
                $btn.prop('disabled', true);
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Saving records...');
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
