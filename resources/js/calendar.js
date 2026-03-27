const weekdayLabels = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, (character) => {
        const map = {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#039;",
        };

        return map[character] ?? character;
    });
}

function buildCalendarCell(dateString, month, year, postCalendarDays) {
    const date = new Date(`${dateString}T00:00:00`);
    const today = new Date();
    const isToday =
        date.getFullYear() === today.getFullYear() &&
        date.getMonth() === today.getMonth() &&
        date.getDate() === today.getDate();
    const isOutsideMonth =
        date.getMonth() + 1 !== Number(month) ||
        date.getFullYear() !== Number(year);
    const dayData = postCalendarDays[dateString] ?? null;
    const classes = ["devotional-calendar-cell"];

    if (isToday) {
        classes.push("is-today");
    }

    if (isOutsideMonth) {
        classes.push("is-outside");
    }

    if (dayData) {
        classes.push("has-post");
    }

    const day = date.getDate();
    const tooltip = dayData?.titles?.length
        ? ` title="${escapeHtml(dayData.titles.join(" | "))}"`
        : "";

    if (dayData) {
        return `
            <td class="${classes.join(" ")}">
                <a href="${escapeHtml(dayData.url)}" class="devotional-calendar-link"${tooltip}>
                    <span class="devotional-calendar-date">${day}</span>
                    <span class="devotional-calendar-count">${dayData.count}</span>
                </a>
            </td>
        `;
    }

    return `
        <td class="${classes.join(" ")}">
            <span class="devotional-calendar-link">
                <span class="devotional-calendar-date">${day}</span>
            </span>
        </td>
    `;
}

function renderCalendarBody(payload) {
    const rows = [];

    for (let index = 0; index < payload.calendar.length; index += 7) {
        const week = payload.calendar.slice(index, index + 7);
        const cells = week
            .map((dateString) =>
                buildCalendarCell(
                    dateString,
                    payload.month,
                    payload.year,
                    payload.postCalendarDays ?? {},
                ),
            )
            .join("");

        rows.push(`<tr>${cells}</tr>`);
    }

    return rows.join("");
}

function getTargetMonth(month, year, direction) {
    const current = new Date(Number(year), Number(month) - 1, 1);
    current.setMonth(current.getMonth() + direction);

    return {
        month: current.getMonth() + 1,
        year: current.getFullYear(),
    };
}

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("[data-blog-calendar]").forEach((calendar) => {
        const prevButton = calendar.querySelector("[data-calendar-previous]");
        const nextButton = calendar.querySelector("[data-calendar-next]");
        const monthLabel = calendar.querySelector("[data-calendar-month-label]");
        const body = calendar.querySelector("[data-calendar-body]");
        const endpointTemplate = calendar.dataset.calendarEndpointTemplate;

        if (!prevButton || !nextButton || !monthLabel || !body || !endpointTemplate) {
            return;
        }

        const toggleLoadingState = (isLoading) => {
            prevButton.disabled = isLoading;
            nextButton.disabled = isLoading;
            calendar.classList.toggle("is-loading", isLoading);
        };

        const loadCalendar = async (direction) => {
            const { month, year } = getTargetMonth(
                calendar.dataset.calendarMonth,
                calendar.dataset.calendarYear,
                direction,
            );

            const endpoint = endpointTemplate
                .replace("__YEAR__", String(year))
                .replace("__MONTH__", String(month));

            toggleLoadingState(true);

            try {
                const response = await fetch(endpoint, {
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                });

                if (!response.ok) {
                    throw new Error(`Calendar request failed with ${response.status}`);
                }

                const payload = await response.json();

                calendar.dataset.calendarMonth = String(payload.month);
                calendar.dataset.calendarYear = String(payload.year);
                monthLabel.textContent = payload.currentMonth;
                body.innerHTML = renderCalendarBody(payload);
            } catch (error) {
                console.error("Unable to load blog calendar month.", error);
            } finally {
                toggleLoadingState(false);
            }
        };

        prevButton.addEventListener("click", () => loadCalendar(-1));
        nextButton.addEventListener("click", () => loadCalendar(1));

        const headerRow = calendar.querySelector("thead tr");
        if (headerRow && !headerRow.children.length) {
            headerRow.innerHTML = weekdayLabels.map((day) => `<th>${day}</th>`).join("");
        }
    });
});
