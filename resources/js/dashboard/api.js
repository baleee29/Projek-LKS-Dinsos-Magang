// Pengiriman ke server. Keberhasilan hanya ditampilkan setelah server mengonfirmasi.
const token = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

export class ApiError extends Error {
    constructor(message, status, errors = {}) {
        super(message);
        this.status = status;
        this.errors = errors;
    }
}

export async function send(url, { method = 'POST', body = null } = {}) {
    let payload = body;
    const headers = { Accept: 'application/json', 'X-CSRF-TOKEN': token(), 'X-Requested-With': 'XMLHttpRequest' };
    // PHP hanya mengurai multipart pada POST, jadi metode lain dikirim lewat _method.
    let realMethod = method;
    if (method !== 'GET' && method !== 'POST') {
        payload = body instanceof FormData ? body : new FormData();
        payload.set('_method', method);
        realMethod = 'POST';
    }
    let response;
    try {
        response = await fetch(url, { method: realMethod, headers, body: payload, credentials: 'same-origin' });
    } catch {
        throw new ApiError('Tidak dapat terhubung ke server. Periksa koneksi, lalu coba lagi.', 0);
    }
    const data = await response.json().catch(() => ({}));
    if (!response.ok) {
        const fallback = response.status === 419 ? 'Sesi halaman berakhir. Muat ulang halaman, lalu coba lagi.'
            : response.status === 413 ? 'Ukuran berkas terlalu besar untuk dikirim.'
            : response.status === 429 ? 'Terlalu banyak percobaan. Tunggu sebentar, lalu coba lagi.'
            : 'Permintaan tidak dapat diproses. Coba lagi.';
        throw new ApiError(response.status === 422 ? firstError(data) || fallback : data.message || fallback, response.status, data.errors || {});
    }
    return data;
}

const firstError = (data) => Object.values(data.errors || {})[0]?.[0] || data.message;

/** Setelah berhasil: pindah halaman bila diminta, selain itu muat ulang agar data terbaru tampil. */
export function afterSuccess(data) {
    if (data.redirect) window.location.assign(data.redirect);
    else window.location.reload();
}
