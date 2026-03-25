import "./bootstrap";
import * as BootstrapJS from "bootstrap";
window.bootstrap = BootstrapJS;

import Alpine from "alpinejs";
window.Alpine = Alpine;
Alpine.start();

/* ─── Mobile navigation burger toggle ───────────────────────────── */
document.addEventListener("DOMContentLoaded", () => {
    const navToggle = document.querySelector("[data-site-nav-toggle]");
    const navPanel = document.getElementById("publicNavigation");

    if (navToggle && navPanel) {
        navToggle.addEventListener("click", () => {
            const isOpen = navPanel.classList.toggle("is-open");
            navToggle.setAttribute("aria-expanded", String(isOpen));
        });

        // Close when a nav link is tapped (single-page-style navigation)
        navPanel.querySelectorAll("a").forEach((link) => {
            link.addEventListener("click", () => {
                navPanel.classList.remove("is-open");
                navToggle.setAttribute("aria-expanded", "false");
            });
        });

        // Close when clicking outside the nav panel or toggle button
        document.addEventListener("click", (e) => {
            if (!navPanel.contains(e.target) && !navToggle.contains(e.target)) {
                navPanel.classList.remove("is-open");
                navToggle.setAttribute("aria-expanded", "false");
            }
        });
    }
});

/* ─── Partnership form conditionals ─────────────────────────────── */
document.addEventListener("DOMContentLoaded", () => {
    // ── Leadership details toggle ────────────────────────────────
    const leadershipSelect = document.querySelector(
        '[name="leadership_experience"]',
    );
    const leadershipDetails = document.getElementById("leadershipDetails");

    if (leadershipSelect && leadershipDetails) {
        const toggleLeadership = () => {
            const show = leadershipSelect.value === "yes";
            leadershipDetails.classList.toggle("d-none", !show);
            leadershipDetails.setAttribute("aria-hidden", String(!show));
        };
        leadershipSelect.addEventListener("change", toggleLeadership);
        toggleLeadership(); // honour old() values on validation fail
    }

    // ── "Add More Experience" clones the first leadership entry ──
    const addMoreBtn = document.getElementById("addMoreLeadership");

    function updateRemoveButtons() {
        if (!leadershipDetails) return;
        const entries = leadershipDetails.querySelectorAll(".leadership-entry");
        entries.forEach((entry, index) => {
            const btn = entry.querySelector(".remove-leadership-entry");
            if (btn) {
                // Hide the remove button on the first (only) entry; show it when there are multiple
                btn.classList.toggle("d-none", entries.length <= 1);
            }
        });
    }

    if (addMoreBtn && leadershipDetails) {
        addMoreBtn.addEventListener("click", () => {
            const firstEntry =
                leadershipDetails.querySelector(".leadership-entry");
            if (!firstEntry) return;
            const clone = firstEntry.cloneNode(true);
            // Clear cloned input values
            clone.querySelectorAll("input").forEach((input) => {
                input.value = "";
            });
            firstEntry.parentNode.insertBefore(clone, addMoreBtn);
            updateRemoveButtons();
        });

        // Event delegation for remove buttons (handles both original and clones)
        leadershipDetails.addEventListener("click", (e) => {
            const removeBtn = e.target.closest(".remove-leadership-entry");
            if (!removeBtn) return;
            const entry = removeBtn.closest(".leadership-entry");
            if (!entry) return;
            const entries =
                leadershipDetails.querySelectorAll(".leadership-entry");
            if (entries.length > 1) {
                entry.remove();
                updateRemoveButtons();
            }
        });
    }

    // ── Born-again details ───────────────────────────────────────
    const bornAgainSelect = document.querySelector('[name="born_again"]');
    const bornAgainDetails = document.querySelector(".born-again-details");

    if (bornAgainSelect && bornAgainDetails) {
        const toggleBornAgain = () => {
            const show = bornAgainSelect.value === "yes";
            bornAgainDetails.classList.toggle("d-none", !show);
            bornAgainDetails.setAttribute("aria-hidden", String(!show));
        };
        bornAgainSelect.addEventListener("change", toggleBornAgain);
        toggleBornAgain();
    }

    // ── Water baptism type ───────────────────────────────────────
    const waterBaptizedSelect = document.querySelector(
        '[name="water_baptized"]',
    );
    const baptismTypeGroup = document.querySelector(".baptism-type-group");

    if (waterBaptizedSelect && baptismTypeGroup) {
        const toggleBaptismType = () => {
            const show = waterBaptizedSelect.value === "yes";
            baptismTypeGroup.classList.toggle("d-none", !show);
            baptismTypeGroup.setAttribute("aria-hidden", String(!show));
        };
        waterBaptizedSelect.addEventListener("change", toggleBaptismType);
        toggleBaptismType();
    }

    // ── Holy Ghost reason (show when answer is 'no') ─────────────
    const holyGhostSelect = document.querySelector(
        '[name="holy_ghost_baptism"]',
    );
    const holyGhostReason = document.querySelector(".holy-ghost-reason");

    if (holyGhostSelect && holyGhostReason) {
        const toggleHolyGhost = () => {
            const show = holyGhostSelect.value === "no";
            holyGhostReason.classList.toggle("d-none", !show);
            holyGhostReason.setAttribute("aria-hidden", String(!show));
        };
        holyGhostSelect.addEventListener("change", toggleHolyGhost);
        toggleHolyGhost();
    }
});
