

<div class="blog-calendar" id="blog-calendar">
    <h3 class="widget-title">Post Calendar</h3>
    <div class="calendar-container">
        <div class="calendar-header">
            <button class="prev-month btn btn-sm">&lt;</button>
            <span class="current-month">{{ $currentMonth }}</span>
            <button class="next-month btn btn-sm">&gt;</button>
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
                                data-date="{{ $currentDate }}"
                                style="cursor: {{ $hasPost ? 'pointer' : 'default' }}">
                                {{ $date->format('d') }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendar = {
        currentDate: new Date(),
        postsData: @json($postDates),
        
        init() {
            this.bindEvents();
        },
        
        bindEvents() {
            // Navigation buttons
            const prevBtn = document.querySelector('.prev-month');
            const nextBtn = document.querySelector('.next-month');
            
            if (prevBtn) {
                prevBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.currentDate.setMonth(this.currentDate.getMonth() - 1);
                    this.fetchCalendarData();
                });
            }
            
            if (nextBtn) {
                nextBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.currentDate.setMonth(this.currentDate.getMonth() + 1);
                    this.fetchCalendarData();
                });
            }
            
            // Date clicks
            const calendarTable = document.querySelector('.calendar-table');
            if (calendarTable) {
                calendarTable.addEventListener('click', (e) => {
                    if (e.target.classList.contains('has-post')) {
                        const date = e.target.dataset.date;
                        if (date) {
                            window.location.href = `/blog?date=${date}`;
                        }
                    }
                });
            }
        },
        
        async fetchCalendarData() {
            try {
                const response = await fetch(`/blog/calendar/${this.currentDate.getFullYear()}/${this.currentDate.getMonth() + 1}`);
                const data = await response.json();
                this.updateCalendar(data);
            } catch (error) {
                console.error('Failed to fetch calendar data:', error);
            }
        },
        
        updateCalendar(data) {
            // Update month text
            const monthSpan = document.querySelector('.current-month');
            if (monthSpan) {
                monthSpan.textContent = data.currentMonth;
            }
            
            // Update calendar grid
            this.renderCalendar(data.calendar, data.postDates);
        },
        
        renderCalendar(calendarData, postDates) {
            const tbody = document.querySelector('.calendar-table tbody');
            if (!tbody) return;
            
            let html = '';
            const today = new Date();
            
            // Process calendar data in chunks of 7 (weeks)
            for (let i = 0; i < calendarData.length; i += 7) {
                html += '<tr>';
                for (let j = 0; j < 7 && (i + j) < calendarData.length; j++) {
                    const dateStr = calendarData[i + j];
                    const date = new Date(dateStr);
                    const currentDate = date.toISOString().split('T')[0];
                    const hasPost = postDates.includes(currentDate);
                    const isToday = date.toDateString() === today.toDateString();
                    
                    const classes = [];
                    if (hasPost) classes.push('has-post');
                    if (isToday) classes.push('today');
                    
                    html += `<td class="${classes.join(' ')}" 
                                data-date="${currentDate}"
                                style="cursor: ${hasPost ? 'pointer' : 'default'}">
                                ${date.getDate()}
                            </td>`;
                }
                html += '</tr>';
            }
            
            tbody.innerHTML = html;
        }
    };
    
    // Initialize the calendar
    calendar.init();
});
</script>

<style>
.blog-calendar {
    background: #fff;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.calendar-header button {
    background: #FF4C4C;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    min-width: 35px;
}

.calendar-header button:hover {
    background: #FF6B6B;
    transform: scale(1.05);
}

.calendar-table {
    width: 100%;
    border-collapse: collapse;
}

.calendar-table th {
    background: #f8f9fa;
    padding: 8px;
    text-align: center;
    font-weight: bold;
    border-bottom: 1px solid #dee2e6;
}

.calendar-table td {
    text-align: center;
    padding: 8px;
    border: 1px solid #dee2e6;
    width: 14.28%;
}

.calendar-table td.has-post {
    background: #FF4C4C;
    color: white;
    font-weight: bold;
    cursor: pointer;
}

.calendar-table td.has-post:hover {
    background: #FF6B6B;
}

.calendar-table td.today {
    border: 2px solid #007bff;
    font-weight: bold;
}

.calendar-table td.today.has-post {
    border: 2px solid #0056b3;
}
</style>
