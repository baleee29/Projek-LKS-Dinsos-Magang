// Validasi ganti kata sandi di browser. Aturannya mengikuti AccountSecurity::rule() sumber.
// TODO(Tahap 2): kirim ke PUT /account/password.
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

    form.addEventListener('submit', (event) => {
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
        status.textContent = 'Mode demo: perubahan belum disimpan.';
        status.hidden = false;
        form.reset();
    });
}
