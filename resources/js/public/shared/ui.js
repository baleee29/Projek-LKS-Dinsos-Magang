/** Interaksi bersama halaman publik: animasi masuk dan dialog informasi. */
(() => {
    "use strict";

    /* ==========================================================================
       1. ANIMASI SCROLL REVEAL
       ========================================================================== */
    const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");

    if (!reducedMotion.matches && "IntersectionObserver" in window) {
        const revealSelector =
            ".section-heading, .step, .pillar-card, .services-image, .service-item, .gallery-grid > img, .gallery-grid > figure";
        const revealObserver = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;
                    entry.target.classList.add("is-visible");
                    revealObserver.unobserve(entry.target);
                });
            },
            {
                threshold: 0.1,
                rootMargin: "0px 0px -24px 0px",
            },
        );

        document.querySelectorAll(revealSelector).forEach((element) => {
            if (element.getBoundingClientRect().top < window.innerHeight)
                return;

            const siblings = [...element.parentElement.children].filter(
                (sibling) => sibling.matches(revealSelector),
            );
            element.style.setProperty(
                "--reveal-delay",
                `${(siblings.indexOf(element) % 4) * 60}ms`,
            );
            element.classList.add("scroll-reveal");
            revealObserver.observe(element);
        });
    }

    /* ==========================================================================
       2. DIALOG DEMO / MODAL HANDLER
       ========================================================================== */
    const dialog = document.getElementById("demo-dialog");
    const dialogTitle = document.getElementById("dialog-title");
    const dialogDescription = document.getElementById("dialog-description");
    let dialogTrigger = null;

    function showDemo(title, description, trigger) {
        if (!dialog) return;
        dialogTrigger = trigger;
        if (dialogTitle) dialogTitle.textContent = title;
        if (dialogDescription) dialogDescription.textContent = description;
        dialog.showModal();
        document.body.classList.add("dialog-open");
    }

    // Ekspos secara global jika dibutuhkan modul lain
    window.showDemo = showDemo;

    document.querySelectorAll("[data-content-message]").forEach((button) => {
        if (button.closest(".site-footer")) return;
        button.addEventListener("click", () => {
            showDemo(
                button.dataset.contentTitle,
                button.dataset.contentMessage,
                button,
            );
        });
    });

    if (dialog) {
        dialog.addEventListener("close", () => {
            document.body.classList.remove("dialog-open");
            if (dialogTrigger && dialogTrigger.getClientRects().length) {
                dialogTrigger.focus();
            } else {
                const menuToggle = document.querySelector(".menu-toggle");
                if (menuToggle) menuToggle.focus();
            }
        });

        dialog.addEventListener("click", (event) => {
            if (event.target !== dialog) return;
            const bounds = dialog.getBoundingClientRect();
            if (
                event.clientX < bounds.left ||
                event.clientX > bounds.right ||
                event.clientY < bounds.top ||
                event.clientY > bounds.bottom
            ) {
                dialog.close();
            }
        });
    }
})();
