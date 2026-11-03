/** Pilar Sosial: panel yang melebar dan pencocok kebutuhan. Semua tautan tetap memakai navigasi browser. */
(() => {
    "use strict";

    const grid = document.getElementById("pillar-directory-grid");
    if (!grid) return;
    const items = [...grid.querySelectorAll(".pillar-panel-item")];
    if (!items.length) return;

    /* Satu panel aktif pada satu waktu; mengikuti kursor dan fokus papan ketik. */
    function activate(item) {
        items.forEach((current) => current.classList.toggle("is-active", current === item));
        grid.classList.add("has-active");
    }
    items.forEach((item) => {
        item.addEventListener("mouseenter", () => activate(item));
        item.addEventListener("focusin", () => activate(item));
    });
    grid.addEventListener("mouseleave", () => {
        const highlighted = items.find((item) => item.classList.contains("is-matched"));
        if (highlighted) activate(highlighted);
    });
    activate(items[0]);

    /* "Saya ingin…": memilih kebutuhan menyorot pilar yang paling sesuai. */
    const matcher = document.querySelector("[data-pillar-match]");
    if (!matcher) return;
    const options = [...matcher.querySelectorAll("[data-match]")];
    const result = matcher.querySelector(".pillar-match-result");
    const reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    options.forEach((option) => {
        option.addEventListener("click", () => {
            const item = items.find((candidate) => candidate.dataset.pillar === option.dataset.match);
            if (!item) return;
            options.forEach((other) => other.setAttribute("aria-pressed", String(other === option)));
            items.forEach((candidate) => candidate.classList.toggle("is-matched", candidate === item));
            activate(item);

            const link = item.querySelector(".pillar-panel");
            const title = item.querySelector(".pillar-panel-title")?.textContent.trim() || "";
            result.replaceChildren();
            const heading = document.createElement("p");
            heading.className = "pillar-match-pick";
            heading.textContent = title;
            const text = document.createElement("p");
            text.textContent = option.dataset.matchText || "";
            const go = document.createElement("a");
            go.className = "pillar-match-go";
            go.href = link.getAttribute("href");
            go.textContent = `Buka halaman ${title}`;
            result.append(heading, text, go);

            item.scrollIntoView({ behavior: reduced ? "auto" : "smooth", block: "nearest" });
        });
    });
    matcher.hidden = false;
})();
