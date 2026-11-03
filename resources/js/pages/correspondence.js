// Pemeriksaan surat masuk dan kotak konfirmasi surat balasan, dari admin/correspondence*.js.
import { confirmDialog } from '../dashboard/dialogs.js';
import { DEMO_SAVE_MESSAGE, notify } from '../dashboard/toast.js';

const review = document.querySelector('[data-correspondence-review]');

if (review) {
    const note = review.elements.namedItem('note');
    const error = review.querySelector('[data-correspondence-error]');

    review.addEventListener('submit', async (event) => {
        event.preventDefault();
        const decision = event.submitter?.value;
        if (['Perlu Perbaikan', 'Ditolak'].includes(decision) && !note.value.trim()) {
            error.textContent = 'Catatan pemeriksaan wajib untuk perbaikan atau penolakan.';
            error.hidden = false;
            note.focus();
            return;
        }
        error.hidden = true;
        const accepted = await confirmDialog({
            title: 'Simpan keputusan?',
            message: `Status pengajuan akan menjadi "${decision}".`,
            confirmText: 'Ya, lanjutkan',
            danger: decision === 'Ditolak',
        });
        if (accepted) notify(DEMO_SAVE_MESSAGE);
    });
}

// Tombol terbit dan hapus aktif setelah kotak konfirmasinya dicentang.
document.querySelectorAll('[data-guarded-by]').forEach((button) => {
    const box = document.querySelector(`[${button.dataset.guardedBy}]`);
    box?.addEventListener('change', () => {
        button.disabled = !box.checked;
    });
});
