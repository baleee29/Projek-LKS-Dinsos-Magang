"use strict";

// Required documents of the public LKS registration form, their PDF preview, and the sample PDF used by the demo fill.
window.SidayaLKSRegistrationDocuments = (() => {
    // Limits and input IDs follow the page contract, not arbitrary data attributes.
    const definitions = [
        {
            key: "surat_permohonan",
            label: "Surat Permohonan Pendaftaran LKS",
            maxMb: 5,
            exampleTitle: "SURAT PERMOHONAN PENDAFTARAN LKS",
        },
        {
            key: "ad_art",
            label: "Anggaran Dasar dan Anggaran Rumah Tangga (AD/ART)",
            maxMb: 2,
            exampleTitle: "AD ART LKS",
        },
        {
            key: "akta_pendirian",
            label: "Akta Pendirian",
            maxMb: 2,
            exampleTitle: "AKTA PENDIRIAN",
        },
        {
            key: "domisili",
            label: "Surat Keterangan Domisili dari Lurah/Kepala Desa Setempat atau Nama Lain",
            maxMb: 2,
            exampleTitle: "SURAT DOMISILI",
        },
        {
            key: "npwp",
            label: "Nomor Pokok Wajib Pajak (NPWP) Badan Hukum LKS",
            maxMb: 2,
            exampleTitle: "NPWP BADAN HUKUM",
        },
        {
            key: "kemenkumham",
            label: "Pengesahan Pendirian Badan Hukum oleh Kementerian yang Menyelenggarakan Urusan Pemerintahan di Bidang Hukum",
            maxMb: 2,
            exampleTitle: "PENGESAHAN KEMENKUMHAM",
        },
        {
            key: "struktur_organisasi",
            label: "Struktur Organisasi LKS",
            maxMb: 2,
            exampleTitle: "STRUKTUR ORGANISASI",
        },
        {
            key: "data_umum",
            label: "DATA UMUM LKS : Alamat, Nomor Telepon, Website, dan Media Sosial LKS",
            maxMb: 2,
            exampleTitle: "DATA UMUM LKS",
        },
        {
            key: "ktp_kontak_pengurus",
            label: "Kartu Tanda Penduduk Pengurus dan Nomor Telepon Pengurus LKS",
            maxMb: 2,
            exampleTitle: "KTP DAN KONTAK PENGURUS",
        },
        {
            key: "bebas_narkoba",
            label: "Surat Keterangan Bebas dari Narkoba bagi Pengurus LKS",
            maxMb: 2,
            exampleTitle: "SURAT BEBAS NARKOBA",
        },
        {
            key: "pernyataan_sengketa",
            label: "Surat Pernyataan Tidak dalam Sengketa Kepengurusan atau Tidak dalam Perkara di Pengadilan",
            maxMb: 2,
            exampleTitle: "PERNYATAAN BEBAS SENGKETA",
        },
        {
            key: "kesanggupan_laporan",
            label: "Surat Pernyataan Kesanggupan Melaporkan Kegiatan",
            maxMb: 2,
            exampleTitle: "KESANGGUPAN LAPORAN",
        },
        {
            key: "keabsahan_dana",
            label: "Surat Pernyataan bahwa Sumber Pendanaan Tidak Berasal dari Kegiatan yang Melawan Hukum dan Tidak Digunakan untuk Kegiatan yang Melawan Hukum",
            maxMb: 2,
            exampleTitle: "SUMBER PENDANAAN",
        },
        {
            key: "persetujuan_tetangga",
            label: "Surat Pernyataan Persetujuan Tetangga",
            maxMb: 2,
            exampleTitle: "PERSETUJUAN TETANGGA",
        },
        {
            key: "proposal",
            label: "Proposal yang Memuat (Program dan Kegiatan LKS, Modal Kerja untuk Pelaksanaan Kegiatan, Nomor Rekening LKS, Sumber Daya Manusia dan Kelengkapan Sarana dan Prasarana)",
            maxMb: 5,
            exampleTitle: "PROPOSAL PROGRAM LKS",
        },
        {
            key: "formulir_pendaftaran",
            label: "FORMULIR PENDAFTARAN LKS",
            maxMb: 5,
            exampleTitle: "FORMULIR PENDAFTARAN",
        },
    ];
    function pdfPreview(container, url, title, filename) {
        const frame = document.createElement("iframe");
        frame.src = url;
        frame.title = title;
        const fallback = document.createElement("a");
        fallback.href = url;
        fallback.download = filename;
        fallback.textContent = "Unduh berkas jika pratinjau PDF tidak tersedia";
        container.append(frame, fallback);
    }
    function examplePdf(attachment) {
        const stream =
            "BT /F1 14 Tf 50 750 Td (" +
            attachment.exampleTitle +
            " - CONTOH DEMO) Tj " +
            "0 -30 Td (DOKUMEN FIKTIF UNTUK SIMULASI) Tj " +
            "/F1 11 Tf 0 -26 Td (Bukan dokumen legalitas atau izin resmi.) Tj ET";
        const objects = [
            "<< /Type /Catalog /Pages 2 0 R >>",
            "<< /Type /Pages /Kids [3 0 R] /Count 1 >>",
            "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>",
            "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>",
            "<< /Length " +
                stream.length +
                " >>\nstream\n" +
                stream +
                "\nendstream",
        ];
        let pdf = "%PDF-1.4\n";
        const offsets = [];
        objects.forEach((object, index) => {
            offsets.push(pdf.length);
            pdf += index + 1 + " 0 obj\n" + object + "\nendobj\n";
        });
        const xref = pdf.length;
        pdf +=
            "xref\n0 6\n0000000000 65535 f \n" +
            offsets
                .map(
                    (offset) =>
                        String(offset).padStart(10, "0") + " 00000 n \n",
                )
                .join("");
        pdf +=
            "trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n" +
            xref +
            "\n%%EOF";
        return new File([pdf], attachment.key + "_Dokumen_Contoh.pdf", {
            type: "application/pdf",
        });
    }

    return { definitions, pdfPreview, examplePdf };
})();
