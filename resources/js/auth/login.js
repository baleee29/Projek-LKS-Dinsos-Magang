// Interaksi halaman login, dipindahkan dari Login.vue: maskot, logo latar, sandi, dan dialog footer.
const reduceMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const random = Math.random;

const crew = [...document.querySelectorAll('.crew-member')].map((button) => ({
    button,
    svg: button.querySelector('svg'),
    pupils: [...button.querySelectorAll('.crew-pupil')],
    greeting: button.dataset.greeting,
    timer: null,
}));

// Mata maskot mengikuti pointer.
let lookFrame = 0;
window.addEventListener('pointermove', (event) => {
    cancelAnimationFrame(lookFrame);
    lookFrame = requestAnimationFrame(() => {
        crew.forEach((member) => {
            const box = member.svg.getBoundingClientRect();
            const dx = event.clientX - (box.left + box.width / 2);
            const dy = event.clientY - (box.top + box.height * 0.4);
            const distance = Math.hypot(dx, dy) || 1;
            const reach = Math.min(1, distance / 180) * 3.8;
            member.pupils.forEach((pupil) => {
                pupil.setAttribute('cx', Number(pupil.dataset.x) + (dx / distance) * reach);
                pupil.setAttribute('cy', Number(pupil.dataset.y) + (dy / distance) * reach);
            });
        });
    });
});

crew.forEach((member) => {
    member.button.addEventListener('click', () => {
        member.button.classList.remove('is-jumping');
        requestAnimationFrame(() => member.button.classList.add('is-jumping'));

        let bubble = member.button.querySelector('.crew-bubble');
        if (!bubble) {
            bubble = document.createElement('span');
            bubble.className = 'crew-bubble bubble-enter-active bubble-enter-from';
            bubble.textContent = member.greeting;
            member.button.prepend(bubble);
            requestAnimationFrame(() => requestAnimationFrame(() => bubble.classList.remove('bubble-enter-from')));
        }
        clearTimeout(member.timer);
        member.timer = setTimeout(() => bubble.remove(), 1800);
    });
    member.button.addEventListener('animationend', (event) => {
        if (event.target === member.button) member.button.classList.remove('is-jumping');
    });
});

function scheduleBlink() {
    setTimeout(() => {
        const member = crew[Math.floor(random() * crew.length)];
        member.button.classList.add('is-blinking');
        setTimeout(() => member.button.classList.remove('is-blinking'), 160);
        scheduleBlink();
    }, 1200 + random() * 2200);
}
if (crew.length && !reduceMotion()) scheduleBlink();

// Cahaya panel sambutan mengikuti pointer.
const intro = document.querySelector('.login-intro');
intro?.addEventListener('pointermove', (event) => {
    if (reduceMotion()) return;
    const box = intro.getBoundingClientRect();
    intro.style.setProperty('--px', ((event.clientX - box.left) / box.width).toFixed(3));
    intro.style.setProperty('--py', ((event.clientY - box.top) / box.height).toFixed(3));
});
intro?.addEventListener('pointerleave', () => {
    intro.style.setProperty('--px', 0.5);
    intro.style.setProperty('--py', 0.5);
});

