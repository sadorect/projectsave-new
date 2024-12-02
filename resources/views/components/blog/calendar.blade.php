<div class="blog-calendar">
    <h3>Post Calendar</h3>
    <div class="calendar-header">
        <button class="prev-month"><</button>
        <span class="current-month">{{ $currentMonth }}</span>
        <button class="next-month">></button>
    </div>
    <div class="calendar-grid">
        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
            <div class="day-header">{{ $day }}</div>
        @endforeach
        
        @foreach($calendar as $date)
            <div class="day {{ $date->isToday() ? 'today' : '' }} {{ in_array($date->format('Y-m-d'), $postDates) ? 'has-posts' : '' }}">
                {{ $date->format('d') }}
            </div>
        @endforeach
    </div>
</div>
