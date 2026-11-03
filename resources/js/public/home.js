/** Beranda SiDaya: pemilih peran, status jam layanan, dan tombol salin kontak. */
(() => {
    "use strict";

    /* "Saya adalah…": satu panel langkah per peran. Tanpa JavaScript semua panel tampil. */
    document.querySelectorAll("[data-home-roles]").forEach((root) => {
        const tabList = root.querySelector(".home-role-tabs");
        const tabs = [...root.querySelectorAll(".home-role-tab")];
        const panels = tabs.map((tab) => document.getElementById(tab.getAttribute("aria-controls")));
        if (!tabList || !tabs.length || panels.some((panel) => !panel)) return;

        function select(index, focus = false) {
            tabs.forEach((tab, i) => {
                const active = i === index;
                tab.setAttribute("aria-selected", String(active));
                tab.tabIndex = active ? 0 : -1;
                panels[i].hidden = !active;
            });
            if (focus) tabs[index].focus();
        }
        tabs.forEach((tab, index) => {
            tab.addEventListener("click", () => select(index));
            tab.addEventListener("keydown", (event) => {
                const keys = { ArrowRight: 1, ArrowDown: 1, ArrowLeft: -1, ArrowUp: -1 };
                if (event.key === "Home" || event.key === "End") {
                    event.preventDefault();
                    select(event.key === "Home" ? 0 : tabs.length - 1, true);
                    return;
                }
                if (!keys[event.key]) return;
                event.preventDefault();
                select((index + keys[event.key] + tabs.length) % tabs.length, true);
            });
        });
        root.classList.add("is-ready");
        tabList.hidden = false;
        select(0);
    });

    /* Status layanan menurut jam WITA (tidak memperhitungkan hari libur nasional). */
    const status = document.querySelector("[data-open-status]");
    if (status) {
        const schedule = {
            1: [480, 990],
            2: [480, 990],
            3: [480, 990],
            4: [480, 990],
            5: [450, 660],
        };
        const days = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
        const clock = (minutes) =>
            `${String(Math.floor(minutes / 60)).padStart(2, "0")}.${String(minutes % 60).padStart(2, "0")}`;

        function update() {
            const parts = Object.fromEntries(
                new Intl.DateTimeFormat("en-US", {
                    timeZone: "Asia/Makassar",
                    weekday: "short",
                    hour: "numeric",
                    minute: "numeric",
                    hourCycle: "h23",
                })
                    .formatToParts(new Date())
                    .map((part) => [part.type, part.value]),
            );
            const day = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"].indexOf(parts.weekday);
            const now = Number(parts.hour) * 60 + Number(parts.minute);
            const today = schedule[day];
            let open = false;
            let text;
            if (today && now >= today[0] && now < today[1]) {
                open = true;
                text = `Sedang buka · tutup pukul ${clock(today[1])} WITA`;
            } else {
                let next = day;
                let offset = 0;
                do {
                    next = (next + 1) % 7;
                    offset += 1;
                } while (!schedule[next]);
                const startsToday = today && now < today[0];
                const label = startsToday ? "hari ini" : offset === 1 ? "besok" : days[next];
                text = `Sedang tutup · buka ${label} pukul ${clock(startsToday ? today[0] : schedule[next][0])} WITA`;
            }
            status.textContent = `${text} (di luar hari libur nasional)`;
            status.classList.toggle("is-open", open);
        }
        update();
        status.hidden = false;
        setInterval(update, 60_000);
    }

    /* Salin nomor telepon dan email. */
    document.querySelectorAll("[data-copy]").forEach((button) => {
        if (!navigator.clipboard?.writeText) return;
        button.hidden = false;
        button.addEventListener("click", async () => {
            try {
                await navigator.clipboard.writeText(button.dataset.copy);
                button.textContent = "Tersalin";
            } catch {
                button.textContent = "Gagal";
            }
            setTimeout(() => (button.textContent = "Salin"), 1800);
        });
    });
})();
