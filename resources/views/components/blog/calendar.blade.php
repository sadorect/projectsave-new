@props([
    'calendar',
    'currentMonth',
    'calendarMonth',
    'calendarYear',
    'calendarStartMonth',
    'calendarStartYear',
    'calendarEndMonth',
    'calendarEndYear',
    'postCalendarDays' => [],
])

@php
    $archiveStartLabel = \Carbon\Carbon::createFromDate($calendarStartYear, $calendarStartMonth, 1)->format('M Y');
    $archiveEndLabel = \Carbon\Carbon::createFromDate($calendarEndYear, $calendarEndMonth, 1)->format('M Y');
@endphp

<div
    class="public-sidebar-card devotional-calendar"
    data-blog-calendar
    data-calendar-month="{{ $calendarMonth }}"
    data-calendar-year="{{ $calendarYear }}"
    data-calendar-start-month="{{ $calendarStartMonth }}"
    data-calendar-start-year="{{ $calendarStartYear }}"
    data-calendar-end-month="{{ $calendarEndMonth }}"
    data-calendar-end-year="{{ $calendarEndYear }}"
    data-calendar-endpoint-template="{{ url('/blog/calendar/__YEAR__/__MONTH__') }}"
>
    <div class="devotional-calendar-toolbar d-flex align-items-start justify-content-between gap-3">
        <div>
            <div class="public-kicker mb-2">Archive calendar</div>
            <h2 class="h5 mb-0">Post Calendar</h2>
        </div>

        <div class="devotional-calendar-nav d-flex align-items-center gap-2">
            <button type="button" class="calendar-nav-button" data-calendar-previous aria-label="View previous month">
                <i class="bi bi-chevron-left"></i>
            </button>
            <button type="button" class="calendar-nav-button" data-calendar-next aria-label="View next month">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </div>

    <div class="devotional-calendar-jump mt-4">
        <div class="d-flex flex-column gap-1">
            <div class="devotional-calendar-month" data-calendar-month-label>{{ $currentMonth }}</div>
            <p class="devotional-calendar-range mb-0">Browse {{ $archiveStartLabel }} to {{ $archiveEndLabel }}</p>
        </div>

        <div class="devotional-calendar-jump-grid mt-3">
            <label class="devotional-calendar-select-shell" for="calendar-month-select-{{ $attributes->get('id', 'archive') }}">
                <!--span class="devotional-calendar-select-icon"><i class="bi bi-calendar3"></i></span-->
                <div class="devotional-calendar-select-inner">
                    <span class="devotional-calendar-select-label">Month</span>
                    <select
                        id="calendar-month-select-{{ $attributes->get('id', 'archive') }}"
                        class="devotional-calendar-select"
                        data-calendar-month-select
                        aria-label="Select archive month"
                    >
                    @foreach(range(1, 12) as $monthNumber)
                            <option value="{{ $monthNumber }}" @selected((int) $calendarMonth === $monthNumber)>
                                {{ \Carbon\Carbon::createFromDate(2000, $monthNumber, 1)->format('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </label>

            <label class="devotional-calendar-select-shell" for="calendar-year-select-{{ $attributes->get('id', 'archive') }}">
                <!--span class="devotional-calendar-select-icon"><i class="bi bi-clock-history"></i></span-->
                <div class="devotional-calendar-select-inner">
                    <span class="devotional-calendar-select-label">Year</span>
                    <select
                        id="calendar-year-select-{{ $attributes->get('id', 'archive') }}"
                        class="devotional-calendar-select"
                        data-calendar-year-select
                        aria-label="Select archive year"
                    >
                    @foreach(range((int) $calendarEndYear, (int) $calendarStartYear) as $yearNumber)
                            <option value="{{ $yearNumber }}" @selected((int) $calendarYear === $yearNumber)>
                                {{ $yearNumber }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </label>

            <button type="button" class="devotional-calendar-jump-button" data-calendar-jump>
                <i class="bi bi-arrow-up-right-circle"></i>
                <span>Go</span>
            </button>
        </div>
    </div>

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
        Highlighted days show how many devotionals were published. Tap a day to open the post or that day’s archive.
    </p>
</div>
