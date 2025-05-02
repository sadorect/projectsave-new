document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    const calendar = document.querySelector('.blog-calendar');
    const prevButton = calendar.querySelector('.prev-month');
    const nextButton = calendar.querySelector('.next-month');
    const currentMonthDisplay = calendar.querySelector('.current-month');

    function updateCalendar(month, year) {
        fetch(`/post-dates/${month}/${year}`)
            .then(response => response.json())
            .then(dates => {
                const cells = document.querySelectorAll('.calendar-table td');
                // Clear existing indicators
                cells.forEach(cell => cell.classList.remove('has-post'));
                
                // Add indicators for dates with posts
                cells.forEach(cell => {
                    const date = cell.dataset.date;
                    if (dates.includes(date)) {
                        cell.classList.add('has-post');
                    }
                });
            });
    }

    // Handle month navigation
    prevButton.addEventListener('click', () => {
        console.log('Previous month button clicked');
        const [month, year] = currentMonthDisplay.textContent.split(' ');
        console.log(`Current display: ${month} ${year}`);
            const date = new Date(`${month} 1, ${year}`);
        date.setMonth(date.getMonth() - 1);
        console.log(`Navigating to: ${date.getMonth() + 1}/${date.getFullYear()}`);
        updateCalendar(date.getMonth() + 1, date.getFullYear());
    });

    nextButton.addEventListener('click', () => {
        console.log('Next month button clicked');
        const [month, year] = currentMonthDisplay.textContent.split(' ');
        console.log(`Current display: ${month} ${year}`);
        const date = new Date(`${month} 1, ${year}`);
        date.setMonth(date.getMonth() + 1);
        console.log(`Navigating to: ${date.getMonth() + 1}/${date.getFullYear()}`);
        updateCalendar(date.getMonth() + 1, date.getFullYear());
    });

    // Initial load
    const now = new Date();
    console.log('Initial calendar load');
    updateCalendar(now.getMonth() + 1, now.getFullYear());
});