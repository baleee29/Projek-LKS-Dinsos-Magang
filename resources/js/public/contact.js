// Contact page: live service status in WITA, today's hours highlight and copy-to-clipboard buttons.
(() => {
    const statusTag = document.querySelector("[data-contact-status]");
    const statusText = document.querySelector("[data-contact-status-text]");
    const hourRows = [...document.querySelectorAll(".contact-hours li[data-days]")];
    const toast = document.querySelector("[data-contact-toast]");

    // Office hours are published in WITA (UTC+8), independent of the visitor's time zone.
    function witaNow() {
        const now = new Date();
        return new Date(now.getTime() + now.getTimezoneOffset() * 60000 + 8 * 3600000);
    }

    function toMinutes(value) {
        const [hours, minutes] = value.split(":").map(Number);
        return hours * 60 + minutes;
    }

    function updateStatus() {
        const now = witaNow();
        const day = String(now.getDay());
        const minutes = now.getHours() * 60 + now.getMinutes();
        let state = "closed";
        let label = "Tutup hari ini";

        hourRows.forEach((row) => {
            const isToday = row.dataset.days.split(" ").includes(day);
            row.classList.toggle("is-today", isToday);
            if (!isToday || !row.dataset.open) return;
            const [start, end] = row.dataset.open.split("-").map(toMinutes);
            if (minutes < start) {
                label = `Buka pukul ${row.dataset.open.split("-")[0].replace(":", ".")} WITA`;
            } else if (minutes < end) {
                state = "open";
                label = `Buka sekarang · tutup ${row.dataset.open.split("-")[1].replace(":", ".")} WITA`;
            } else {
                label = "Sudah tutup hari ini";
            }
        });

        if (statusTag && statusText) {
            statusTag.dataset.state = state;
            statusText.textContent = label;
        }
    }

    let toastTimer;
    function showToast(message) {
        if (!toast) return;
        toast.textContent = message;
        toast.hidden = false;
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
            toast.hidden = true;
        }, 2200);
    }

    document.querySelectorAll("[data-copy]").forEach((button) => {
        const original = button.textContent;
        button.addEventListener("click", async () => {
            try {
                await navigator.clipboard.writeText(button.dataset.copy);
                button.textContent = "Tersalin ✓";
                button.classList.add("is-copied");
                showToast("Berhasil disalin ke papan klip.");
            } catch {
                showToast("Gagal menyalin. Salin secara manual.");
            }
            setTimeout(() => {
                button.textContent = original;
                button.classList.remove("is-copied");
            }, 1800);
        });
    });

    updateStatus();
    setInterval(updateStatus, 60000);
})();
