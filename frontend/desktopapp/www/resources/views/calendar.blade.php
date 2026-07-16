@extends('layouts.app')

@section('title', config('company.name') . ' - Events Calendar')

@section('styles')
    <style>
        .calendar-container {
            background-color: #fff;
            border-radius: 4px;
            padding: 20px;
        }
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background-color: #eef2f5;
            border: 1px solid #eef2f5;
        }
        .calendar-day-header {
            background-color: #f8f9fa;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            color: #495057;
            font-size: 13px;
        }
        .calendar-day {
            background-color: #fff;
            min-height: 100px;
            padding: 8px;
            position: relative;
        }
        .calendar-day.empty {
            background-color: #f8f9fa;
        }
        .day-number {
            font-size: 12px;
            font-weight: bold;
            color: #6c757d;
            margin-bottom: 5px;
        }
        .calendar-event {
            font-size: 11px;
            padding: 3px 6px;
            border-radius: 3px;
            margin-bottom: 3px;
            color: #fff;
            font-weight: 500;
        }
        .event-blue { background-color: #007bff; }
        .event-green { background-color: #28a745; }
        .event-orange { background-color: #ff9800; }
        .event-red { background-color: #dc3545; }
    </style>
@endsection

@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-6 col-sm-12">
            <h2>Events & Property Schedules Calendar</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> {{ config('company.name') }}</a></li>
                <li class="breadcrumb-item active">Calendar</li>
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
        <!-- Event Categories sidebar -->
        <div class="col-lg-3 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><strong>Schedule</strong> Categories</h2>
                </div>
                <div class="body">
                    <div class="event-tag m-b-15">
                        <span class="badge badge-success p-2 d-block text-left"><i class="zmdi zmdi-receipt m-r-10"></i> Rent Due Invoice Alert</span>
                    </div>
                    <div class="event-tag m-b-15">
                        <span class="badge badge-primary p-2 d-block text-left"><i class="zmdi zmdi-wrench m-r-10"></i> Maintenance Work Order</span>
                    </div>
                    <div class="event-tag m-b-15">
                        <span class="badge badge-warning p-2 d-block text-left"><i class="zmdi zmdi-walk m-r-10"></i> Visitor Check-in Event</span>
                    </div>
                    <div class="event-tag m-b-15">
                        <span class="badge badge-danger p-2 d-block text-left"><i class="zmdi zmdi-shield-security m-r-10"></i> Lease Agreement Expiry</span>
                    </div>
                </div>
            </div>

            <!-- Quick Add Event (Static) -->
            <div class="card">
                <div class="header">
                    <h2><strong>Create</strong> Reminder</h2>
                </div>
                <div class="body">
                    <form action="javascript:void(0);" id="event-form">
                        <div class="form-group">
                            <label for="event-title">Reminder Title</label>
                            <input type="text" id="event-title" class="form-control" placeholder="e.g. Clean main lobby water tank" required>
                        </div>
                        <div class="form-group">
                            <label for="event-date">Target Date</label>
                            <input type="date" id="event-date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block waves-effect" id="save-btn">
                            <i class="zmdi zmdi-plus"></i> Add Event
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Monthly Calendar View -->
        <div class="col-lg-9 col-md-12">
            <div class="card">
                <div class="body">
                    <div class="calendar-container shadow-sm">
                        <div class="calendar-header">
                            <h4 class="font-weight-bold text-dark mb-0"><i class="zmdi zmdi-calendar-note text-primary"></i> July 2026</h4>
                            <div>
                                <button class="btn btn-sm btn-outline-secondary" onclick="Swal.fire('Info', 'Previous month loading...', 'info')"><i class="zmdi zmdi-chevron-left"></i> Prev</button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="Swal.fire('Info', 'Next month loading...', 'info')">Next <i class="zmdi zmdi-chevron-right"></i></button>
                            </div>
                        </div>

                        <div class="calendar-grid">
                            <!-- Weekday Headers -->
                            <div class="calendar-day-header">Sun</div>
                            <div class="calendar-day-header">Mon</div>
                            <div class="calendar-day-header">Tue</div>
                            <div class="calendar-day-header">Wed</div>
                            <div class="calendar-day-header">Thu</div>
                            <div class="calendar-day-header">Fri</div>
                            <div class="calendar-day-header">Sat</div>

                            <!-- Blank Days (July 2026 starts on Wednesday) -->
                            <div class="calendar-day empty"></div>
                            <div class="calendar-day empty"></div>
                            <div class="calendar-day empty"></div>

                            <!-- Month Days -->
                            @for ($day = 1; $day <= 31; $day++)
                                <div class="calendar-day">
                                    <div class="day-number">{{ $day }}</div>
                                    
                                    <!-- Hardcoded scheduling dates matching walkthrough events -->
                                    @if($day === 1)
                                        <div class="calendar-event event-green" title="Monthly billing invoices issued.">Rent Invoices Issued</div>
                                    @endif

                                    @if($day === 10)
                                        <div class="calendar-event event-green" title="Grace period deadline for rent settlement.">Grace Period Due</div>
                                    @endif

                                    @if($day === 16)
                                        <div class="calendar-event event-orange" title="Pre-registered visit check-in log.">John Doe Checked In</div>
                                    @endif

                                    @if($day === 18)
                                        <div class="calendar-event event-blue" title="Kampala Plumbing Masters ceiling pipes repair order.">WO: Ceiling Pipes</div>
                                    @endif

                                    @if($day === 31)
                                        <div class="calendar-event event-red" title="Expiring contracts reminders.">Lease Agreement Renewal</div>
                                    @endif
                                </div>
                            @endfor

                            <!-- Blank trailing days to fill the grid -->
                            <div class="calendar-day empty"></div>
                        </div>
                    </div>
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
            $('#event-form').on('submit', function() {
                var $btn = $('#save-btn');
                
                if ($btn.data('submitting')) {
                    return false;
                }
                
                $btn.data('submitting', true);
                $btn.prop('disabled', true);
                
                var originalHtml = $btn.html();
                $btn.data('original-html', originalHtml);
                $btn.html('<i class="zmdi zmdi-hc-spin zmdi-spinner"></i> Saving reminder...');

                setTimeout(function() {
                    Swal.fire({
                        title: 'Reminder Created!',
                        text: 'Your calendar reminder was saved successfully.',
                        icon: 'success'
                    }).then(function() {
                        $btn.prop('disabled', false);
                        $btn.data('submitting', false);
                        $btn.html(originalHtml);
                        $('#event-title').val('');
                    });
                }, 1500);
            });
        });
    </script>
@endsection
