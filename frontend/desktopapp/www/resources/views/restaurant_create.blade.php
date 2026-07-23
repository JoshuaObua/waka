@extends('layouts.app')

@section('title', config('company.name') . ' - Add Restaurant Record')

@section('styles')
    <!-- Sweetalert Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert/sweetalert.css') }}">
@endsection

@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-6 col-sm-12">
            <h2>Add Restaurant Record</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item"><a href="/restaurant">Restaurant & Bar</a></li>
                <li class="breadcrumb-item active">Add Record</li>
            </ul>
            <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
        </div>
        <div class="col-lg-5 col-md-6 col-sm-12">                
            <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
            <a href="/restaurant" class="btn btn-default btn-icon btn-round float-right m-r-10">
                <i class="zmdi zmdi-arrow-left"></i> <span>Back to Restaurant</span>
            </a>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row clearfix">
        <div class="col-lg-8 col-md-12 offset-lg-2">
            <div class="card">
                <div class="header">
                    <h2><strong>Add</strong> Menu Item</h2>
                </div>
                <div class="body">
                    <form action="/restaurant/items" method="POST" enctype="multipart/form-data" id="menu-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="item_name">Item Name <span class="text-danger">*</span></label>
                                    <input type="text" id="item_name" name="name" class="form-control" placeholder="e.g. Club Sandwich" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="item_price">Price (UGX) <span class="text-danger">*</span></label>
                                    <input type="number" id="item_price" name="price" class="form-control" placeholder="e.g. 18000" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="item_desc">Description <span class="text-danger">*</span></label>
                            <input type="text" id="item_desc" name="description" class="form-control" placeholder="e.g. Toasted sandwich with chicken" required>
                        </div>

                        <div class="form-group">
                            <label for="photo">Item Photo</label>
                            <input type="file" id="photo" name="photo" class="form-control-file border p-2 rounded">
                        </div>

                        <button type="submit" class="btn btn-primary btn-block btn-round waves-effect">
                            <i class="zmdi zmdi-plus"></i> Save Menu Item
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
            $('#menu-form').on('submit', function() {
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
