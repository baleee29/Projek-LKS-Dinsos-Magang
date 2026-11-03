// Tombol simpan aktif setelah isian berubah, dan penghitung karakter, dari admin/landing-content.js.
import { afterSuccess, send } from '../dashboard/api.js';
import { notify } from '../dashboard/toast.js';

async function kirim(button, url, body) {
    button.disabled = true;
    try {
        afterSuccess(await send(url, { body }));
    } catch (error) {
        button.disabled = false;
        notify(error.message);
    }
}

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

    const url = `/admin/landing-content/${encodeURIComponent(card.dataset.slot)}`;
    const simpanTeks = (button, control) => {
        const body = new FormData();
        body.set('value', control.value);
        kirim(button, url, body);
    };
    save?.addEventListener('click', () => simpanTeks(save, input));
    saveAlt?.addEventListener('click', () => simpanTeks(saveAlt, alt));
    upload?.addEventListener('click', () => {
        const body = new FormData();
        body.set('image', file.files[0]);
        kirim(upload, `${url}/image`, body);
    });
});
