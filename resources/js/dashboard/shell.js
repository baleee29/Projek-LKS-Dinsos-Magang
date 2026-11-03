// Kerangka dashboard, dipindahkan dari Dashboard.vue dan DashboardSidebar.vue.
const $ = (selector) => document.querySelector(selector);

function initTheme() {
    const toggle = $('#theme-toggle');
    if (!toggle) return;
    const root = document.documentElement;
    const sync = () => {
        const dark = root.classList.contains('dark');
        toggle.setAttribute('aria-label', dark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap');
        toggle.querySelector('use').setAttribute('href', dark ? '#icon-moon' : '#icon-sun');
    };
    toggle.addEventListener('click', () => {
        const dark = root.classList.toggle('dark');
        try {
            localStorage.setItem('sidaya-theme', dark ? 'dark' : 'light');
        } catch {
            // Tema tetap berlaku di halaman ini bila penyimpanan browser ditolak.
        }
        sync();
    });
    sync();
}

function initNavGroups() {
    document.querySelectorAll('[data-nav-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const target = document.getElementById(button.getAttribute('aria-controls'));
            const open = button.getAttribute('aria-expanded') !== 'true';
            button.setAttribute('aria-expanded', String(open));
            target.hidden = !open;
        });
    });
    // Halaman dimuat penuh, jadi posisi gulir sidebar dipulihkan dengan menampilkan menu aktif.
    $('#side-nav .nav-item.is-active')?.scrollIntoView({ block: 'nearest' });
}

function initSidebar() {
    const shell = $('#dashboard-shell');
    const sidebar = $('#sidebar');
    const main = $('#main-area');
    const backdrop = $('#sidebar-backdrop');
    const menu = $('#mobile-menu');
    const close = $('#sidebar-close');
    const collapseToggle = $('#sidebar-toggle');
    if (!shell || !sidebar) return;

    const media = window.matchMedia('(max-width: 980px)');
    let mobileOpen = false;
    let collapsed = false;

    const syncInert = () => {
        sidebar.inert = media.matches ? !mobileOpen : collapsed;
        main.inert = mobileOpen;
    };

    function setMobile(open, restore = true) {
        const previous = mobileOpen;
        mobileOpen = open && media.matches;
        document.body.classList.toggle('drawer-open', mobileOpen);
        shell.classList.toggle('mobile-nav-open', mobileOpen);
        backdrop.hidden = !mobileOpen;
        menu.setAttribute('aria-expanded', String(mobileOpen));
        if (mobileOpen) {
            sidebar.setAttribute('role', 'dialog');
            sidebar.setAttribute('aria-modal', 'true');
        } else {
            sidebar.removeAttribute('role');
            sidebar.removeAttribute('aria-modal');
        }
        syncInert();
        if (mobileOpen) close.focus();
        else if (previous && restore) menu.focus();
    }

    menu.addEventListener('click', () => setMobile(!mobileOpen));
    close.addEventListener('click', () => setMobile(false));
    backdrop.addEventListener('click', () => setMobile(false));
    collapseToggle.addEventListener('click', () => {
        collapsed = !collapsed;
        shell.classList.toggle('sidebar-collapsed', collapsed);
        collapseToggle.setAttribute('aria-label', collapsed ? 'Tampilkan sidebar' : 'Sembunyikan sidebar');
        collapseToggle.setAttribute('aria-expanded', String(!collapsed));
        syncInert();
    });
    media.addEventListener('change', () => {
        shell.classList.toggle('sidebar-collapsed', !media.matches && collapsed);
        setMobile(false, false);
    });

    document.addEventListener('keydown', (event) => {
        if (!mobileOpen || document.querySelector('dialog[open]')) return;
        if (event.key === 'Escape') {
            event.preventDefault();
            setMobile(false);
        }
        if (event.key === 'Tab') {
            const controls = [...document.querySelectorAll('#sidebar button,#sidebar a')]
                .filter((node) => !node.closest('[hidden]') && node.getClientRects().length);
            if (event.shiftKey && document.activeElement === controls[0]) {
                event.preventDefault();
                controls.at(-1)?.focus();
            } else if (!event.shiftKey && document.activeElement === controls.at(-1)) {
                event.preventDefault();
                controls[0]?.focus();
            }
        }
    });

    syncInert();
}

function initLogout() {
    const dialog = $('#logout-dialog');
    if (!dialog) return;
    $('#logout-button').addEventListener('click', () => dialog.showModal());
    $('#cancel-logout').addEventListener('click', () => dialog.close());
    dialog.addEventListener('close', () => $('#logout-button').focus());
    dialog.querySelector('form').addEventListener('submit', () => {
        $('#confirm-logout').disabled = true;
    });
}

export function initShell() {
    initTheme();
    initNavGroups();
    initSidebar();
    initLogout();
}
