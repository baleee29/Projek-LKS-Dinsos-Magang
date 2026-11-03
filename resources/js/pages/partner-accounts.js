// Form akun LKS dan dialog reset sandi, dari admin/partner-accounts.js.
import { send } from '../dashboard/api.js';
import { confirmDialog } from '../dashboard/dialogs.js';
import { notify } from '../dashboard/toast.js';

function generatePassword() {
    const digits = new Uint32Array(8);
    crypto.getRandomValues(digits);
    return 'LKS#' + Array.from(digits, (n) => n % 10).join('');
}

/** Sandi hanya tampil di tautan WhatsApp ini, lalu halaman dimuat ulang. */
async function offerWhatsapp(title, data) {
    if (data.whatsapp_url) {
        const send = await confirmDialog({
            title,
            message: 'Kirim data login ke pengurus lewat WhatsApp sekarang? Kata sandi tidak dapat dilihat lagi setelah jendela ini ditutup.',
            confirmText: 'Kirim via WhatsApp',
            cancelText: 'Tutup',
            danger: false,
        });
        if (send) window.open(data.whatsapp_url, '_blank', 'noopener');
    }
    window.location.reload();
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
        const body = new FormData();
        body.set('registration_id', select.value);
        body.set('whatsapp', whatsapp.value.trim());
        body.set('name', name.value.trim());
        body.set('username', username.value.trim());
        body.set('password', password.value.trim());
        try {
            const data = await send('/admin/partner-accounts', { body });
            password.value = '';
            await offerWhatsapp('Akun LKS tersimpan', data);
        } catch (error) {
            alert.textContent = error.message;
            alert.hidden = false;
        }
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
        const url = trigger.dataset.resetUrl;
        resetDialog.close();
        const accepted = await confirmDialog({
            title: 'Ganti Kata Sandi?',
            message: `Apakah Anda yakin ingin mengganti kata sandi akun ${accountName}?`,
            confirmText: 'Ya, Ganti Kata Sandi',
            danger: false,
        });
        if (!accepted) return;
        const body = new FormData();
        body.set('password', value);
        try {
            await offerWhatsapp('Kata sandi diganti', await send(url, { body }));
        } catch (error) {
            notify(error.message);
        }
    });
}
