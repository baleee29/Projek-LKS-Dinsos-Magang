// Toast tunggal, sama seperti notify() di shared/workspace.js sumber.
let timer;

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
