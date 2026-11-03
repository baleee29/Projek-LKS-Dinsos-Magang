/* Hero halaman publik: fakta tampil satu per satu dan bergantian otomatis sampai pengguna memilih.
   Tanpa JavaScript semua fakta tetap tampil sebagai daftar. */
(() => {
    "use strict";

    const reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    document.querySelectorAll("[data-ph-facts]").forEach((card) => {
        const list = card.querySelector(".ph-facts");
        const facts = list ? [...list.children] : [];
        if (facts.length < 2) return;
        const tabs = document.createElement("div");
        tabs.className = "ph-fact-tabs";
        tabs.setAttribute("role", "tablist");
        tabs.setAttribute("aria-label", card.querySelector("h2")?.textContent.trim() || "Fakta");
        const buttons = facts.map((fact, index) => {
            const button = document.createElement("button");
            button.type = "button";
            button.setAttribute("role", "tab");
            button.textContent =
                fact.querySelector("dt")?.textContent.trim() || `Fakta ${index + 1}`;
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
        const stop = () => {
            clearInterval(timer);
            timer = null;
        };
        buttons.forEach((button, index) => {
            button.addEventListener("click", () => {
                stop();
                show(index);
            });
            button.addEventListener("keydown", (event) => {
                const step =
                    event.key === "ArrowRight" ? 1 : event.key === "ArrowLeft" ? -1 : 0;
                if (!step) return;
                event.preventDefault();
                stop();
                show((index + step + buttons.length) % buttons.length);
                buttons[current].focus();
            });
        });
        card.addEventListener("mouseenter", stop);
        card.addEventListener("focusin", stop);
        list.classList.add("is-rotating");
        list.after(tabs);
        show(0);
        if (!reduced)
            timer = setInterval(() => show((current + 1) % facts.length), 5000);
    });
})();
