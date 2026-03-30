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

function compareMonthYear(leftMonth, leftYear, rightMonth, rightYear) {
    if (Number(leftYear) !== Number(rightYear)) {
        return Number(leftYear) - Number(rightYear);
    }

    return Number(leftMonth) - Number(rightMonth);
}

function clampTargetMonth(month, year, range) {
    if (
        compareMonthYear(month, year, range.startMonth, range.startYear) < 0
    ) {
        return {
            month: range.startMonth,
            year: range.startYear,
        };
    }

    if (compareMonthYear(month, year, range.endMonth, range.endYear) > 0) {
        return {
            month: range.endMonth,
            year: range.endYear,
        };
    }

    return {
        month: Number(month),
        year: Number(year),
    };
}

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("[data-blog-calendar]").forEach((calendar) => {
        const prevButton = calendar.querySelector("[data-calendar-previous]");
        const nextButton = calendar.querySelector("[data-calendar-next]");
        const monthLabel = calendar.querySelector("[data-calendar-month-label]");
        const body = calendar.querySelector("[data-calendar-body]");
        const monthSelect = calendar.querySelector("[data-calendar-month-select]");
        const yearSelect = calendar.querySelector("[data-calendar-year-select]");
        const jumpButton = calendar.querySelector("[data-calendar-jump]");
        const endpointTemplate = calendar.dataset.calendarEndpointTemplate;
        const range = {
            startMonth: Number(calendar.dataset.calendarStartMonth || 1),
            startYear: Number(calendar.dataset.calendarStartYear || new Date().getFullYear()),
            endMonth: Number(calendar.dataset.calendarEndMonth || 12),
            endYear: Number(calendar.dataset.calendarEndYear || new Date().getFullYear()),
        };

        if (!prevButton || !nextButton || !monthLabel || !body || !endpointTemplate) {
            return;
        }

        const isLoading = () => calendar.classList.contains("is-loading");

        const updateMonthOptionAvailability = () => {
            if (!monthSelect || !yearSelect) {
                return;
            }

            const selectedYear = Number(yearSelect.value);

            Array.from(monthSelect.options).forEach((option) => {
                const optionMonth = Number(option.value);
                const beforeArchiveStart =
                    selectedYear === range.startYear &&
                    optionMonth < range.startMonth;
                const afterArchiveEnd =
                    selectedYear === range.endYear &&
                    optionMonth > range.endMonth;

                option.disabled = beforeArchiveStart || afterArchiveEnd;
            });

            const selectedOption = monthSelect.selectedOptions[0];

            if (selectedOption?.disabled) {
                const firstEnabledOption = Array.from(monthSelect.options).find(
                    (option) => !option.disabled,
                );

                if (firstEnabledOption) {
                    monthSelect.value = firstEnabledOption.value;
                }
            }
        };

        const updateJumpAvailability = () => {
            if (!jumpButton || !monthSelect || !yearSelect) {
                return;
            }

            jumpButton.disabled =
                isLoading() ||
                (Number(monthSelect.value) ===
                    Number(calendar.dataset.calendarMonth) &&
                    Number(yearSelect.value) ===
                        Number(calendar.dataset.calendarYear));
        };

        const updateNavigationAvailability = () => {
            const currentMonth = Number(calendar.dataset.calendarMonth);
            const currentYear = Number(calendar.dataset.calendarYear);
            const atStart =
                compareMonthYear(
                    currentMonth,
                    currentYear,
                    range.startMonth,
                    range.startYear,
                ) <= 0;
            const atEnd =
                compareMonthYear(
                    currentMonth,
                    currentYear,
                    range.endMonth,
                    range.endYear,
                ) >= 0;

            prevButton.disabled = isLoading() || atStart;
            nextButton.disabled = isLoading() || atEnd;
        };

        const syncPickerState = () => {
            if (monthSelect) {
                monthSelect.value = String(calendar.dataset.calendarMonth);
            }

            if (yearSelect) {
                yearSelect.value = String(calendar.dataset.calendarYear);
            }

            updateMonthOptionAvailability();
            updateJumpAvailability();
        };

        const toggleLoadingState = (isLoading) => {
            calendar.classList.toggle("is-loading", isLoading);

            if (monthSelect) {
                monthSelect.disabled = isLoading;
            }

            if (yearSelect) {
                yearSelect.disabled = isLoading;
            }

            if (jumpButton) {
                jumpButton.disabled = isLoading;
            }

            updateNavigationAvailability();
            updateJumpAvailability();
        };

        const loadCalendar = async (requestedMonth, requestedYear) => {
            const { month, year } = clampTargetMonth(
                requestedMonth,
                requestedYear,
                range,
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
                calendar.dataset.calendarStartMonth = String(payload.startMonth ?? range.startMonth);
                calendar.dataset.calendarStartYear = String(payload.startYear ?? range.startYear);
                calendar.dataset.calendarEndMonth = String(payload.endMonth ?? range.endMonth);
                calendar.dataset.calendarEndYear = String(payload.endYear ?? range.endYear);
                monthLabel.textContent = payload.currentMonth;
                body.innerHTML = renderCalendarBody(payload);
                range.startMonth = Number(payload.startMonth ?? range.startMonth);
                range.startYear = Number(payload.startYear ?? range.startYear);
                range.endMonth = Number(payload.endMonth ?? range.endMonth);
                range.endYear = Number(payload.endYear ?? range.endYear);
                syncPickerState();
            } catch (error) {
                console.error("Unable to load blog calendar month.", error);
            } finally {
                toggleLoadingState(false);
            }
        };

        prevButton.addEventListener("click", () => {
            const { month, year } = getTargetMonth(
                calendar.dataset.calendarMonth,
                calendar.dataset.calendarYear,
                -1,
            );

            loadCalendar(month, year);
        });

        nextButton.addEventListener("click", () => {
            const { month, year } = getTargetMonth(
                calendar.dataset.calendarMonth,
                calendar.dataset.calendarYear,
                1,
            );

            loadCalendar(month, year);
        });

        if (yearSelect) {
            yearSelect.addEventListener("change", () => {
                updateMonthOptionAvailability();
                updateJumpAvailability();
            });
        }

        if (monthSelect) {
            monthSelect.addEventListener("change", updateJumpAvailability);
        }

        if (jumpButton && monthSelect && yearSelect) {
            jumpButton.addEventListener("click", () => {
                loadCalendar(monthSelect.value, yearSelect.value);
            });
        }

        const headerRow = calendar.querySelector("thead tr");
        if (headerRow && !headerRow.children.length) {
            headerRow.innerHTML = weekdayLabels.map((day) => `<th>${day}</th>`).join("");
        }

        syncPickerState();
        updateNavigationAvailability();
    });
});
