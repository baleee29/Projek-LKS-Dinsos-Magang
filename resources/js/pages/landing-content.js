// Tombol simpan aktif setelah isian berubah, dan penghitung karakter, dari admin/landing-content.js.
// TODO(Tahap 2): PUT dan DELETE /admin/landing-content, POST untuk unggah foto.
document.querySelectorAll('.landing-slot').forEach((card) => {
    const input = card.querySelector('[data-landing-input]');
    const save = card.querySelector('[data-landing-save]');
    const count = card.querySelector('[data-landing-count]');
    input?.addEventListener('input', () => {
        save.disabled = input.value === input.defaultValue;
        count.textContent = `${input.value.length}/${count.dataset.max}`;
    });

    const alt = card.querySelector('[data-landing-alt]');
    const saveAlt = card.querySelector('[data-landing-save-alt]');
    alt?.addEventListener('input', () => {
        saveAlt.disabled = alt.value === alt.defaultValue;
    });

    const file = card.querySelector('[data-landing-file]');
    const upload = card.querySelector('[data-landing-upload]');
    file?.addEventListener('change', () => {
        upload.disabled = !file.files?.length;
    });
});
