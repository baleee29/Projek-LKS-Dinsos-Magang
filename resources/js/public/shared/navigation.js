/** Navigasi publik SiDaya: menu ponsel dan aksesibilitas keyboard. */
(() => {
    "use strict";

    document.documentElement.classList.add("js");

    const menuToggle = document.querySelector(".menu-toggle");
    const navigation = document.querySelector(".main-navigation");

    if (!menuToggle || !navigation) return;

    function closeNavigation() {
        navigation.classList.remove("is-open");
        menuToggle.setAttribute("aria-expanded", "false");
        menuToggle.setAttribute("aria-label", "Buka menu navigasi");
    }

    // Tampilkan tombol hamburger setelah JS aktif
    menuToggle.hidden = false;

    // Toggle menu navigasi mobile
    menuToggle.addEventListener("click", () => {
        const isOpen = navigation.classList.toggle("is-open");
        menuToggle.setAttribute("aria-expanded", String(isOpen));
        menuToggle.setAttribute(
            "aria-label",
            isOpen ? "Tutup menu navigasi" : "Buka menu navigasi",
        );
    });

    // Tutup menu navigasi saat tautan diklik
    navigation.querySelectorAll("a").forEach((link) => {
        link.addEventListener("click", closeNavigation);
    });

    // Tangani penekanan tombol Escape (aksesibilitas)
    document.addEventListener("keydown", (event) => {
        if (event.key !== "Escape") return;

        if (navigation.classList.contains("is-open")) {
            closeNavigation();
            menuToggle.focus();
        }
    });

    // Reset status menu saat ukuran layar kembali ke desktop
    const desktopQuery = window.matchMedia("(min-width: 901px)");
    desktopQuery.addEventListener("change", () => closeNavigation());
})();
