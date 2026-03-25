import "./bootstrap";
import * as BootstrapJS from "bootstrap";
window.bootstrap = BootstrapJS;

import Alpine from "alpinejs";
window.Alpine = Alpine;
Alpine.start();

/* ─── Admin sidebar nav group toggles ──────────────────────────────── */
document.addEventListener("DOMContentLoaded", () => {
    // Wire up every collapsible nav group in the sidebar
    document.querySelectorAll("[data-admin-nav-toggle]").forEach((btn) => {
        btn.addEventListener("click", () => {
            const targetId = btn.getAttribute("data-admin-nav-target");
            const section = targetId ? document.getElementById(targetId) : null;
            const group = btn.closest(".admin-nav-group");

            if (!group) return;

            const isOpen = group.classList.contains("is-open");

            group.classList.toggle("is-open", !isOpen);
            if (section) section.classList.toggle("is-open", !isOpen);
            btn.setAttribute("aria-expanded", String(!isOpen));
        });
    });

    /* ─── Dashboard: celebration type filter ───────────────────────── */
    document
        .querySelectorAll("[data-dashboard-celebration-filter]")
        .forEach((btn) => {
            btn.addEventListener("click", () => {
                const filter = btn.getAttribute(
                    "data-dashboard-celebration-filter",
                );

                // Update active state on filter buttons
                document
                    .querySelectorAll("[data-dashboard-celebration-filter]")
                    .forEach((b) => b.classList.remove("active"));
                btn.classList.add("active");

                // Show/hide table rows
                document
                    .querySelectorAll("[data-celebration-row]")
                    .forEach((row) => {
                        const type = row.getAttribute("data-celebration-row");
                        row.style.display =
                            filter === "all" || type === filter ? "" : "none";
                    });
            });
        });

    /* ─── Dashboard: reminder preview ──────────────────────────────── */
    const previewModal = document.getElementById("reminderPreviewModal");

    document.addEventListener("click", (e) => {
        // Preview reminder
        const previewBtn = e.target.closest(
            "[data-dashboard-preview-reminder]",
        );
        if (previewBtn && previewModal) {
            const url = previewBtn.getAttribute("data-preview-url");
            if (!url) return;
            fetch(url, {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            })
                .then((r) => r.json())
                .then((data) => {
                    const emailEl = previewModal.querySelector(
                        ".email-preview-content",
                    );
                    const notifEl = previewModal.querySelector(
                        ".notification-preview-content",
                    );
                    if (emailEl)
                        emailEl.innerHTML =
                            data.email ?? "<em>No preview.</em>";
                    if (notifEl)
                        notifEl.innerHTML =
                            data.notification ?? "<em>No preview.</em>";
                    window.bootstrap.Modal.getOrCreateInstance(
                        previewModal,
                    ).show();
                })
                .catch(() => {});
        }

        // Send reminder
        const sendBtn = e.target.closest("[data-dashboard-send-reminder]");
        if (sendBtn) {
            const url = sendBtn.getAttribute("data-send-url");
            if (!url || sendBtn.disabled) return;
            const token = document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content");
            sendBtn.disabled = true;
            const original = sendBtn.textContent;
            sendBtn.textContent = "Sending\u2026";
            fetch(url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": token,
                    "X-Requested-With": "XMLHttpRequest",
                },
            })
                .then(() => {
                    sendBtn.textContent = "Sent!";
                })
                .catch(() => {
                    sendBtn.disabled = false;
                    sendBtn.textContent = original;
                });
        }

        // Send birthday/anniversary wishes
        const wishesBtn = e.target.closest("[data-dashboard-send-wishes]");
        if (wishesBtn) {
            const url = wishesBtn.getAttribute("data-send-wishes-url");
            if (!url || wishesBtn.disabled) return;
            const token = document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content");
            wishesBtn.disabled = true;
            const original = wishesBtn.textContent;
            wishesBtn.textContent = "Sending\u2026";
            fetch(url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": token,
                    "X-Requested-With": "XMLHttpRequest",
                },
            })
                .then(() => {
                    wishesBtn.textContent = "Sent \u2713";
                })
                .catch(() => {
                    wishesBtn.disabled = false;
                    wishesBtn.textContent = original;
                });
        }
    });
});
