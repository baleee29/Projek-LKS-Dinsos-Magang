"use strict";

// Excel and PDF recap of LKS semester reports for the Admin report list.
window.SidayaReportRecapExport = (() => {
    const text = (value) => String(value ?? "");
    const xml = window.SidayaXlsx.xml;

    const cleanPdfText = (value) =>
        String(value ?? "")
            .replace(/[\u0000-\u0008\u000b\u000c\u000e-\u001f]/g, "")
            .replace(/[\u2010-\u2015]/g, "-")
            .replace(/\u00a0/g, " ")
            .trim();

    const months = [
        "Januari", "Februari", "Maret", "April", "Mei", "Juni",
        "Juli", "Agustus", "September", "Oktober", "November", "Desember",
    ];

    function indonesianDate(value) {
        if (!value) return "-";
        const clean = text(value).trim();
        const match = /^(\d{4})-(\d{2})-(\d{2})/.exec(clean);
        if (!match) return clean;
        const [, year, month, day] = match;
        const monthNum = Number(month);
        if (monthNum >= 1 && monthNum <= 12) {
            return `${Number(day)} ${months[monthNum - 1]} ${year}`;
        }
        return clean;
    }

    const zipBlob = (parts) => window.SidayaXlsx.zip(parts, "Ukuran ekspor terlalu besar.");

    const reportStylesXml = `<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="3">
    <font><sz val="11"/><name val="Calibri"/></font>
    <font><b/><sz val="11"/><name val="Calibri"/></font>
    <font><b/><sz val="13"/><name val="Calibri"/></font>
  </fonts>
  <fills count="3">
    <fill><patternFill patternType="none"/></fill>
    <fill><patternFill patternType="gray125"/></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FFE2E8F0"/></patternFill></fill>
  </fills>
  <borders count="2">
    <border><left/><right/><top/><bottom/></border>
    <border>
      <left style="thin"><color rgb="FFCBD5E1"/></left>
      <right style="thin"><color rgb="FFCBD5E1"/></right>
      <top style="thin"><color rgb="FFCBD5E1"/></top>
      <bottom style="thin"><color rgb="FFCBD5E1"/></bottom>
    </border>
  </borders>
  <cellStyleXfs count="1">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
  </cellStyleXfs>
  <cellXfs count="6">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
    <xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0"/>
    <xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0"/>
    <xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>
    <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
  </cellXfs>
</styleSheet>`;

    function buildReportsXlsx(reports, context, meta = {}) {
        const isSemester = reports.some(r => r.pillar === "lks" || r.period_type === "semester");
        const docTitle = isSemester ? "REKAPITULASI LAPORAN SEMESTER LKS" : "REKAPITULASI LAPORAN TRIWULAN";
        const sheetName = isSemester ? "Laporan Semester LKS" : "Rekap Laporan";
        const todayFormatted = indonesianDate(meta.today || new Date().toISOString().slice(0, 10));

        const headers = [
            "NO", "NAMA MITRA / LEMBAGA", "JENIS MITRA", "KELURAHAN", "KECAMATAN",
            "PERIODE", "JUDUL LAPORAN", "BERKAS TERLAMPIR", "TANGGAL PENGAJUAN", "STATUS"
        ];
        const columnWidths = [6, 32, 16, 20, 22, 26, 36, 18, 22, 18];
        const colsXml = `<cols>${columnWidths.map((w, i) => `<col min="${i + 1}" max="${i + 1}" width="${w}" customWidth="1"/>`).join("")}</cols>`;

        const labels = meta.labels || { lks: "LKS", psm: "PSM", "karang-taruna": "Karang Taruna" };
        const store = meta.store || window.SidayaQuarterly;

        const dataRows = reports.map((r, index) => {
            const org = r.organizationSnapshot || store?.organization?.(r.organizationId) || {};
            const orgTitle = (meta.organizationTitle ? meta.organizationTitle(r) : null) || org.title || r.organizationId || "Mitra";
            const pillar = labels[r.pillar] || "LKS";
            const periodLabel = r.periodLabel || (isSemester
                ? `Semester ${r.quarter} - ${r.year}`
                : `Triwulan ${r.quarter} - ${r.year}`);
            const filesCount = `${(r.attachments || []).length} berkas`;
            const submitDate = r.submittedAt ? indonesianDate(r.submittedAt) : "Belum diajukan";
            const status = r.status || "Draf";

            return [
                index + 1,
                orgTitle,
                pillar,
                org.village || "-",
                org.district || "-",
                periodLabel,
                r.title || "-",
                filesCount,
                submitDate,
                status
            ];
        });

        const periodFilter = context.year && context.quarter
            ? (context.quarter === "all" ? `Tahun ${context.year}` : (isSemester ? `Semester ${context.quarter} - ${context.year}` : `Triwulan ${context.quarter} - ${context.year}`))
            : "Semua Periode";
        const filterDesc = [
            `Periode: ${periodFilter}`,
            context.status && context.status !== "all" ? `Status: ${context.status}` : "",
            context.district ? `Kecamatan: ${context.district}` : ""
        ].filter(Boolean).join(" | ");

        const xmlRows = [];
        xmlRows.push(`<row r="1" ht="26" customHeight="1"><c r="A1" s="2" t="inlineStr"><is><t>${xml(docTitle)}</t></is></c></row>`);
        xmlRows.push('<row r="2" ht="20" customHeight="1"><c r="A2" s="1" t="inlineStr"><is><t>DINAS SOSIAL KOTA BANJARBARU</t></is></c></row>');
        xmlRows.push(`<row r="3" ht="18" customHeight="1"><c r="A3" s="0" t="inlineStr"><is><t>${xml(filterDesc || "Semua Laporan")}</t></is></c></row>`);
        xmlRows.push(`<row r="4" ht="18" customHeight="1"><c r="A4" s="0" t="inlineStr"><is><t>${xml("Tanggal Unduh: " + todayFormatted + " | Total Data: " + reports.length + " laporan")}</t></is></c></row>`);
        xmlRows.push('<row r="5"/>');

        let r6 = '<row r="6" ht="26" customHeight="1">';
        headers.forEach((h, col) => {
            const addr = String.fromCharCode(65 + col) + "6";
            r6 += `<c r="${addr}" s="3" t="inlineStr"><is><t>${xml(h)}</t></is></c>`;
        });
        r6 += '</row>';
        xmlRows.push(r6);

        const centerCols = new Set([0, 2, 5, 7, 8, 9]);
        dataRows.forEach((rowVals, idx) => {
            const rowNum = idx + 7;
            let rStr = `<row r="${rowNum}" ht="22" customHeight="1">`;
            rowVals.forEach((val, col) => {
                const addr = String.fromCharCode(65 + col) + rowNum;
                const style = centerCols.has(col) ? "4" : "5";
                if (col === 0 && typeof val === "number") {
                    rStr += `<c r="${addr}" s="${style}"><v>${val}</v></c>`;
                } else {
                    rStr += `<c r="${addr}" s="${style}" t="inlineStr"><is><t xml:space="preserve">${xml(val)}</t></is></c>`;
                }
            });
            rStr += '</row>';
            xmlRows.push(rStr);
        });

        const mergeList = ["A1:J1", "A2:J2", "A3:J3", "A4:J4"];
        const mergesXml = `<mergeCells count="${mergeList.length}">${mergeList.map((m) => `<mergeCell ref="${m}"/>`).join("")}</mergeCells>`;

        const sheet1 = `<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <dimension ref="A1:J${dataRows.length + 6}"/>
  <sheetViews><sheetView workbookViewId="0"/></sheetViews>
  <sheetFormatPr defaultRowHeight="15"/>
  ${colsXml}
  <sheetData>
    ${xmlRows.join("\n    ")}
  </sheetData>
  ${mergesXml}
  <pageMargins left="0.25" right="0.25" top="0.35" bottom="0.35" header="0.15" footer="0.15"/>
  <pageSetup paperSize="9" orientation="landscape" fitToWidth="1" fitToHeight="0"/>
</worksheet>`;

        const parts = {
            "[Content_Types].xml": `<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
</Types>`,
            "_rels/.rels": `<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>`,
            "xl/_rels/workbook.xml.rels": `<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rIdStyles" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
  <Relationship Id="rIdSheet1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
</Relationships>`,
            "xl/workbook.xml": `<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets><sheet name="${xml(sheetName)}" sheetId="1" r:id="rIdSheet1"/></sheets>
</workbook>`,
            "xl/styles.xml": reportStylesXml,
            "xl/worksheets/sheet1.xml": sheet1
        };
        return zipBlob(parts);
    }

    function buildReportsPdf(reports, context, meta = {}) {
        if (!window.jspdf?.jsPDF) {
            throw new Error("Pustaka PDF belum termuat di peramban. Silakan muat ulang halaman.");
        }
        const pdf = new window.jspdf.jsPDF({
            orientation: "landscape",
            unit: "mm",
            format: "a4",
            compress: true,
            putOnlyUsedFonts: true,
        });

        let hasCustomFont = false;
        if (window.SidayaReportFonts) {
            try {
                for (const [style, data] of Object.entries(window.SidayaReportFonts)) {
                    pdf.addFileToVFS("Sidaya-" + style + ".ttf", data);
                    pdf.addFont("Sidaya-" + style + ".ttf", "Sidaya", style);
                }
                pdf.setFont("Sidaya", "normal");
                hasCustomFont = true;
            } catch {
                hasCustomFont = false;
            }
        }
        const fontName = hasCustomFont ? "Sidaya" : "helvetica";

        const isSemester = reports.some(r => r.pillar === "lks" || r.period_type === "semester");
        const docTitle = isSemester ? "REKAPITULASI LAPORAN SEMESTER LKS" : "REKAPITULASI LAPORAN TRIWULAN";
        const todayFormatted = indonesianDate(meta.today || new Date().toISOString().slice(0, 10));

        pdf.setProperties({
            title: `${docTitle} - Dinas Sosial Kota Banjarbaru`,
            subject: "Rekapitulasi Laporan",
            author: "SiDaya - Dinas Sosial Kota Banjarbaru",
            creator: "SiDaya Admin Workspace",
        });

        const setFont = (size = 8, style = "normal", color = [30, 41, 59]) => {
            pdf.setFont(fontName, style);
            pdf.setFontSize(size);
            pdf.setTextColor(...color);
        };

        const left = 10;
        const contentWidth = 277;
        const pageBottom = 192;

        const columns = [
            { label: "NO", width: 9, align: "center" },
            { label: "NAMA MITRA / LEMBAGA", width: 48, align: "left" },
            { label: "WILAYAH", width: 40, align: "left" },
            { label: "PERIODE", width: 38, align: "center" },
            { label: "JUDUL LAPORAN", width: 62, align: "left" },
            { label: "BERKAS", width: 24, align: "center" },
            { label: "PENGAJUAN", width: 28, align: "center" },
            { label: "STATUS", width: 28, align: "center" },
        ];

        const periodFilter = context.year && context.quarter
            ? (context.quarter === "all" ? `Tahun ${context.year}` : (isSemester ? `Semester ${context.quarter} - ${context.year}` : `Triwulan ${context.quarter} - ${context.year}`))
            : "Semua Periode";
        const filterDesc = [
            `Periode: ${periodFilter}`,
            context.status && context.status !== "all" ? `Status: ${context.status}` : "",
            context.district ? `Kecamatan: ${context.district}` : ""
        ].filter(Boolean).join(" | ");

        let y = 12;

        const drawDocHeader = () => {
            setFont(9, "bold", [71, 85, 105]);
            pdf.text("PEMERINTAH KOTA BANJARBARU · DINAS SOSIAL", left, y);
            y += 4.5;

            setFont(12, "bold", [15, 23, 42]);
            pdf.text(docTitle, left, y);
            y += 4.5;

            setFont(8, "normal", [71, 85, 105]);
            pdf.text(filterDesc || "Semua Laporan", left, y);
            y += 3.8;

            pdf.setDrawColor(30, 41, 59);
            pdf.setLineWidth(0.6);
            pdf.line(left, y, left + contentWidth, y);
            y += 0.8;
            pdf.setDrawColor(203, 213, 225);
            pdf.setLineWidth(0.2);
            pdf.line(left, y, left + contentWidth, y);
            y += 4;

            setFont(8, "bold", [51, 65, 85]);
            pdf.text(`Total Data: ${reports.length} laporan terdaftar | Tanggal Cetak: ${todayFormatted}`, left, y);
            y += 3.5;
        };

        const drawRunningHeader = () => {
            setFont(7.5, "bold", [100, 116, 139]);
            pdf.text(`SiDaya · DINAS SOSIAL KOTA BANJARBARU — ${docTitle}`, left, 11);
            pdf.text(`Tanggal Cetak: ${todayFormatted}`, left + contentWidth, 11, { align: "right" });
            pdf.setDrawColor(226, 232, 240);
            pdf.setLineWidth(0.2);
            pdf.line(left, 13, left + contentWidth, 13);
            y = 15;
        };

        const drawTableHeader = () => {
            const headerHeight = 7;
            pdf.setFillColor(241, 245, 249);
            pdf.rect(left, y, contentWidth, headerHeight, "F");

            pdf.setDrawColor(203, 213, 225);
            pdf.setLineWidth(0.2);
            pdf.rect(left, y, contentWidth, headerHeight, "S");

            let currentX = left;
            setFont(7, "bold", [30, 41, 59]);

            columns.forEach((col, idx) => {
                if (idx > 0) {
                    pdf.line(currentX, y, currentX, y + headerHeight);
                }
                if (col.align === "center") {
                    pdf.text(col.label, currentX + col.width / 2, y + 4.6, { align: "center" });
                } else if (col.align === "right") {
                    pdf.text(col.label, currentX + col.width - 2, y + 4.6, { align: "right" });
                } else {
                    pdf.text(col.label, currentX + 2, y + 4.6);
                }
                currentX += col.width;
            });

            y += headerHeight;
        };

        drawDocHeader();
        drawTableHeader();

        const labels = meta.labels || { lks: "LKS", psm: "PSM", "karang-taruna": "Karang Taruna" };
        const store = meta.store || window.SidayaQuarterly;

        reports.forEach((item, index) => {
            const org = item.organizationSnapshot || store?.organization?.(item.organizationId) || {};
            const orgTitle = cleanPdfText((meta.organizationTitle ? meta.organizationTitle(item) : null) || org.title || item.organizationId || "Mitra");
            const wilayah = cleanPdfText([org.village, org.district].filter(Boolean).join(", ") || "-");
            const pLabel = cleanPdfText(item.periodLabel || (isSemester ? `Semester ${item.quarter} - ${item.year}` : `Triwulan ${item.quarter} - ${item.year}`));
            const title = cleanPdfText(item.title || "-");
            const filesCount = `${(item.attachments || []).length} berkas`;
            const submitDate = item.submittedAt ? indonesianDate(item.submittedAt) : "-";
            const status = item.status || "Draf";

            const rowVals = [
                String(index + 1),
                orgTitle,
                wilayah,
                pLabel,
                title,
                filesCount,
                submitDate,
                status
            ];

            const col1Lines = pdf.splitTextToSize(orgTitle, columns[1].width - 4);
            const col2Lines = pdf.splitTextToSize(wilayah, columns[2].width - 4);
            const col4Lines = pdf.splitTextToSize(title, columns[4].width - 4);

            const lineCount = Math.max(1, col1Lines.length, col2Lines.length, col4Lines.length);
            const rowHeight = Math.max(6.5, lineCount * 3.4 + 2.5);

            if (y + rowHeight > pageBottom) {
                pdf.addPage("a4", "landscape");
                drawRunningHeader();
                drawTableHeader();
            }

            if (index % 2 === 1) {
                pdf.setFillColor(248, 250, 252);
                pdf.rect(left, y, contentWidth, rowHeight, "F");
            }

            pdf.setDrawColor(226, 232, 240);
            pdf.setLineWidth(0.15);
            pdf.rect(left, y, contentWidth, rowHeight, "S");

            let currentX = left;
            setFont(7, "normal", [30, 41, 59]);

            columns.forEach((col, cIdx) => {
                if (cIdx > 0) {
                    pdf.line(currentX, y, currentX, y + rowHeight);
                }

                let textVal = rowVals[cIdx];
                let lines = [textVal];
                if (cIdx === 1) lines = col1Lines;
                else if (cIdx === 2) lines = col2Lines;
                else if (cIdx === 4) lines = col4Lines;

                const textYOffset = (rowHeight - (lines.length * 3.2)) / 2 + 2.6;

                lines.forEach((lineStr, lineIdx) => {
                    const lineY = y + textYOffset + (lineIdx * 3.2);
                    if (col.align === "center") {
                        pdf.text(lineStr, currentX + col.width / 2, lineY, { align: "center" });
                    } else if (col.align === "right") {
                        pdf.text(lineStr, currentX + col.width - 2, lineY, { align: "right" });
                    } else {
                        pdf.text(lineStr, currentX + 2, lineY);
                    }
                });

                currentX += col.width;
            });

            y += rowHeight;
        });

        const totalPages = pdf.getNumberOfPages();
        for (let p = 1; p <= totalPages; p++) {
            pdf.setPage(p);
            setFont(7, "normal", [148, 163, 184]);
            pdf.text(
                "SiDaya · Dinas Sosial Kota Banjarbaru",
                left,
                202,
            );
            pdf.text(
                `Halaman ${p} dari ${totalPages}`,
                left + contentWidth,
                202,
                { align: "right" },
            );
        }

        return pdf.output("blob");
    }

    async function download(format, reports, context, meta = {}) {
        if (!reports || !reports.length) {
            throw new Error("Belum ada laporan sesuai filter untuk diunduh.");
        }
        const todayStr = meta.today || new Date().toISOString().slice(0, 10);
        const ext = format === "xlsx" || format === "excel" ? "xlsx" : "pdf";
        let blob;

        if (ext === "pdf") {
            await window.SidayaPdf?.ready();
            blob = buildReportsPdf(reports, context, meta);
        } else {
            blob = buildReportsXlsx(reports, context, meta);
        }

        const isSemester = reports.some(r => r.pillar === "lks" || r.period_type === "semester");
        const prefix = isSemester ? "Rekap_Laporan_Semester_LKS" : "Rekap_Laporan_Triwulan";
        const yearPart = context.year && context.year !== "all" ? `_${context.year}` : "";
        const filename = `${prefix}${yearPart}_${todayStr}.${ext}`;

        const url = URL.createObjectURL(blob);
        const anchor = document.createElement("a");
        anchor.href = url;
        anchor.download = filename;
        document.body.append(anchor);
        anchor.click();
        anchor.remove();
        setTimeout(() => URL.revokeObjectURL(url), 1000);
    }

    return { download, buildReportsXlsx, buildReportsPdf };
})();
