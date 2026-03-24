@props([
    'calendar',
    'currentMonth',
    'calendarMonth',
    'calendarYear',
    'postCalendarDays' => [],
])

<div
    class="public-sidebar-card devotional-calendar"
    data-blog-calendar
    data-calendar-month="{{ $calendarMonth }}"
    data-calendar-year="{{ $calendarYear }}"
    data-calendar-endpoint-template="{{ url('/blog/calendar/__YEAR__/__MONTH__') }}"
>
    <div class="d-flex align-items-start justify-content-between gap-3">
        <div>
            <div class="public-kicker mb-2">Archive calendar</div>
            <h2 class="h5 mb-0">Post Calendar</h2>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button" class="calendar-nav-button" data-calendar-previous aria-label="View previous month">
                <i class="bi bi-chevron-left"></i>
            </button>
            <button type="button" class="calendar-nav-button" data-calendar-next aria-label="View next month">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </div>

    <div class="devotional-calendar-month mt-4" data-calendar-month-label>{{ $currentMonth }}</div>

    <table class="devotional-calendar-table mt-3">
        <thead>
            <tr>
                @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                    <th>{{ $day }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody data-calendar-body>
            @foreach($calendar->chunk(7) as $week)
                <tr>
                    @foreach($week as $date)
                        @php
                            $dateKey = $date->format('Y-m-d');
                            $dayData = $postCalendarDays[$dateKey] ?? null;
                            $isOutsideMonth = $date->month !== (int) $calendarMonth || $date->year !== (int) $calendarYear;
                            $cellClass = collect([
                                'devotional-calendar-cell',
                                $date->isToday() ? 'is-today' : null,
                                $isOutsideMonth ? 'is-outside' : null,
                                $dayData ? 'has-post' : null,
                            ])->filter()->implode(' ');
                            $tooltip = $dayData
                                ? implode(' | ', $dayData['titles'])
                                : null;
                        @endphp

                        <td class="{{ $cellClass }}">
                            @if($dayData)
                                <a
                                    href="{{ $dayData['url'] }}"
                                    class="devotional-calendar-link"
                                    title="{{ $tooltip }}"
                                >
                                    <span class="devotional-calendar-date">{{ $date->day }}</span>
                                    <span class="devotional-calendar-count">{{ $dayData['count'] }}</span>
                                </a>
                            @else
                                <span class="devotional-calendar-link">
                                    <span class="devotional-calendar-date">{{ $date->day }}</span>
                                </span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="devotional-calendar-note mb-0 mt-3">
        Highlighted days show how many devotionals were published. Tap a day to open the post or that day's archive.
    </p>
</div>
