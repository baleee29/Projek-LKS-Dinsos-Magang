/* Kelompok tab independen. Tanpa JavaScript seluruh panel tetap terbaca. */
(() => {
    "use strict";

    const mobileLayout = window.matchMedia("(max-width: 600px)");
    document.querySelectorAll("[data-profile-tabs]").forEach((section) => {
        const tabList = section.querySelector(".profile-tabs");
        const buttons = [...section.querySelectorAll("[data-profile-tab]")];
        const panels = buttons.map((button) =>
            document.getElementById(button.dataset.profileTab),
        );
        if (
            !tabList ||
            !buttons.length ||
            panels.some((panel) => !panel || !section.contains(panel))
        )
            return;

        function activateTab(index, moveFocus = false) {
            buttons.forEach((button, currentIndex) => {
                const selected = currentIndex === index;
                button.setAttribute("aria-selected", String(selected));
                button.tabIndex = selected ? 0 : -1;
                panels[currentIndex].hidden = !selected;
            });
            /* Garis alur dan garis bawah tab mengikuti pilihan. */
            tabList.style.setProperty("--profile-step", String(index));
            tabList.style.setProperty("--profile-steps", String(buttons.length - 1));
            moveIndicator();
            if (moveFocus) buttons[index].focus();
        }

        function moveIndicator() {
            const active = buttons.find((button) => button.getAttribute("aria-selected") === "true");
            if (!active) return;
            tabList.style.setProperty("--profile-ind-x", `${active.offsetLeft}px`);
            tabList.style.setProperty("--profile-ind-y", `${active.offsetTop + active.offsetHeight}px`);
            tabList.style.setProperty("--profile-ind-w", `${active.offsetWidth}px`);
        }

        function isVertical() {
            return (
                mobileLayout.matches &&
                !tabList.classList.contains("profile-support-tabs")
            );
        }

        function setOrientation() {
            tabList.setAttribute(
                "aria-orientation",
                isVertical() ? "vertical" : "horizontal",
            );
        }

        tabList.setAttribute("role", "tablist");
        buttons.forEach((button, index) => {
            button.setAttribute("role", "tab");
            panels[index].setAttribute("role", "tabpanel");
            panels[index].setAttribute("aria-labelledby", button.id);
            panels[index].tabIndex = 0;

            button.addEventListener("click", () => activateTab(index));
            button.addEventListener("keydown", (event) => {
                const previousKey = isVertical() ? "ArrowUp" : "ArrowLeft";
                const nextKey = isVertical() ? "ArrowDown" : "ArrowRight";
                let nextIndex;
                if (event.key === previousKey)
                    nextIndex = (index - 1 + buttons.length) % buttons.length;
                if (event.key === nextKey)
                    nextIndex = (index + 1) % buttons.length;
                if (event.key === "Home") nextIndex = 0;
                if (event.key === "End") nextIndex = buttons.length - 1;
                if (nextIndex === undefined) return;
                event.preventDefault();
                activateTab(nextIndex, true);
            });
        });

        setOrientation();
        mobileLayout.addEventListener("change", setOrientation);
        section.classList.add("profile-tabs-ready");
        tabList.hidden = false;
        activateTab(0);
        window.addEventListener("resize", moveIndicator);
    });

    /* Tiga lapis pemberdayaan: memilih lapis menyorot lingkarannya. */
    document.querySelectorAll("[data-profile-layers]").forEach((root) => {
        const layers = [...root.querySelectorAll("[data-layer]")];
        const rings = [...root.querySelectorAll("[data-ring]")];
        function select(layer) {
            layers.forEach((button) =>
                button.setAttribute(
                    "aria-pressed",
                    String(button.dataset.layer === layer),
                ),
            );
            rings.forEach((ring) =>
                ring.classList.toggle("is-active", ring.dataset.ring === layer),
            );
            root.dataset.activeLayer = layer;
        }
        /* Lingkaran juga dapat diklik; lingkaran terdalam yang menang. Daftar tombol tetap jalur keyboard. */
        rings.forEach((ring) =>
            ring.addEventListener("click", (event) => {
                event.stopPropagation();
                select(ring.dataset.ring);
            }),
        );
        layers.forEach((button) => {
            button.addEventListener("click", () => select(button.dataset.layer));
            button.addEventListener("mouseenter", () =>
                select(button.dataset.layer),
            );
            button.addEventListener("focus", () => select(button.dataset.layer));
        });
        select("1");
    });

    /* Hero: foto dalam carousel geser (sentuh, trackpad, tombol, titik, atau panah keyboard). Tanpa JavaScript foto tetap tampil. */
    document.querySelectorAll(".profile-hero .ph-visual").forEach((visual) => {
        const figures = [...visual.querySelectorAll(".ph-photo")];
        if (figures.length < 2) return;
        const details = {
            "pemberdayaan-sosial.jpg": ["Kegiatan bersama warga", "Warga dan pendamping berdiskusi dalam kegiatan pemberdayaan sosial."],
            "lks.jpg": ["Lembaga Kesejahteraan Sosial", "Ilustrasi relawan dan petugas sosial mendata serta menata kotak bantuan."],
        };
        const track = document.createElement("div");
        track.className = "profile-slides";
        track.tabIndex = 0;
        track.setAttribute("aria-label", "Foto kegiatan, geser untuk melihat foto lain");
        visual.setAttribute("role", "region");
        visual.setAttribute("aria-roledescription", "carousel");
        visual.setAttribute("aria-label", "Foto pemberdayaan sosial");
        const labels = figures.map((figure, index) => {
            const image = figure.querySelector("img");
            const [label, alt] = details[image.getAttribute("src").split("/").pop()] || ["Foto kegiatan", ""];
            image.alt = alt;
            image.loading = "eager";
            figure.setAttribute("role", "group");
            figure.setAttribute("aria-roledescription", "slide");
            figure.setAttribute("aria-label", `${index + 1} dari ${figures.length}: ${label}`);
            const caption = document.createElement("figcaption");
            caption.className = "profile-hero-caption";
            caption.textContent = label;
            figure.append(caption);
            track.append(figure);
            return label;
        });

        const controls = document.createElement("div");
        controls.className = "profile-slide-controls";
        const button = (text, label) => {
            const node = document.createElement("button");
            node.type = "button";
            node.className = "profile-slide-arrow";
            node.setAttribute("aria-label", label);
            node.innerHTML = `<svg class="profile-icon" aria-hidden="true"><use href="#profile-icon-arrow"/></svg>`;
            node.dataset.direction = text;
            return node;
        };
        const prev = button("prev", "Foto sebelumnya");
        const next = button("next", "Foto berikutnya");
        const dots = document.createElement("div");
        dots.className = "profile-slide-dots";
        const dotButtons = labels.map((label, index) => {
            const dot = document.createElement("button");
            dot.type = "button";
            dot.setAttribute("aria-label", `Foto ${index + 1}: ${label}`);
            dot.addEventListener("click", () => go(index));
            dots.append(dot);
            return dot;
        });
        controls.append(prev, dots, next);
        visual.replaceChildren(track, controls);

        let current = 0;
        const go = (index) => {
            const target = (index + figures.length) % figures.length;
            track.scrollTo({
                left: target * track.clientWidth,
                behavior: matchMedia("(prefers-reduced-motion: reduce)").matches ? "auto" : "smooth",
            });
        };
        const update = () => {
            current = Math.round(track.scrollLeft / Math.max(1, track.clientWidth));
            dotButtons.forEach((dot, index) =>
                dot.setAttribute("aria-current", String(index === current)),
            );
        };
        prev.addEventListener("click", () => go(current - 1));
        next.addEventListener("click", () => go(current + 1));
        track.addEventListener("scroll", () => requestAnimationFrame(update), { passive: true });
        update();
        visual.classList.add("is-carousel");
    });

    /* Bilah bagian yang lengket dengan penanda bagian yang sedang dibaca. */
    const toc = document.querySelector("[data-profile-toc]");
    const main = document.querySelector(".profile-main");
    if (!toc || !main || !("IntersectionObserver" in window)) return;

    const header = document.querySelector(".site-header");
    const setHeaderHeight = () =>
        document.documentElement.style.setProperty(
            "--profile-header-height",
            `${header ? header.offsetHeight : 0}px`,
        );
    setHeaderHeight();
    window.addEventListener("resize", setHeaderHeight);

    const bar = document.createElement("nav");
    bar.className = "profile-sectionbar";
    bar.setAttribute("aria-label", "Bagian halaman");
    bar.innerHTML = `<div class="container">${toc.querySelector("ol").outerHTML}</div>`;
    /* The hero paths carry their own layout; the bar keeps only the titles. */
    bar.querySelector("ol").removeAttribute("class");
    const hero = main.querySelector(".profile-hero");
    hero.after(bar);

    /* Garis kemajuan baca di dasar bilah. */
    const progress = document.createElement("span");
    progress.className = "profile-progress";
    progress.setAttribute("aria-hidden", "true");
    bar.append(progress);
    let ticking = false;
    const updateProgress = () => {
        ticking = false;
        const rect = main.getBoundingClientRect();
        const total = rect.height - window.innerHeight;
        const ratio = total > 0 ? Math.min(1, Math.max(0, -rect.top / total)) : 0;
        progress.style.transform = `scaleX(${ratio})`;
    };
    window.addEventListener(
        "scroll",
        () => {
            if (ticking) return;
            ticking = true;
            requestAnimationFrame(updateProgress);
        },
        { passive: true },
    );
    updateProgress();

    const links = [...bar.querySelectorAll("a")];
    const sections = links
        .map((link) => document.querySelector(link.getAttribute("href")))
        .filter(Boolean);

    new IntersectionObserver(
        ([entry]) => bar.classList.toggle("is-visible", !entry.isIntersecting),
        { rootMargin: "-80px 0px 0px 0px" },
    ).observe(toc);

    const list = bar.querySelector("ol");
    const visible = new Map();
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) =>
                visible.set(entry.target.id, entry.isIntersecting),
            );
            const current = sections.find((section) => visible.get(section.id));
            links.forEach((link) => {
                const active =
                    !!current && link.getAttribute("href") === `#${current.id}`;
                link.toggleAttribute("aria-current", active);
                if (active) link.setAttribute("aria-current", "true");
                /* Keep the active item visible in the horizontally scrolling bar only. */
                if (active)
                    list.scrollLeft =
                        link.offsetLeft - (list.clientWidth - link.offsetWidth) / 2;
            });
        },
        { rootMargin: "-45% 0px -50% 0px" },
    );
    sections.forEach((section) => observer.observe(section));
})();
