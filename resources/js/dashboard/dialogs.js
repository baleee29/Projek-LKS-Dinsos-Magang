// Dialog konfirmasi dari shared/ui-helpers.js, serta pembuka <dialog> yang sudah dirender Blade.
import { DEMO_DOWNLOAD_MESSAGE, DEMO_SAVE_MESSAGE, notify } from './toast.js';

let sequence = 0;

/** Modal pengganti window.confirm. Teksnya dibangun lewat textContent, bukan innerHTML. */
export function confirmDialog({ title = 'Konfirmasi', message = '', confirmText = 'Ya, lanjutkan', cancelText = 'Batal', danger = true }) {
    return new Promise((resolve) => {
        const trigger = document.activeElement;
        const id = `ui-dialog-title-${++sequence}`;
        const el = document.createElement('dialog');
        el.className = 'ui-dialog';
        el.setAttribute('aria-labelledby', id);

        const heading = document.createElement('h2');
        heading.id = id;
        heading.textContent = title;
        const body = document.createElement('p');
        body.textContent = message;
        const actions = document.createElement('div');
        actions.className = 'ui-dialog-actions';

        const button = (text, className) => {
            const node = document.createElement('button');
            node.type = 'button';
            node.className = `ui-dialog-button ${className}`.trim();
            node.textContent = text;
            actions.append(node);
            return node;
        };
        const cancel = cancelText ? button(cancelText, '') : null;
        const accept = button(confirmText, danger ? 'is-danger' : 'is-primary');

        let accepted = false;
        accept.addEventListener('click', () => {
            accepted = true;
            el.close();
        });
        cancel?.addEventListener('click', () => el.close());
        el.addEventListener('click', (event) => {
            if (event.target === el) el.close();
        });
        el.addEventListener('close', () => {
            el.remove();
            if (trigger?.isConnected) trigger.focus();
            resolve(accepted);
        }, { once: true });

        el.append(heading, body, actions);
        document.body.append(el);
        el.showModal();
        (cancel || accept).focus();
    });
}

/**
 * Perilaku bersama untuk aksi dummy:
 * - [data-demo-action]: konfirmasi opsional (data-confirm, data-confirm-title, data-confirm-text,
 *   data-confirm-safe), lalu toast netral.
 * - [data-demo-download]: toast unduhan.
 * - [data-open-dialog="id"] dan [data-close-dialog]: membuka dan menutup <dialog>.
 * Tidak ada permintaan ke server. TODO(Tahap 2): ganti tiap aksi dengan endpoint sumber.
 */
export function initDialogs() {
    let lastTrigger = null;

    document.addEventListener('click', async (event) => {
        const download = event.target.closest('[data-demo-download]');
        if (download) {
            event.preventDefault();
            notify(DEMO_DOWNLOAD_MESSAGE);
            return;
        }

        const opener = event.target.closest('[data-open-dialog]');
        if (opener) {
            event.preventDefault();
            const dialog = document.getElementById(opener.dataset.openDialog);
            if (dialog) {
                lastTrigger = opener;
                dialog.showModal();
                dialog.dispatchEvent(new CustomEvent('demo:open', { detail: { trigger: opener } }));
            }
            return;
        }

        const closer = event.target.closest('[data-close-dialog]');
        if (closer) {
            event.preventDefault();
            closer.closest('dialog')?.close();
            return;
        }

        const action = event.target.closest('button[data-demo-action], a[data-demo-action]');
        if (action) {
            event.preventDefault();
            if (action.disabled) return;
            await runDemoAction(action);
        }
    });

    document.querySelectorAll('dialog').forEach((dialog) => {
        dialog.addEventListener('close', () => {
            if (lastTrigger?.isConnected && !document.querySelector('dialog[open]')) lastTrigger.focus();
        });
    });
}

export async function runDemoAction(node) {
    const { confirm: message, confirmTitle, confirmText, confirmSafe } = node.dataset;
    if (message) {
        const accepted = await confirmDialog({
            title: confirmTitle || 'Konfirmasi',
            message,
            confirmText: confirmText || 'Ya, lanjutkan',
            danger: confirmSafe === undefined,
        });
        if (!accepted) return false;
    }
    node.closest('dialog')?.close();
    notify(DEMO_SAVE_MESSAGE);
    return true;
}
