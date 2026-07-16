@extends('layouts.app')

@section('title', config('company.name') . ' - GraphQL Explorer')

@section('styles')
    <style>
        .code-textarea {
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            background-color: #1e1e1e;
            color: #d4d4d4;
            padding: 15px;
            border-radius: 4px;
            font-size: 14px;
            line-height: 1.5;
            resize: vertical;
        }
        .result-pre {
            background-color: #2d2d2d;
            color: #9cdcfe;
            padding: 15px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 13px;
            white-space: pre-wrap;
            word-wrap: break-word;
            max-height: 500px;
            overflow-y: auto;
        }
    </style>
@endsection

@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-6 col-sm-12">
            <h2>GraphQL Query Explorer</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item active">GraphQL Explorer</li>
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
        <!-- Query Box -->
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><strong>GraphQL</strong> Query Body</h2>
                </div>
                <div class="body">
                    <form action="/graphql/query" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="query">Input GraphQL Schema Query</label>
                            <textarea id="query" name="query" class="form-control code-textarea" rows="12" required>{{ $query ?: "query {\n  tenant {\n    name\n    propertiesCount\n    totalRentDue\n  }\n}" }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block waves-effect">
                            <i class="zmdi zmdi-play"></i> Execute Query
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Result Box -->
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Aggregated</strong> Response JSON</h2>
                </div>
                <div class="body">
                    @if($result)
                        <pre class="result-pre">@php
                            // Format JSON for pretty printing
                            $decoded = json_decode($result, true);
                            if (json_last_error() === JSON_ERROR_NONE) {
                                echo json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                            } else {
                                echo htmlentities($result);
                            }
                        @endphp</pre>
                    @else
                        <div class="alert alert-neutral text-center">
                            <i class="zmdi zmdi-info-outline zmdi-hc-3x d-block m-b-10 text-muted"></i>
                            Execute a query on the left to pull aggregated metrics from Go's GraphQL gateway.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Prevent double-clicking and convert submit button into loading spinner
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
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Querying gateway...');
            });
        });
    </script>
@endsection
