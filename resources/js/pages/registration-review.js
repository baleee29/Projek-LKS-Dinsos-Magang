// Keputusan pendaftaran, dari admin/registrations.js. Alasan penolakan wajib minimal 10 karakter.
import { confirmDialog } from '../dashboard/dialogs.js';
import { DEMO_SAVE_MESSAGE, notify } from '../dashboard/toast.js';

const panel = document.querySelector('[data-registration-review]');

if (panel) {
    const note = panel.querySelector('#registration-review-note');
    const error = panel.querySelector('[data-review-error]');
    const whatsapp = panel.querySelector('[data-review-whatsapp]');

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
        notify(DEMO_SAVE_MESSAGE);
        // Tautan WhatsApp tetap berguna di demo; isinya tidak mengubah data apa pun.
        whatsapp.hidden = !rejecting;
    });
}
