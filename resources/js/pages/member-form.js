// Foto profil dan ringkasan error form personel, dari lks/member-form.js.
import { confirmDialog } from '../dashboard/dialogs.js';

const form = document.querySelector('[data-member-form]');

if (form) {
    const input = form.querySelector('#lm-photo-input');
    const preview = form.querySelector('#lm-photo-preview');
    const remove = form.querySelector('#lm-photo-remove');
    const error = form.querySelector('#lm-photo-error');
    const uploadLabel = form.querySelector('.lm-button-upload span');
    const titleInput = form.querySelector('[name="title"]');
    let hasPhoto = false;

    const placeholder = () => {
        const span = document.createElement('span');
        span.className = 'lm-photo-placeholder';
        span.setAttribute('aria-hidden', 'true');
        span.textContent = (titleInput.value || 'SDM').slice(0, 2).toUpperCase();
        preview.replaceChildren(span);
        remove.hidden = true;
        uploadLabel.textContent = 'Upload Foto Profil';
    };

    input.addEventListener('change', () => {
        const file = input.files?.[0];
        if (!file) return;
        const problem = !file.type.startsWith('image/')
            ? 'Pilih file gambar valid (JPG, PNG, atau WebP).'
            : file.size > 5 * 1024 * 1024
                ? 'Ukuran file terlalu besar. Maksimal 5 MB.'
                : '';
        error.textContent = problem;
        error.hidden = !problem;
        if (problem) return;

        const image = document.createElement('img');
        image.alt = 'Foto Profil';
        image.src = URL.createObjectURL(file);
        image.addEventListener('load', () => URL.revokeObjectURL(image.src), { once: true });
        preview.replaceChildren(image);
        hasPhoto = true;
        remove.hidden = false;
        uploadLabel.textContent = 'Ubah Foto Profil';
    });

    remove.addEventListener('click', async () => {
        const accepted = await confirmDialog({
            title: 'Hapus foto?',
            message: 'Foto akan dilepas. Perubahan tersimpan saat formulir disimpan.',
            confirmText: 'Ya, hapus foto',
        });
        if (!accepted) return;
        hasPhoto = false;
        input.value = '';
        error.hidden = true;
        placeholder();
    });

    titleInput.addEventListener('input', () => {
        if (!hasPhoto) placeholder();
    });

    form.querySelector('#lm-attachment').addEventListener('change', (event) => {
        const allowed = ['application/pdf', 'image/jpeg', 'image/png'];
        const invalid = [...event.target.files].some((file) => file.size > 5 * 1024 * 1024 || !allowed.includes(file.type));
        const node = form.querySelector('[data-lm-field-error="attachments"]');
        node.textContent = invalid ? 'Gunakan PDF, JPG, atau PNG, maksimal 5 MB per berkas.' : '';
        node.hidden = !invalid;
        if (invalid) event.target.value = '';
    });
}
