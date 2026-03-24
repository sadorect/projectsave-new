@extends('admin.layouts.app')

@section('title', 'Celebration Calendar')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Celebration Calendar</h5>
            <div class="btn-group">
                <button class="btn btn-outline-primary" data-calendar-view="dayGridMonth">Month</button>
                <button class="btn btn-outline-primary" data-calendar-view="listMonth">List</button>
            </div>
        </div>
        <div class="card-body">
            <div
                id="celebration-calendar"
                data-celebration-calendar
                data-send-wishes-url-template="{{ route('admin.dashboard.send-wishes', ['userId' => '__USER_ID__']) }}"
            ></div>
            <script type="application/json" id="celebrationCalendarPayload">@json($celebrations)</script>
        </div>

        <div class="modal fade" id="celebrationModal" tabindex="-1" aria-labelledby="celebrationModalTitle" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="celebrationModalTitle">Celebration Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="celebration-info"></div>
                        <div class="mt-3">
                            <button class="btn btn-primary send-wishes" type="button">Send Wishes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
