// Validasi dan pengiriman form: pesan error per isian, pratinjau berkas, dan area seret-lepas.
import { afterSuccess, send } from './api.js';
import { confirmDialog } from './dialogs.js';
import { notify } from './toast.js';

const MB = 1024 * 1024;

function errorNode(control) {
    const id = control.getAttribute('aria-describedby')?.split(' ').find((part) => part.endsWith('-error'));
    return id ? document.getElementById(id) : null;
}

export function setFieldError(control, message) {
    const node = errorNode(control);
    if (node) {
        node.textContent = message;
        node.hidden = !message;
    }
    control.setAttribute('aria-invalid', message ? 'true' : 'false');
}

function fieldMessage(control) {
    const value = control.type === 'checkbox' || control.type === 'file' ? '' : String(control.value).trim();
    const label = control.dataset.label || control.labels?.[0]?.textContent.replace('(wajib)', '').replace('*', '').trim() || 'Isian ini';

    if (control.type === 'file') {
        return control.required && !control.files.length ? `${label} wajib diunggah.` : '';
    }
    if (control.type === 'checkbox') {
        return control.required && !control.checked ? `${label} wajib dicentang.` : '';
    }
    if (control.required && !value) return `${label} wajib diisi.`;
    if (!value) return '';
    if (control.dataset.exactLength && value.length !== Number(control.dataset.exactLength)) {
        return `${label} harus ${control.dataset.exactLength} digit.`;
    }
    if (control.minLength > 0 && value.length < control.minLength) {
        return `${label} minimal ${control.minLength} karakter.`;
    }
    if (control.type === 'date' && control.max && value > control.max) {
        return `${label} tidak boleh melewati hari ini.`;
    }
    return '';
}

export function validateForm(form) {
    let first = null;
    for (const control of form.elements) {
        if (!control.name || control.disabled || control.closest('[hidden]')) continue;
        if (!control.matches('input,select,textarea')) continue;
        const message = fieldMessage(control);
        setFieldError(control, message);
        if (message && !first) first = control;
    }
    first?.focus();
    return !first;
}

function initFileInputs() {
    document.addEventListener('change', (event) => {
        const input = event.target;
        if (!input.matches('input[type="file"]')) return;
        const file = input.files?.[0];
        const maxMb = Number(input.dataset.maxMb || 0);
        const status = input.dataset.status ? document.getElementById(input.dataset.status) : null;

        if (file && maxMb && file.size > maxMb * MB) {
            input.value = '';
            if (input.getAttribute('aria-describedby')) setFieldError(input, `Ukuran berkas maksimal ${maxMb} MB.`);
            notify(`Ukuran berkas maksimal ${maxMb} MB.`);
            if (status) status.textContent = 'Belum ada berkas dipilih';
            return;
        }
        if (input.getAttribute('aria-describedby')) setFieldError(input, '');
        if (status) {
            status.textContent = file ? `${file.name} (${Math.max(1, Math.round(file.size / 1024))} KB)` : 'Belum ada berkas dipilih';
        }
        const preview = input.dataset.preview ? document.getElementById(input.dataset.preview) : null;
        if (preview) {
            preview.replaceChildren();
            if (file?.type.startsWith('image/')) {
                const image = document.createElement('img');
                image.alt = 'Pratinjau';
                image.src = URL.createObjectURL(file);
                image.addEventListener('load', () => URL.revokeObjectURL(image.src), { once: true });
                preview.append(image);
            } else if (preview.dataset.placeholder) {
                preview.textContent = preview.dataset.placeholder;
            }
        }
    });

    // Area seret-lepas meneruskan berkas ke input di dalamnya.
    document.querySelectorAll('[data-dropzone]').forEach((zone) => {
        const input = zone.querySelector('input[type="file"]');
        if (!input) return;
        ['dragenter', 'dragover'].forEach((type) => zone.addEventListener(type, (event) => {
            event.preventDefault();
            zone.classList.add('is-dragging');
        }));
        ['dragleave', 'drop'].forEach((type) => zone.addEventListener(type, (event) => {
            event.preventDefault();
            zone.classList.remove('is-dragging');
        }));
        zone.addEventListener('drop', (event) => {
            if (!event.dataTransfer?.files.length) return;
            input.files = event.dataTransfer.files;
            input.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });
}

/** Menampilkan pesan validasi server di bawah isian yang bersangkutan. */
export function showServerErrors(form, errors) {
    let first = null;
    Object.entries(errors).forEach(([name, messages]) => {
        const control = form.elements.namedItem(name);
        if (control instanceof Element && control.getAttribute('aria-describedby')) {
            setFieldError(control, messages[0]);
            first ??= control;
        }
    });
    first?.focus();
    return first !== null;
}

/**
 * form[data-form-url]: validasi, konfirmasi opsional (data-confirm, data-confirm-title,
 * data-confirm-text, data-confirm-safe), lalu kirim ke server.
 */
export function initForms() {
    initFileInputs();
    document.addEventListener('submit', async (event) => {
        const form = event.target;
        if (!form.matches('form[data-form-url]')) return;
        event.preventDefault();
        if (!validateForm(form)) return;
        const { confirm: message, confirmTitle, confirmText, confirmSafe } = form.dataset;
        if (message) {
            const accepted = await confirmDialog({
                title: confirmTitle || 'Konfirmasi',
                message,
                confirmText: confirmText || 'Ya, lanjutkan',
                danger: confirmSafe === undefined,
            });
            if (!accepted) return;
        }
        const submitter = event.submitter;
        if (submitter) submitter.disabled = true;
        try {
            afterSuccess(await send(form.dataset.formUrl, { method: form.dataset.formMethod || 'POST', body: new FormData(form) }));
        } catch (error) {
            if (submitter) submitter.disabled = false;
            if (!showServerErrors(form, error.errors || {})) notify(error.message);
        }
    });

    document.addEventListener('input', (event) => {
        const control = event.target;
        if (control.getAttribute?.('aria-invalid') === 'true') setFieldError(control, fieldMessage(control));
        // Sama seperti numeric-input.js sumber: hanya angka untuk NIK, KK, dan telepon.
        if (control.dataset?.numeric) {
            const pattern = control.dataset.numeric === 'phone' ? /[^0-9+\-\s()]/g : /\D/g;
            control.value = control.value.replace(pattern, '');
        }
    });
}
