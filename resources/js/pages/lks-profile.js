// Tombol GPS dan hapus logo pada dialog Edit Profil, dari shared/workspace-profile.js.
// Pratinjau logo ditangani dashboard/forms.js lewat data-preview.
import { notify } from '../dashboard/toast.js';

const dialog = document.querySelector('#edit-profile-dialog');

if (dialog) {
    const detect = dialog.querySelector('#btn-detect-gps');
    const fileInput = dialog.querySelector('#org-logo-file-input');
    const preview = dialog.querySelector('#org-logo-preview-box');
    const remove = dialog.querySelector('#btn-remove-org-logo');
    const status = dialog.querySelector('#org-logo-status-text');

    detect?.addEventListener('click', () => {
        if (!navigator.geolocation) {
            notify('Browser Anda tidak mendukung deteksi lokasi otomatis.');
            return;
        }
        const label = detect.textContent;
        detect.disabled = true;
        detect.textContent = 'Mendeteksi...';
        navigator.geolocation.getCurrentPosition((position) => {
            detect.disabled = false;
            detect.textContent = label;
            dialog.querySelector('[name="latitude"]').value = position.coords.latitude.toFixed(6);
            dialog.querySelector('[name="longitude"]').value = position.coords.longitude.toFixed(6);
            // Isian hanya terisi di form; belum ada yang tersimpan.
            notify('Titik koordinat terisi di formulir.');
        }, (error) => {
            detect.disabled = false;
            detect.textContent = label;
            notify(`Gagal mendeteksi lokasi GPS: ${error.message || 'Izin lokasi tidak diberikan.'}`);
        }, { enableHighAccuracy: true, timeout: 10000 });
    });

    fileInput?.addEventListener('change', () => {
        remove.style.display = fileInput.files?.length ? 'inline-block' : 'none';
    });
    remove?.addEventListener('click', () => {
        fileInput.value = '';
        preview.textContent = preview.dataset.placeholder;
        remove.style.display = 'none';
        status.textContent = 'Belum ada logo dipilih';
    });
}