// Logo latar: mengambang, menjauhi pointer, dan berputar saat diketuk.
const field = document.querySelector('.login-logo-field');
const page = document.querySelector('.login-page');
const logos = [];
if (field) {
    for (let index = 0; index < 22; index += 1) {
        const size = 28 + Math.round(random() * 34);
        const bubble = document.createElement('span');
        bubble.className = 'login-logo-bubble';
        const x = ((index * 37) % 100) + random() * 6 - 3;
        const y = ((index * 53) % 100) + random() * 6 - 3;
        Object.assign(bubble.style, {
            left: `${x}%`,
            top: `${y}%`,
            width: `${size}px`,
            height: `${size}px`,
        });
        const image = document.createElement('img');
        image.src = '/assets/images/branding/sidaya-logo-480.webp';
        image.alt = '';
        image.draggable = false;
        image.style.setProperty('--tilt', `${Math.round(random() * 40 - 20)}deg`);
        image.style.animationDelay = `${-(random() * 12).toFixed(2)}s`;
        image.style.animationDuration = `${(9 + random() * 8).toFixed(2)}s`;
        image.addEventListener('animationend', () => image.classList.remove('is-spinning'));
        bubble.addEventListener('click', () => {
            image.classList.remove('is-spinning');
            requestAnimationFrame(() => image.classList.add('is-spinning'));
        });
        bubble.append(image);
        field.append(bubble);
        logos.push({ bubble, x, y });
    }
}
let logoFrame = 0;
page?.addEventListener('pointermove', (event) => {
    if (!field || reduceMotion()) return;
    cancelAnimationFrame(logoFrame);
    logoFrame = requestAnimationFrame(() => {
        const box = field.getBoundingClientRect();
        logos.forEach((logo) => {
            const distX = box.left + (logo.x / 100) * box.width - event.clientX;
            const distY = box.top + (logo.y / 100) * box.height - event.clientY;
            const distance = Math.hypot(distX, distY) || 1;
            const push = Math.max(0, 160 - distance) / 160;
            logo.bubble.style.transform = `translate(${(distX / distance) * push * 46}px, ${(distY / distance) * push * 46}px)`;
        });
    });
});
page?.addEventListener('pointerleave', () => {
    logos.forEach((logo) => { logo.bubble.style.transform = 'translate(0px, 0px)'; });
});

// Kata sandi: tampilkan/sembunyikan, Caps Lock, maskot malu, dan daftar aturan.
const password = document.querySelector('#password');
const toggle = document.querySelector('.password-toggle');
const crewBox = document.querySelector('.login-crew');
const caps = document.querySelector('.caps-warning');
const iconShown = document.querySelector('[data-password-icon="shown"]');
const iconHidden = document.querySelector('[data-password-icon="hidden"]');
let showing = false;
let focused = false;

const syncShy = () => crewBox?.classList.toggle('is-shy', focused && !showing);

toggle?.addEventListener('click', () => {
    showing = !showing;
    password.type = showing ? 'text' : 'password';
    toggle.setAttribute('aria-label', showing ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
    toggle.setAttribute('aria-pressed', String(showing));
    iconShown.toggleAttribute('hidden', !showing);
    iconHidden.toggleAttribute('hidden', showing);
    syncShy();
});

const checkCaps = (event) => {
    if (caps) caps.hidden = !event.getModifierState?.('CapsLock');
};
password?.addEventListener('keydown', checkCaps);
password?.addEventListener('keyup', checkCaps);
password?.addEventListener('focus', () => { focused = true; syncShy(); });
password?.addEventListener('blur', () => {
    focused = false;
    if (caps) caps.hidden = true;
    syncShy();
});

// Informatif saja, sama seperti sumber: aturan ini tidak menghalangi login.
const rules = {
    length: (value) => value.length >= 10,
    letter: (value) => /\p{L}/u.test(value),
    number: (value) => /\p{N}/u.test(value),
};
password?.addEventListener('input', () => {
    document.querySelectorAll('[data-rule]').forEach((item) => {
        const met = rules[item.dataset.rule](password.value);
        item.classList.toggle('is-met', met);
        item.querySelector('.password-rule-icon').textContent = met ? '✓' : '•';
        item.querySelector('.visually-hidden').textContent = met ? '(terpenuhi)' : '(belum terpenuhi)';
    });
});

// Kartu bergetar bila server menolak isian.
const card = document.querySelector('.login-card');
if (card?.dataset.hasErrors) {
    requestAnimationFrame(() => card.classList.add('is-shaking'));
}
card?.addEventListener('animationend', (event) => {
    if (event.target === card) card.classList.remove('is-shaking');
});

// Dialog info footer.
const footerDialog = document.querySelector('.site-chrome-dialog');
let trigger = null;
document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-content-message]');
    if (!button || !footerDialog) return;
    trigger = button;
    footerDialog.querySelector('#footer-info-title').textContent = button.dataset.contentTitle;
    footerDialog.querySelector('#footer-info-message').textContent = button.dataset.contentMessage;
    footerDialog.showModal();
});
footerDialog?.addEventListener('close', () => trigger?.focus());
