// Batalkan perubahan dan peringatan perubahan belum disimpan, dari admin/account-settings.js.
import { confirmDialog } from '../dashboard/dialogs.js';

const form = document.querySelector('#admin-account-form');

if (form) {
    const note = form.querySelector('#account-unsaved');
    const initial = () => {
        const values = {};
        for (const control of form.elements) {
            if (!control.name) continue;
            values[control.name] = control.type === 'checkbox' ? control.defaultChecked : control.defaultValue;
        }
        return values;
    };
    const baseline = initial();
    const isDirty = () => [...form.elements].some((control) => control.name
        && (control.type === 'checkbox' ? control.checked : control.value) !== baseline[control.name]);
    const sync = () => {
        note.hidden = !isDirty();
    };

    form.addEventListener('input', sync);
    form.addEventListener('change', sync);

    form.querySelector('#account-reset').addEventListener('click', () => {
        form.reset();
        form.querySelectorAll('[aria-invalid="true"]').forEach((control) => control.setAttribute('aria-invalid', 'false'));
        form.querySelectorAll('.q-field-error').forEach((node) => { node.hidden = true; });
        sync();
    });

    // Peringatan bawaan browser saat menutup tab; tautan di dalam dashboard memakai dialog yang sama seperti sumber.
    window.addEventListener('beforeunload', (event) => {
        if (isDirty()) event.preventDefault();
    });
    document.addEventListener('click', async (event) => {
        const link = event.target.closest('a[href]');
        if (!link || !isDirty() || link.target === '_blank' || link.getAttribute('href').startsWith('#')) return;
        event.preventDefault();
        const leave = await confirmDialog({
            title: 'Tinggalkan halaman?',
            message: 'Perubahan pada formulir ini belum disimpan.',
            confirmText: 'Ya, tinggalkan',
        });
        if (leave) {
            form.reset();
            window.location.href = link.href;
        }
    });
}
