// Form akun LKS dan dialog reset sandi, dari admin/partner-accounts.js. Tidak ada yang dikirim atau disimpan.
// TODO(Tahap 2): POST /admin/partner-accounts dan POST /admin/partner-accounts/{id}/reset-password.
import { confirmDialog } from '../dashboard/dialogs.js';
import { DEMO_SAVE_MESSAGE, notify } from '../dashboard/toast.js';

function generatePassword() {
    const digits = new Uint32Array(8);
    crypto.getRandomValues(digits);
    return 'LKS#' + Array.from(digits, (n) => n % 10).join('');
}

const validPassword = (value) => value.length >= 10 && /[A-Za-z]/.test(value) && /\d/.test(value);

const form = document.querySelector('#form-create-account');

if (form) {
    const select = form.querySelector('#acc-reg-select');
    const whatsapp = form.querySelector('#acc-whatsapp');
    const name = form.querySelector('#acc-name');
    const username = form.querySelector('#acc-username');
    const password = form.querySelector('#acc-password');
    const alert = form.querySelector('#form-error-alert');

    select.addEventListener('change', () => {
        const option = select.selectedOptions[0];
        if (!option?.value) return;
        whatsapp.value = option.dataset.whatsapp;
        name.value = option.dataset.name;
    });

    form.querySelector('#btn-gen-username').addEventListener('click', () => {
        const slug = name.value.toLowerCase().replace(/[^a-z0-9]+/g, '').slice(0, 20);
        username.value = slug ? `lks_${slug}` : '';
        username.focus();
    });
    form.querySelector('#btn-gen-password').addEventListener('click', () => {
        password.value = generatePassword();
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const problems = [];
        if (!whatsapp.value.trim()) problems.push('Nomor WhatsApp wajib diisi.');
        if (!name.value.trim()) problems.push('Nama lembaga wajib diisi.');
        if (!username.value.trim()) problems.push('Username wajib diisi.');
        if (!validPassword(password.value.trim())) problems.push('Kata sandi minimal 10 karakter dan memuat huruf serta angka.');
        alert.textContent = problems.join(' ');
        alert.hidden = !problems.length;
        if (problems.length) return;
        const accepted = await confirmDialog({
            title: 'Simpan akun LKS?',
            message: `Akun ${name.value.trim()} akan dibuat.`,
            confirmText: 'Ya, simpan',
            danger: false,
        });
        if (!accepted) return;
        notify(DEMO_SAVE_MESSAGE);
        password.value = '';
    });

}

const resetDialog = document.querySelector('#admin-accounts-reset-dialog');

if (resetDialog) {
    const input = resetDialog.querySelector('#modal-new-password');
    const error = resetDialog.querySelector('#modal-password-error');
    let trigger = null;

    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-reset-acc]');
        if (!button) return;
        trigger = button;
        resetDialog.querySelector('[data-reset-name]').textContent = button.dataset.resetAcc;
        input.value = generatePassword();
        error.hidden = true;
        resetDialog.showModal();
        input.focus();
        input.select();
    });
    resetDialog.querySelector('#modal-btn-regenerate').addEventListener('click', () => {
        input.value = generatePassword();
        input.focus();
    });
    resetDialog.addEventListener('click', (event) => {
        if (event.target === resetDialog) resetDialog.close();
    });
    resetDialog.addEventListener('close', () => {
        input.value = '';
        trigger?.focus();
    });
    resetDialog.querySelector('[data-modal-confirm]').addEventListener('click', async () => {
        const value = input.value.trim();
        if (!validPassword(value)) {
            error.textContent = 'Kata sandi minimal 10 karakter dan memuat huruf serta angka.';
            error.hidden = false;
            input.focus();
            return;
        }
        const accountName = resetDialog.querySelector('[data-reset-name]').textContent;
        resetDialog.close();
        const accepted = await confirmDialog({
            title: 'Ganti Kata Sandi?',
            message: `Apakah Anda yakin ingin mengganti kata sandi akun ${accountName}?`,
            confirmText: 'Ya, Ganti Kata Sandi',
            danger: false,
        });
        if (accepted) notify(DEMO_SAVE_MESSAGE);
    });
}
