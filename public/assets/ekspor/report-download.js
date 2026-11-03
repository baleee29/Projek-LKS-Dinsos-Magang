"use strict";

// Admin-only PDF export. Uses the same report schema and submission snapshot as the dashboard.
window.SidayaReportPDF = (() => {
    const labels = { psm: "PSM", lks: "LKS", "karang-taruna": "Karang Taruna" };
    const clean = (value) =>
        String(value ?? "")
            .replace(/[\u0000-\u0008\u000b\u000c\u000e-\u001f]/g, "")
            .replace(/[\u2010-\u2015]/g, "-")
            .replace(/\u00a0/g, " ");
    const date = (value) => {
        if (!value) return "Belum dicatat";
        const parsed = new Date(
            value.length === 10 ? value + "T12:00:00+08:00" : value,
        );
        return Number.isNaN(parsed.getTime())
            ? clean(value)
            : new Intl.DateTimeFormat("id-ID", {
                  dateStyle: "long",
                  timeZone: "Asia/Makassar",
              }).format(parsed);
    };
    const number = (value) =>
        new Intl.NumberFormat("id-ID").format(Number(value) || 0);
    const money = (value) => "Rp " + number(value);
    const valueOf = (field, value) => {
        if (field.type === "checkbox") return value ? "Ya" : "Tidak";
        if (
            value === undefined ||
            value === null ||
            String(value).trim() === ""
        )
            return "Tidak diisi / tidak relevan";
        if (field.type === "date") return date(value);
        if (field.type === "number")
            return /\(Rp\)/.test(field.label) ? money(value) : number(value);
        return clean(value);
    };
    const slug = (value) =>
        clean(value)
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, "-")
            .replace(/^-|-$/g, "")
            .slice(0, 70) || "mitra";

    function create(id, role) {
        if (role !== "admin")
            throw new Error(
                "Unduhan laporan terkirim tersedia pada dashboard Admin.",
            );
        const store = window.SidayaQuarterly;
        const report = store?.report(id);
        if (!report || report.status === "Draf")
            throw new Error(
                "Laporan dapat diunduh setelah dikirim oleh LKS.",
            );
        if (!window.jspdf?.jsPDF || !window.SidayaReportFonts)
            throw new Error(
                "Pembuat PDF belum tersedia. Muat ulang halaman lalu coba kembali.",
            );
        const org =
            report.organizationSnapshot ||
            store.organization(report.organizationId);
        if (!org)
            throw new Error("Identitas organisasi laporan tidak tersedia.");
        const isSemester = report.pillar === "lks" || report.period_type === "semester";
        const period = store.period(report.year, report.quarter, report.pillar);
        const pdf = new window.jspdf.jsPDF({
            unit: "mm",
            format: "a4",
            compress: true,
            putOnlyUsedFonts: true,
        });
        for (const [style, data] of Object.entries(window.SidayaReportFonts)) {
            pdf.addFileToVFS("Sidaya-" + style + ".ttf", data);
            pdf.addFont("Sidaya-" + style + ".ttf", "Sidaya", style);
        }
        pdf.setProperties({
            title: "Laporan " + period.label + " - " + org.title,
            subject: isSemester ? "Laporan semester resmi SiDaya" : "Laporan triwulan resmi SiDaya",
            author: "SiDaya - Dinas Sosial Kota Banjarbaru",
            creator: "SiDaya Admin",
        });
        const left = 17,
            width = 176,
            bottom = 275;
        let y = 24;
        const font = (size = 9, style = "normal", color = [38, 44, 53]) => {
            pdf.setFont("Sidaya", style);
            pdf.setFontSize(size);
            pdf.setTextColor(...color);
        };
        const pageHeader = () => {
            font(8, "bold", [72, 98, 132]);
            pdf.text("SiDaya  |  DINAS SOSIAL KOTA BANJARBARU", left, 12);
            pdf.setDrawColor(213, 220, 230);
            pdf.setLineWidth(0.25);
            pdf.line(left, 16, left + width, 16);
            y = 24;
        };
        const room = (height) => {
            if (y + height > bottom) {
                pdf.addPage();
                pageHeader();
            }
        };
        const paragraph = (
            value,
            size = 9,
            style = "normal",
            color,
            lineHeight = 4.6,
        ) => {
            font(size, style, color);
            const lines = pdf.splitTextToSize(clean(value) || "-", width);
            for (const line of lines) {
                room(lineHeight);
                font(size, style, color);
                pdf.text(line, left, y);
                y += lineHeight;
            }
        };
        const section = (title, contentHeight = 14) => {
            room(12 + contentHeight);
            y += 4;
            font(12, "bold", [72, 98, 132]);
            const lines = pdf.splitTextToSize(clean(title), width);
            for (const line of lines) {
                room(6);
                pdf.text(line, left, y);
                y += 6;
            }
            y += 2;
        };
        const field = (label, value) => {
            room(13);
            paragraph(label, 8, "bold", [93, 103, 116], 4.2);
            paragraph(value || "Belum dicatat");
            y += 2.5;
        };
        const canPair = (entry, next) =>
            next &&
            !entry[2] &&
            !next[2] &&
            clean(entry[1]).length <= 100 &&
            clean(next[1]).length <= 100;
        const measureField = (entry, fieldWidth = width) => {
            font(8, "bold");
            const labelLines = pdf.splitTextToSize(clean(entry[0]), fieldWidth);
            font();
            const valueLines = pdf.splitTextToSize(
                clean(entry[1]) || "Belum dicatat",
                fieldWidth,
            );
            return {
                labelLines,
                valueLines,
                height: labelLines.length * 4.2 + valueLines.length * 4.6 + 3,
            };
        };
        const entriesHeight = (entries) => {
            let height = 0;
            for (let index = 0; index < entries.length; index++) {
                if (canPair(entries[index], entries[index + 1])) {
                    height += Math.max(
                        measureField(entries[index], (width - 10) / 2).height,
                        measureField(entries[index + 1], (width - 10) / 2)
                            .height,
                    );
                    index++;
                } else height += measureField(entries[index]).height;
            }
            return height;
        };
        const pairs = (entries) => {
            for (let index = 0; index < entries.length; index++) {
                const entry = entries[index],
                    next = entries[index + 1];
                if (!canPair(entry, next)) {
                    field(entry[0], entry[1]);
                    continue;
                }
                const columnWidth = (width - 10) / 2;
                const blocks = [entry, next].map((item) =>
                    measureField(item, columnWidth),
                );
                const height = Math.max(...blocks.map((block) => block.height));
                room(height);
                blocks.forEach((block, column) => {
                    const x = left + column * (columnWidth + 10);
                    let baseline = y;
                    font(8, "bold", [93, 103, 116]);
                    block.labelLines.forEach((line) => {
                        pdf.text(line, x, baseline);
                        baseline += 4.2;
                    });
                    font();
                    block.valueLines.forEach((line) => {
                        pdf.text(line, x, baseline);
                        baseline += 4.6;
                    });
                });
                y += height;
                index++;
            }
        };
        const fieldEntries = (fields, values) =>
            fields.map((item) => [
                item.label,
                valueOf(item, values?.[item.key]),
                item.type === "textarea",
            ]);
        const schemaFields = (fields, values) =>
            pairs(fieldEntries(fields, values));
        const rowHeight = (fields, values) => {
            const height = entriesHeight(fieldEntries(fields, values)) + 10;
            return height <= 160 ? height : 24;
        };
        pageHeader();
        paragraph(isSemester ? "Laporan Semester LKS" : "Laporan Triwulan", 20, "bold", [38, 44, 53], 8);
        paragraph(
            period.label + " | " + labels[report.pillar],
            11,
            "bold",
            [72, 98, 132],
            6,
        );
        paragraph(org.title, 11, "normal", [38, 44, 53], 5.5);
        y += 3;
        paragraph(
            "LAPORAN TERKIRIM - DINAS SOSIAL KOTA BANJARBARU",
            9,
            "bold",
            [53, 108, 83],
        );
        paragraph(
            isSemester
                ? "Laporan semester pilar sosial LKS diterima melalui Sistem SiDaya Kota Banjarbaru."
                : "Laporan triwulan pilar sosial terverifikasi melalui Sistem SiDaya Kota Banjarbaru.",
            8,
            "normal",
            [93, 103, 116],
            4.2,
        );

        section("Identitas organisasi dan periode");
        pairs([
            ["ID laporan", report.id],
            ["Jenis mitra", labels[report.pillar]],
            ["Nama organisasi / lembaga", org.title, true],
            ["Penanggung jawab", org.chair],
            ["Periode laporan", date(period.start) + " - " + date(period.end)],
            ["Kecamatan", org.district],
            ["Kelurahan", org.village],
            ["Alamat sekretariat", org.address, true],
            ["Kontak organisasi", org.contact],
            ["Pengiriman terakhir", date(report.submittedAt)],
        ]);
        if (report.pillar === "lks")
            field(
                "Legalitas lembaga",
                // typeof null is "object": an LKS without legal data prints an empty field.
                org.legal && typeof org.legal === "object"
                    ? Object.entries(org.legal)
                          .map(([key, value]) => key + ": " + value)
                          .join("\n")
                    : org.legal,
            );
        paragraph(
            "Identitas mengikuti salinan profil saat laporan dikirim.",
            8,
            "normal",
            [93, 103, 116],
            4.2,
        );

        if (isSemester) {
            section("Informasi Laporan");
            field("Judul / Perihal Laporan", report.title || "—");
            field("Jumlah Berkas Terlampir", `${(report.attachments || []).length} berkas`);
        } else {
            section("Ringkasan pelaksanaan");
            paragraph(report.summary || "Belum diisi");
            if (report.noActivity) {
                y += 2;
                field(
                    "Tidak ada kegiatan / layanan pada periode ini",
                    report.noActivityReason || "Alasan belum diisi",
                );
            }
            section("Ringkasan angka");
            const totals = store.totals(report);
            const metrics = [
                ["Rincian kegiatan / layanan", number(totals.activityCount)],
            ];
        if (report.pillar === "psm")
            metrics.push(
                [
                    "Warga unik yang dijangkau / didampingi",
                    number(totals.peopleUnique),
                ],
                ["Keluarga unik", number(report.fields.uniqueFamilies)],
            );
        else if (report.pillar === "lks")
            metrics.push([
                "Penerima manfaat unik seluruh layanan",
                number(totals.peopleUnique),
            ]);
        else {
            metrics.push([
                "Kehadiran peserta (dapat berulang)",
                number(totals.participantAttendances),
            ]);
            if (
                report.fields.uniqueParticipants !== "" &&
                report.fields.uniqueParticipants != null
            )
                metrics.push([
                    "Peserta unik lintas kegiatan",
                    number(totals.peopleUnique),
                ]);
        }
        if (store.schema(report.pillar).some((item) => item.key === "finance"))
            metrics.push(
                ["Saldo awal", money(totals.openingBalance)],
                ["Pemasukan", money(totals.income)],
                ["Pengeluaran", money(totals.expense)],
                ["Saldo akhir", money(totals.balance)],
            );
        pairs(metrics);
        paragraph(
            "Jumlah kunjungan dan kehadiran dapat mencakup orang yang sama. Jumlah orang unik dicatat terpisah.",
            8,
            "normal",
            [93, 103, 116],
            4.2,
        );

        for (const part of store.schema(report.pillar)) {
            font(8);
            const descriptionHeight = part.description
                ? pdf.splitTextToSize(clean(part.description), width).length *
                      4.2 +
                  2
                : 0;
            const firstRow = part.rows ? report.rows[part.rows.key]?.[0] : null;
            const contentHeight = part.fields?.length
                ? Math.min(
                      entriesHeight(fieldEntries(part.fields, report.fields)),
                      80,
                  )
                : firstRow
                  ? rowHeight(part.rows.fields, firstRow)
                  : 10;
            section(part.title, descriptionHeight + contentHeight);
            if (part.description) {
                paragraph(part.description, 8, "normal", [93, 103, 116], 4.2);
                y += 2;
            }
            if (part.fields?.length) schemaFields(part.fields, report.fields);
            if (part.rows) {
                const entries = report.rows[part.rows.key] || [];
                if (!entries.length)
                    paragraph(
                        "Tidak ada rincian pada bagian ini.",
                        9,
                        "normal",
                        [93, 103, 116],
                    );
                entries.forEach((entry, index) => {
                    room(rowHeight(part.rows.fields, entry));
                    y += 3;
                    paragraph(
                        part.rows.label + " " + (index + 1),
                        10,
                        "bold",
                        [72, 98, 132],
                        5,
                    );
                    schemaFields(part.rows.fields, entry);
                    pdf.setDrawColor(228, 232, 238);
                    pdf.line(left, y, left + width, y);
                    y += 2;
                });
            }
        }
        section("Evaluasi dan tindak lanjut");
        field("Hasil dan capaian utama", report.result);
        field(
            "Kendala dan kebutuhan dukungan",
            report.obstacles || "Tidak diisi / tidak relevan",
        );
        field("Tindak lanjut dan rencana triwulan berikutnya", report.followup);
        }
        section("Lampiran pendukung");
        if (!report.attachments?.length) paragraph("Tidak ada lampiran.");
        else {
            report.attachments.forEach((file, index) =>
                field(
                    "Lampiran " + (index + 1),
                    (file.name || "Dokumen") +
                        "\n" +
                        (file.type || "Dokumen") +
                        " | " +
                        number(Math.ceil((file.size || 0) / 1024)) +
                        " KB",
                ),
            );
            paragraph(
                "Daftar ini mencantumkan lampiran laporan. Berkas asli tersedia melalui tombol Lihat pada bagian lampiran di detail laporan dashboard admin.",
                8,
                "normal",
                [93, 103, 116],
                4.2,
            );
        }
        section("Informasi pengiriman");
        pairs([
            ["Status", report.status],
            ["Tanggal pengiriman", date(report.submittedAt)],
            ["Versi laporan", number(report.version)],
        ]);
        section("Riwayat status");
        (report.history || []).forEach((item, index) =>
            field(
                index + 1 + ". " + item.status + " | " + date(item.at),
                (item.actor || "Pengguna") +
                    (item.note ? " | " + item.note : ""),
            ),
        );
        const totalPages = pdf.getNumberOfPages();
        for (let page = 1; page <= totalPages; page++) {
            pdf.setPage(page);
            pdf.setDrawColor(213, 220, 230);
            pdf.line(left, 281, left + width, 281);
            font(7, "normal", [93, 103, 116]);
            pdf.text(
                (isSemester ? "LAPORAN SEMESTER | " : "LAPORAN TRIWULAN | ") + clean(report.id).slice(0, 80),
                left,
                286,
            );
            pdf.text(
                "Halaman " + page + " / " + totalPages,
                left + width,
                286,
                { align: "right" },
            );
        }
        return {
            blob: pdf.output("blob"),
            filename:
                "sidaya-" +
                report.pillar +
                "-" +
                slug(org.title) +
                "-" +
                report.year +
                (isSemester ? "-semester-" : "-triwulan-") +
                report.quarter +
                "-terkirim.pdf",
        };
    }

    function download(id, role) {
        const result = create(id, role);
        const url = URL.createObjectURL(result.blob);
        const anchor = document.createElement("a");
        anchor.href = url;
        anchor.download = result.filename;
        document.body.append(anchor);
        try {
            anchor.click();
        } finally {
            anchor.remove();
            setTimeout(() => URL.revokeObjectURL(url), 30000);
        }
        return result.filename;
    }
    return { create, download };
})();
