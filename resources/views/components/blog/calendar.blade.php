@php
    \Log::info('Available post dates:', $postDates);
@endphp

<div class="blog-calendar">
    <h3 class="widget-title">Post Calendar</h3>
    <div class="calendar-container">
        <div class="calendar-header">
            <button class="prev-month btn btn-sm"><</button>
            <span class="current-month">{{ $currentMonth }}</span>
            <button class="next-month btn btn-sm">></button>
        </div>
        <table class="calendar-table">
            <thead>
                <tr>
                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                        <th>{{ $day }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($calendar->chunk(7) as $week)
                    <tr>
                        @foreach($week as $date)
                        @php
                        $currentDate = $date->format('Y-m-d');
                        $hasPost = in_array($currentDate, $postDates);
                       
                    @endphp
                            <td class="{{ $date->isToday() ? 'today' : '' }} {{ $hasPost ? 'has-post' : '' }}"
                                data-date="{{ $currentDate }}">
                                {{ $date->format('d') }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
