// Pilihan dan aturan isian dari shared/partner-registration-data.js sumber. Tidak membaca atau menulis data.
export const lksChoiceOptions = () => ({
            "lks-prioritas-pelayanan": [
                "Kemiskinan",
                "Keterlantaran",
                "Disabilitas",
                "Keterpencilan",
                "Ketunaan sosial dan penyimpangan perilaku",
                "Korban bencana",
                "Korban tindak kekerasan, eksploitasi, dan diskriminasi",
            ],
            "lks-jenis-pelayanan": [
                "Jaminan Sosial",
                "Rehabilitasi Sosial",
                "Pemberdayaan Sosial",
                "Perlindungan Sosial",
            ],
            "lks-sasaran-pelayanan": [
                "Perseorangan",
                "Keluarga",
                "Kelompok",
                "Masyarakat",
            ],
            "lks-jenis-kegiatan": [
                "Unit pelayanan sosial untuk anak",
                "Unit pelayanan sosial untuk lansia",
                "Unit pelayanan sosial untuk penyandang disabilitas",
                "Dukungan psikososial untuk anak dalam situasi bencana",
                "Pemberdayaan ekonomi kelompok miskin dan rentan",
            ],
            "lks-sifat-pelayanan": ["Dalam LKS", "Luar LKS"],
});

export const isWhatsAppNumber = (value) => /^08[0-9]{8,12}$/.test(
                String(value)
                    .replace(/[\s()-]/g, "")
                    .replace(/^\+?62/, "0"),
            );
