/* Halaman pilar LKS. Tanpa JavaScript semua layanan dan langkah tetap tampil. */
(() => {
    "use strict";

    /* Hero: fakta LKS tampil satu per satu dan bergantian otomatis sampai pengguna memilih. */
    document.querySelectorAll("[data-lks-facts]").forEach((card) => {
        const list = card.querySelector(".lks-hero-facts");
        const facts = list ? [...list.children] : [];
        if (facts.length < 2) return;
        const tabs = document.createElement("div");
        tabs.className = "lks-fact-tabs";
        tabs.setAttribute("role", "tablist");
        tabs.setAttribute("aria-label", "Fakta LKS");
        const buttons = facts.map((fact, index) => {
            const button = document.createElement("button");
            button.type = "button";
            button.setAttribute("role", "tab");
            button.textContent = fact.querySelector("dt")?.textContent.trim() || `Fakta ${index + 1}`;
            tabs.append(button);
            return button;
        });
        let current = 0;
        let timer = null;
        function show(index) {
            current = index;
            facts.forEach((fact, i) => (fact.hidden = i !== index));
            buttons.forEach((button, i) => {
                button.setAttribute("aria-selected", String(i === index));
                button.tabIndex = i === index ? 0 : -1;
            });
        }
        const reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
        const stop = () => {
            clearInterval(timer);
            timer = null;
        };
        buttons.forEach((button, index) =>
            button.addEventListener("click", () => {
                stop();
                show(index);
            }),
        );
        card.addEventListener("mouseenter", stop);
        card.addEventListener("focusin", stop);
        list.classList.add("is-rotating");
        list.after(tabs);
        show(0);
        if (!reduced)
            timer = setInterval(() => show((current + 1) % facts.length), 5000);
    });

    /* Contoh layanan: pilih kelompok sasaran untuk melihat satu contoh secara utuh. */
    document.querySelectorAll("[data-lks-services]").forEach((list) => {
        const items = [...list.children];
        if (items.length < 2) return;
        const picker = document.createElement("div");
        picker.className = "lks-service-picker";
        picker.setAttribute("role", "tablist");
        picker.setAttribute("aria-label", "Kelompok sasaran layanan");
        const buttons = items.map((item, index) => {
            const label =
                item.querySelector(".lks-service-label")?.textContent.trim() ||
                `Layanan ${index + 1}`;
            const id = `lks-service-${index + 1}`;
            item.id = id;
            item.setAttribute("role", "tabpanel");
            const button = document.createElement("button");
            button.type = "button";
            button.setAttribute("role", "tab");
            button.setAttribute("aria-controls", id);
            button.textContent = label;
            picker.append(button);
            return button;
        });

        function select(index, focus = false) {
            buttons.forEach((button, current) => {
                const active = current === index;
                button.setAttribute("aria-selected", String(active));
                button.tabIndex = active ? 0 : -1;
                items[current].hidden = !active;
            });
            if (focus) buttons[index].focus();
        }

        buttons.forEach((button, index) => {
            button.addEventListener("click", () => select(index));
            button.addEventListener("keydown", (event) => {
                const step =
                    event.key === "ArrowRight" ? 1 : event.key === "ArrowLeft" ? -1 : 0;
                if (!step) return;
                event.preventDefault();
                select((index + step + buttons.length) % buttons.length, true);
            });
        });

        list.before(picker);
        list.classList.add("is-featured");
        select(0);
    });

    /* Alur pendaftaran: tandai langkah yang sudah dikerjakan (hanya di halaman ini). */
    document.querySelectorAll("[data-lks-steps]").forEach((list) => {
        const steps = [...list.children];
        if (!steps.length) return;
        const progress = document.createElement("div");
        progress.className = "lks-steps-progress";
        progress.innerHTML =
            '<p role="status" aria-live="polite"></p><span class="lks-steps-bar" aria-hidden="true"><span></span></span>';
        const status = progress.querySelector("p");
        const fill = progress.querySelector(".lks-steps-bar span");

        function update() {
            const done = steps.filter((step) =>
                step.classList.contains("is-done"),
            ).length;
            status.textContent =
                done === steps.length
                    ? `Semua ${steps.length} langkah sudah Anda tandai.`
                    : `${done} dari ${steps.length} langkah sudah Anda tandai.`;
            fill.style.width = `${(done / steps.length) * 100}%`;
        }

        steps.forEach((step) => {
            const title = step.querySelector("h3")?.textContent.trim() || "langkah";
            const toggle = document.createElement("button");
            toggle.type = "button";
            toggle.className = "lks-step-toggle";
            toggle.setAttribute("aria-pressed", "false");
            toggle.setAttribute("aria-label", `Tandai selesai: ${title}`);
            toggle.textContent = "Tandai selesai";
            toggle.addEventListener("click", () => {
                const done = step.classList.toggle("is-done");
                toggle.setAttribute("aria-pressed", String(done));
                toggle.textContent = done ? "Selesai" : "Tandai selesai";
                update();
            });
            step.querySelector("div")?.append(toggle);
        });

        /* Keep progress and steps in one grid column of the registration layout. */
        const column = document.createElement("div");
        column.className = "lks-steps-column";
        list.before(column);
        column.append(progress, list);
        list.classList.add("is-checklist");
        update();
    });
})();
