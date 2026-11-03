// Ganti kata sandi Admin. Aturannya sama dengan KeamananAkun::aturanSandi() di server.
const form = document.querySelector('#change-password-form');

if (form) {
    const fields = {
        current: form.querySelector('#current_password'),
        next: form.querySelector('#password'),
        confirm: form.querySelector('#password_confirmation'),
    };
    const errors = {
        current: form.querySelector('#current-password-error'),
        next: form.querySelector('#password-error'),
        confirm: form.querySelector('#password-confirmation-error'),
    };
    const status = form.querySelector('#form-status');

    const show = (key, message) => {
        errors[key].textContent = message;
        errors[key].hidden = !message;
        fields[key].setAttribute('aria-invalid', message ? 'true' : 'false');
    };

    form.querySelector('#show-password').addEventListener('change', (event) => {
        const type = event.target.checked ? 'text' : 'password';
        Object.values(fields).forEach((input) => { input.type = type; });
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const next = fields.next.value;
        const messages = {
            current: fields.current.value ? '' : 'Isi kata sandi saat ini.',
            next: next.length < 10 || !/\p{L}/u.test(next) || !/\p{N}/u.test(next)
                ? 'Kata sandi baru minimal 10 karakter dan memuat huruf serta angka.'
                : '',
            confirm: fields.confirm.value === next ? '' : 'Ulangan kata sandi baru belum sama.',
        };
        Object.entries(messages).forEach(([key, message]) => show(key, message));

        const invalid = Object.keys(messages).find((key) => messages[key]);
        if (invalid) {
            fields[invalid].focus();
            status.hidden = true;
            return;
        }
        const body = new URLSearchParams({
            _method: 'PUT',
            current_password: fields.current.value,
            password: next,
            password_confirmation: fields.confirm.value,
        });
        const response = await fetch('/account/password', {
            method: 'POST',
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body,
        }).catch(() => null);
        const data = response ? await response.json().catch(() => ({})) : {};
        if (response?.ok) {
            window.location.assign(data.redirect);
            return;
        }
        if (data.errors?.current_password) show('current', data.errors.current_password[0]);
        if (data.errors?.password) show('next', data.errors.password[0]);
        status.textContent = data.errors ? 'Periksa kembali isian di atas.' : (data.message || 'Tidak dapat terhubung ke server. Coba lagi.');
        status.hidden = false;
    });
}
