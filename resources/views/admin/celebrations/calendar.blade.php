@extends('admin.layouts.app')

@section('title', 'Celebration Calendar')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/@fullcalendar/core@4.4.0/main.min.css' rel='stylesheet' />
<link href='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@4.4.0/main.min.css' rel='stylesheet' />

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Celebration Calendar</h5>
            <div class="btn-group">
                <button class="btn btn-outline-primary" data-calendar-view="month">Month</button>
                <button class="btn btn-outline-primary" data-calendar-view="listMonth">List</button>
            </div>
        </div>
        <div class="card-body">
            <div id="celebration-calendar"></div>
        </div>

        <!-- Add this after the calendar div -->
<div class="modal fade" id="celebrationModal" tabindex="-1">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title">Celebration Details</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
              <div class="celebration-info"></div>
              <div class="mt-3">
                  <button class="btn btn-primary send-wishes">Send Wishes</button>
              </div>
          </div>
      </div>
  </div>
</div>

    </div>
</div>

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@4.4.0/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@4.4.0/main.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('celebration-calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: ['dayGrid'],
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,listMonth'
        },
        events: @json($celebrations),
        eventClick: function(info) {
            showCelebrationDetails(info.event);
        }
    });
    calendar.render();
});
</script>
@endpush
@endsection


<script>
function showCelebrationDetails(event) {
    const modal = new bootstrap.Modal(document.getElementById('celebrationModal'));
    const userId = event.id.split('_')[1];
    const type = event.extendedProps.type;
    
    document.querySelector('.celebration-info').innerHTML = `
        <h6>${event.title}</h6>
        <p>Date: ${event.start.toLocaleDateString()}</p>
    `;
    
    document.querySelector('.send-wishes').onclick = () => sendWishes(userId, type);
    
    modal.show();
}
</script>
