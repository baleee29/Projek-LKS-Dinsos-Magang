"use strict";
// jsPDF and the embedded report fonts are only needed for PDF exports, so they load on first use
// instead of with every dashboard. load-features.js publishes their URLs in SidayaDashboardAssets.
window.SidayaPdf = (() => {
    let loading;
    const urls = () => window.SidayaDashboardAssets?.pdf || [];
    function script(src) {
        return new Promise((resolve, reject) => {
            const node = document.createElement("script");
            node.async = false;
            node.src = src;
            node.onload = resolve;
            node.onerror = () => {
                node.remove();
                reject(new Error("Pustaka PDF tidak dapat dimuat. Periksa koneksi, lalu coba lagi."));
            };
            document.head.append(node);
        });
    }
    function ready() {
        if (window.jspdf?.jsPDF && window.SidayaReportFonts) return Promise.resolve();
        // async = false keeps the fonts after jsPDF; a failed load can be retried by the next export.
        loading ??= Promise.all(urls().map(script)).catch((error) => {
            loading = null;
            throw error;
        });
        return loading;
    }
    return { ready };
})();
