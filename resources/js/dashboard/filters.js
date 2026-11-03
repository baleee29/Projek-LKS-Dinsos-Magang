// Filter berjalan di server lewat query string. Modul ini hanya mengirim formulir saat isian berubah.
const SEARCH_DELAY = 350;

export function initFilters() {
    // Tab dan paginasi tetap <button> seperti sumber; navigasinya lewat query string.
    document.addEventListener('click', (event) => {
        const target = event.target.closest('button[data-href]');
        if (target && !target.disabled) window.location.href = target.dataset.href;
    });

    document.querySelectorAll('form[data-filter-form]').forEach((form) => {
        let timer;
        const submit = () => {
            // Nilai kosong tidak perlu masuk URL. page selalu dibuang agar kembali ke halaman 1.
            for (const control of form.elements) {
                // Kecuali data-keep-empty: "residence" kosong berarti semua, berbeda dengan bawaan server.
                if (control.name && control.name !== form.dataset.keepEmpty && (control.value === '' || control.name === 'page')) control.disabled = true;
            }
            form.submit();
        };

        form.addEventListener('change', (event) => {
            if (event.target.matches('input[type="search"]')) return;
            // Kelurahan milik kecamatan lama tidak berlaku lagi saat kecamatan berganti.
            if (event.target.name === 'district') {
                const village = form.elements.namedItem('village');
                if (village) village.value = '';
            }
            submit();
        });
        form.addEventListener('input', (event) => {
            if (!event.target.matches('input[type="search"]')) return;
            clearTimeout(timer);
            timer = setTimeout(submit, SEARCH_DELAY);
        });
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            clearTimeout(timer);
            submit();
        });
    });

    // Kursor kembali ke akhir kotak cari setelah halaman dimuat ulang oleh pencarian.
    const search = document.querySelector('form[data-filter-form] input[type="search"][data-autofocus]');
    if (search) {
        search.focus();
        search.setSelectionRange(search.value.length, search.value.length);
    }
}
