// Keputusan pendaftaran, dari admin/registrations.js. Alasan penolakan wajib minimal 10 karakter.
import { confirmDialog } from '../dashboard/dialogs.js';
import { afterSuccess, send } from '../dashboard/api.js';

const panel = document.querySelector('[data-registration-review]');

if (panel) {
    const note = panel.querySelector('#registration-review-note');
    const error = panel.querySelector('[data-review-error]');

    panel.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-action]');
        if (!button) return;
        const rejecting = button.dataset.action === 'reject';
        if (rejecting && note.value.trim().length < 10) {
            error.textContent = 'Tuliskan alasan penolakan minimal 10 karakter.';
            note.focus();
            return;
        }
        error.textContent = '';
        const accepted = await confirmDialog(rejecting
            ? { title: 'Tolak pendaftaran?', message: 'Alasan penolakan akan disampaikan kepada pendaftar melalui WhatsApp.', confirmText: 'Ya, tolak' }
            : { title: 'Terima pendaftaran?', message: 'Lembaga akan ditambahkan ke direktori organisasi. Akun dibuat terpisah di menu Buat Akun LKS.', confirmText: 'Ya, terima', danger: false });
        if (!accepted) return;
        const body = new FormData();
        body.set('status', rejecting ? 'Ditolak' : 'Disetujui');
        if (rejecting) body.set('notes', note.value.trim());
        try {
            afterSuccess(await send(panel.dataset.url, { body }));
        } catch (failure) {
            error.textContent = failure.message;
        }
    });
}
