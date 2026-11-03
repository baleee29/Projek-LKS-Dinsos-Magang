// Editor laporan semester, dari lks/report-editor.js dan shared/reports-editor.js.
import { afterSuccess, send } from '../dashboard/api.js';
import { confirmDialog } from '../dashboard/dialogs.js';
import { notify } from '../dashboard/toast.js';

const form = document.querySelector('[data-q-editor]');

if (form) {
    const MAX_FILES = 10;
    const MAX_SIZE = 5 * 1024 * 1024;
    const title = form.querySelector('#q-field-title');
    const titleError = form.querySelector('#q-field-title-error');
    const input = form.querySelector('#q-report-files');
    const uploadError = form.querySelector('[data-upload-error]');
    const list = form.querySelector('[data-q-attachments]');
    const completeness = form.querySelector('[data-q-completeness]');
    const errorSummary = form.querySelector('[data-q-errors]');
    const preview = document.querySelector('#report-preview-dialog');
    const files = JSON.parse(list.dataset.saved || '[]');

    const size = (bytes) => `${Math.max(1, Math.ceil(bytes / 1024)).toLocaleString('id-ID')} KB`;

    function render() {
        list.replaceChildren();
        if (files.length) {
            const wrap = document.createElement('ul');
            wrap.className = 'psm-file-list';
            files.forEach((file, index) => {
                const item = document.createElement('li');
                const meta = document.createElement('span');
                meta.textContent = file.name;
                const detail = document.createElement('small');
                detail.textContent = `${file.type || 'Dokumen'} · ${size(file.size)}`;
                meta.append(detail);
                const view = document.createElement('button');
                view.type = 'button';
                view.className = 'text-button';
                view.textContent = 'Lihat';
                if (file.url) view.dataset.downloadUrl = file.url;
                else view.hidden = true;
                const remove = document.createElement('button');
                remove.type = 'button';
                remove.className = 'psm-delete-button';
                remove.textContent = 'Hapus';
                remove.setAttribute('aria-label', `Hapus lampiran ${file.name}`);
                remove.addEventListener('click', async () => {
                    const accepted = await confirmDialog({
                        title: 'Hapus lampiran?',
                        message: `Lampiran "${file.name}" akan dilepas dari laporan ini. Perubahan tersimpan saat draf disimpan.`,
                        confirmText: 'Ya, hapus lampiran',
                    });
                    if (!accepted) return;
                    files.splice(index, 1);
                    render();
                });
                item.append(meta, view, remove);
                wrap.append(item);
            });
            list.append(wrap);
        } else {
            const empty = document.createElement('p');
            empty.className = 'muted';
            empty.textContent = 'Belum ada lampiran.';
            list.append(empty);
        }
        const missing = [];
        if (!title.value.trim()) missing.push('judul laporan');
        if (!files.length) missing.push('berkas laporan');
        completeness.textContent = missing.length ? `Belum lengkap: ${missing.join(' dan ')}.` : 'Judul dan berkas laporan sudah lengkap.';
    }

    input.addEventListener('change', () => {
        const chosen = [...input.files];
        const tooLarge = chosen.find((file) => file.size > MAX_SIZE);
        let message = '';
        if (tooLarge) message = `Berkas "${tooLarge.name}" melebihi 5 MB.`;
        else if (files.length + chosen.length > MAX_FILES) message = 'Maksimal 10 berkas untuk satu laporan.';
        else chosen.forEach((file) => files.push({ name: file.name, size: file.size, type: file.type, file }));
        uploadError.textContent = message;
        uploadError.hidden = !message;
        input.value = '';
        render();
    });

    title.addEventListener('input', () => {
        titleError.hidden = true;
        title.setAttribute('aria-invalid', 'false');
        render();
    });

    form.querySelector('[data-preview]').addEventListener('click', () => {
        const problems = [];
        if (!title.value.trim()) problems.push('Judul atau perihal laporan wajib diisi.');
        if (!files.length) problems.push('Unggah minimal satu berkas laporan.');
        titleError.textContent = title.value.trim() ? '' : problems[0];
        titleError.hidden = Boolean(title.value.trim());
        title.setAttribute('aria-invalid', String(!title.value.trim()));
        if (problems.length) {
            const heading = document.createElement('h2');
            heading.textContent = 'Laporan belum dapat dikirim';
            const ul = document.createElement('ul');
            problems.forEach((text) => {
                const li = document.createElement('li');
                li.textContent = text;
                ul.append(li);
            });
            errorSummary.replaceChildren(heading, ul);
            errorSummary.hidden = false;
            errorSummary.focus();
            return;
        }
        errorSummary.hidden = true;
        preview.querySelector('[data-preview-title]').textContent = title.value.trim();
        preview.querySelector('[data-preview-count]').textContent = `${files.length} berkas terlampir`;
        preview.querySelector('[data-preview-files-title]').textContent = `Berkas Dokumen Laporan (${files.length})`;
        preview.querySelector('[data-preview-files]').replaceChildren(list.querySelector('ul').cloneNode(true));
        preview.querySelectorAll('[data-preview-files] .psm-delete-button').forEach((node) => node.remove());
        preview.showModal();
    });

    // Simpan draf atau kirim. Berkas baru dikirim bersama daftar berkas lama yang dipertahankan.
    async function save(action) {
        const body = new FormData();
        body.set('action', action);
        body.set('version', form.dataset.version);
        body.set('title', title.value.trim());
        files.forEach((item) => {
            if (item.id) body.append('kept[]', item.id);
            else body.append('files[]', item.file);
        });
        return send(form.dataset.url, { body });
    }

    document.querySelectorAll('[data-report-save]').forEach((button) => {
        button.addEventListener('click', async () => {
            button.disabled = true;
            try {
                afterSuccess(await save('draft'));
            } catch (error) {
                button.disabled = false;
                notify(error.message);
            }
        });
    });

    preview.querySelector('[data-report-send]')?.addEventListener('click', async (event) => {
        const button = event.currentTarget;
        const accepted = await confirmDialog({
            title: button.dataset.confirmTitle,
            message: button.dataset.confirm,
            confirmText: button.dataset.confirmText,
            danger: false,
        });
        if (!accepted) return;
        button.disabled = true;
        try {
            afterSuccess(await save('send'));
        } catch (error) {
            button.disabled = false;
            preview.close();
            notify(error.message);
        }
    });

    render();
}
