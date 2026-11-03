/** Navigasi bagian halaman pilar: menempel di bawah header, menandai bagian aktif, dan menunjukkan kemajuan membaca. */
(() => {
    "use strict";

    const nav = document.querySelector("[data-section-nav]");
    if (!nav) return;

    const root = document.documentElement;
    const header = document.querySelector(".site-header");
    const strip = nav.querySelector("[data-section-links]");
    const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");

    function targetOf(hash) {
        if (!hash || !hash.startsWith("#") || hash === "#") return null;
        try {
            return document.getElementById(decodeURIComponent(hash.slice(1)));
        } catch {
            return null;
        }
    }

    const links = [...nav.querySelectorAll('a[href^="#"]')]
        .map((link) => ({ link, section: targetOf(link.getAttribute("href")) }))
        .filter((item) => item.section);
    if (!links.length) return;
    const sections = [...new Set(links.map((item) => item.section))];

    root.classList.add("has-section-nav");
    const progress = document.createElement("span");
    progress.className = "section-nav-progress";
    progress.setAttribute("aria-hidden", "true");
    nav.append(progress);

    let headerHeight = 0;
    // Tinggi header (berubah saat menu ponsel dibuka) dan navigasi menjadi offset sticky dan anchor.
    function measure() {
        headerHeight = header ? header.getBoundingClientRect().height : 0;
        root.style.setProperty("--site-header-height", `${headerHeight}px`);
        root.style.setProperty("--section-nav-height", `${nav.offsetHeight}px`);
        updateOverflow();
        schedule();
    }

    function updateOverflow() {
        if (!strip) return;
        const max = strip.scrollWidth - strip.clientWidth;
        nav.classList.toggle("can-scroll-start", strip.scrollLeft > 2);
        nav.classList.toggle("can-scroll-end", strip.scrollLeft < max - 2);
    }

    let current;
    function markActive(section) {
        if (section === current) return;
        current = section;
        let activeLink = null;
        links.forEach(({ link, section: target }) => {
            if (target === section) {
                link.setAttribute("aria-current", "location");
                activeLink = activeLink || link;
            } else link.removeAttribute("aria-current");
        });
        // Geser deretan tautan saja, bukan halaman, agar tautan aktif tetap terlihat di layar sempit.
        if (activeLink && strip && strip.scrollWidth > strip.clientWidth) {
            const left =
                activeLink.offsetLeft -
                (strip.clientWidth - activeLink.offsetWidth) / 2;
            strip.scrollTo({
                left: Math.max(0, left),
                behavior: reducedMotion.matches ? "auto" : "smooth",
            });
        }
    }

    function update() {
        const line = headerHeight + nav.offsetHeight + window.innerHeight * 0.2;
        let active = null;
        sections.forEach((section) => {
            if (section.getBoundingClientRect().top <= line) active = section;
        });
        markActive(active);

        const start =
            sections[0].getBoundingClientRect().top + window.scrollY - line;
        const end =
            sections[sections.length - 1].getBoundingClientRect().bottom +
            window.scrollY -
            window.innerHeight;
        const ratio =
            end > start ? (window.scrollY - start) / (end - start) : 0;
        progress.style.transform = `scaleX(${Math.min(1, Math.max(0, ratio))})`;
        nav.classList.toggle(
            "is-stuck",
            window.scrollY > 0 &&
                nav.getBoundingClientRect().top <= headerHeight + 0.5,
        );
    }

    let scheduled = false;
    function schedule() {
        if (scheduled) return;
        scheduled = true;
        window.requestAnimationFrame(() => {
            scheduled = false;
            update();
        });
    }

    // Tautan ke bagian (navigasi maupun tombol di dalam halaman) memindahkan fokus ke judul bagian.
    document.addEventListener("click", (event) => {
        const link = event.target.closest('a[href^="#"]');
        if (
            !link ||
            event.defaultPrevented ||
            event.button !== 0 ||
            event.ctrlKey ||
            event.metaKey ||
            event.shiftKey ||
            event.altKey
        )
            return;
        const section = targetOf(link.getAttribute("href"));
        if (!section || !sections.includes(section)) return;
        window.setTimeout(() => {
            const heading = section.querySelector("h2, h3") || section;
            if (!heading.hasAttribute("tabindex")) heading.tabIndex = -1;
            heading.focus({ preventScroll: true });
            markActive(section);
        }, 0);
    });

    if ("ResizeObserver" in window) {
        const observer = new ResizeObserver(measure);
        if (header) observer.observe(header);
        observer.observe(nav);
    }
    window.addEventListener("resize", measure, { passive: true });
    window.addEventListener("scroll", schedule, { passive: true });
    window.addEventListener("load", measure, { once: true });
    strip?.addEventListener("scroll", updateOverflow, { passive: true });
    measure();
    update();
})();
