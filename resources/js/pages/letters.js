// Buka-tutup form surat keluar dan pratinjau berkas terpilih, dari lks/letters-view.js.
const toggle = document.querySelector('#btn-toggle-add-letter');
const section = document.querySelector('#letter-form-section');

if (toggle && section) {
    const form = section.querySelector('form');
    const icon = toggle.querySelector('path');
    const label = toggle.querySelector('span');
    const input = section.querySelector('#input-letter-file');
    const preview = section.querySelector('#selected-file-preview');

    const setOpen = (open) => {
        section.hidden = !open;
        toggle.setAttribute('aria-expanded', String(open));
        icon.setAttribute('d', open ? 'M18 6 6 18M6 6l12 12' : 'M12 5v14M5 12h14');
        label.textContent = open ? 'Tutup Formulir' : toggle.dataset.openText;
        if (open) section.querySelector('input:not([type="file"]), select')?.focus();
        else toggle.focus();
    };

    const showFile = () => {
        preview.replaceChildren();
        const file = input.files?.[0];
        if (!file) return;
        const wrap = document.createElement('div');
        wrap.className = 'lks-selected-file-info';
        const meta = document.createElement('div');
        meta.className = 'lks-selected-file-meta';
        const name = document.createElement('strong');
        name.textContent = file.name;
        const size = document.createElement('span');
        size.style.cssText = 'font-size: 11px; color: var(--lks-muted, #64748b); margin-left: 8px;';
        size.textContent = `(${Math.max(1, Math.round(file.size / 1024))} KB)`;
        meta.append(name, size);
        const clear = document.createElement('button');
        clear.type = 'button';
        clear.className = 'lks-btn-clear-file';
        clear.title = 'Hapus berkas terpilih';
        clear.setAttribute('aria-label', 'Hapus berkas terpilih');
        clear.innerHTML = '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>';
        clear.addEventListener('click', () => {
            input.value = '';
            preview.replaceChildren();
        });
        wrap.append(meta, clear);
        preview.append(wrap);
    };

    toggle.addEventListener('click', () => setOpen(section.hidden));
    section.querySelector('#btn-cancel-form').addEventListener('click', () => setOpen(false));
    input.addEventListener('change', showFile);
    form.addEventListener('demo:submitted', () => {
        form.reset();
        preview.replaceChildren();
        setOpen(false);
    });
}
