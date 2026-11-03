// Ekspor PDF dan Excel memakai modul sumber di public/assets/ekspor, dimuat saat pertama kali dipakai.
// Server mengirim data sesuai filter halaman; berkas dibuat di browser.
import { send } from './api.js';
import { notify } from './toast.js';

const ASET = '/assets/ekspor/';
const MODUL = ['xlsx-writer', 'pdf-loader', 'partner-export-common', 'partner-export-lks', 'resident-export', 'staff-export', 'reports-export', 'letters-export', 'report-recap-export', 'report-download'];
let loading;

const tanggal = (value) => {
    if (!value) return 'Belum dicatat';
    const parsed = new Date(String(value).length === 10 ? `${value}T12:00:00+08:00` : value);
    return Number.isFinite(parsed.getTime())
        ? new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeZone: 'Asia/Makassar' }).format(parsed)
        : 'Belum dicatat';
};

const periode = (year, quarter) => {
    const semester = Number(String(quarter || '').replace(/[^0-9]/g, '')) >= 2 ? 2 : 1;
    return {
        start: `${year}-${semester === 1 ? '01-01' : '07-01'}`,
        end: `${year}-${semester === 1 ? '06-30' : '12-31'}`,
        label: `Semester ${semester} \u00b7 ${year}`,
    };
};

function muatSkrip(src) {
    return new Promise((resolve, reject) => {
        const node = document.createElement('script');
        node.async = false;
        node.src = src;
        node.onload = resolve;
        node.onerror = () => {
            node.remove();
            reject(new Error('Modul ekspor tidak dapat dimuat. Periksa koneksi, lalu coba lagi.'));
        };
        document.head.append(node);
    });
}

function muatModul() {
    // Fungsi yang di sumber disediakan aplikasi lama.
    window.SidayaDashboardAssets = { pdf: ['/assets/vendor/jspdf/jspdf.umd.min.js', `${ASET}report-fonts.js`] };
    window.SidayaLKSUI = { date: tanggal };
    loading ??= Promise.all(MODUL.map((nama) => muatSkrip(`${ASET}${nama}.js`))).catch((error) => {
        loading = null;
        throw error;
    });
    return loading;
}

function simpan(blob, filename) {
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    document.body.append(link);
    link.click();
    link.remove();
    setTimeout(() => URL.revokeObjectURL(url), 2000);
}

const PENYUSUN = {
    klien: (format, data) => window.SidayaLKSResidentExport.download(format, data.items, data.organizations, data.today),
    pengurus: (format, data) => window.SidayaLKSStaffExport.download(format, data.members, data.org, data.today),
    lembaga: (format, data) => {
        if (!data.items.length) throw new Error('Tidak ada data LKS yang dapat diunduh.');
        window.SidayaAdminPartnerExportLks.exportLks(format, data.items, data.members);
    },
    laporanAdmin: (format, data) => window.SidayaReportRecapExport.download(format, data.reports, data.context, {
        role: 'admin',
        labels: { lks: 'LKS' },
        today: data.today,
    }),
    laporanLks: (format, data) => {
        if (!data.items.length) throw new Error('Belum ada laporan sesuai filter untuk diunduh.');
        const { buildReportPdf, buildReportXlsx } = window.SidayaLKSReportExport;
        const pembuat = format === 'pdf' ? buildReportPdf : buildReportXlsx;
        const tahun = data.state.year ? `_${data.state.year}` : '_semua-tahun';
        simpan(pembuat(data.items, data.organization, data.state, { period: periode }), `Daftar_Laporan_Semester_LKS${tahun}_${data.today}.${format}`);
    },
    laporanDetail: (format, data) => {
        window.SidayaQuarterly = {
            report: () => data.report,
            organization: () => data.report.organizationSnapshot,
            period: periode,
            totals: () => ({}),
            schema: () => [],
        };
        window.SidayaReportPDF.download(data.report.id, 'admin');
    },
    surat: (format, data) => {
        if (!data.items.length) throw new Error('Tidak ada surat sesuai filter untuk diunduh.');
        const { formatDate, buildLksLettersPdf, buildLksLettersXlsx } = window.SidayaLKSLettersExport;
        const pembuat = format === 'pdf' ? buildLksLettersPdf : buildLksLettersXlsx;
        const blob = pembuat(data.items, data.isOutgoing, data.periodText, formatDate(data.today), data.adminMode);
        simpan(blob, `${data.prefix}${data.periodSlug}_${data.today}.${format}`);
    },
};

export async function runExport(button) {
    const format = button.dataset.exportFormat;
    const url = new URL(button.dataset.exportUrl, window.location.origin);
    new URLSearchParams(window.location.search).forEach((value, key) => {
        if (key !== 'page' && !url.searchParams.has(key)) url.searchParams.set(key, value);
    });
    button.disabled = true;
    try {
        const data = await send(url.toString(), { method: 'GET' });
        await muatModul();
        if (format === 'pdf') await window.SidayaPdf.ready();
        await PENYUSUN[data.kind](format, data);
    } catch (error) {
        notify(error.message || 'Berkas gagal dibuat.');
    } finally {
        button.disabled = false;
    }
}
