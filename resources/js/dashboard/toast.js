// Toast tunggal, sama seperti notify() di shared/workspace.js sumber.
let timer;

export const DEMO_SAVE_MESSAGE = 'Mode demo: perubahan belum disimpan.';
export const DEMO_DOWNLOAD_MESSAGE = 'Unduhan tersedia setelah backend terhubung.';

export function notify(message) {
    const toast = document.querySelector('#app-toast');
    if (!toast) return;
    toast.textContent = message;
    toast.hidden = false;
    clearTimeout(timer);
    timer = setTimeout(() => {
        toast.hidden = true;
    }, 6500);
}
