"use strict";

// PDF and Excel builders for the LKS semester report list.
window.SidayaLKSReportExport = (() => {
    const { date } = window.SidayaLKSUI;

    const cleanPdfText = (val) =>
        String(val ?? "")
            .replace(/\r\n/g, " ")
            .replace(/[\r\n\t]/g, " ")
            .replace(/\s+/g, " ")
            .trim();
    const xml = window.SidayaXlsx.xml;
    const zipBlob = (parts) => window.SidayaXlsx.zip(parts, "Ukuran ekspor terlalu besar.");
    const lksReportStylesXml = `<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="3">
    <font><sz val="10"/><name val="Calibri"/></font>
    <font><b/><sz val="10"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>
    <font><b/><sz val="12"/><name val="Calibri"/></font>
  </fonts>
  <fills count="3">
    <fill><patternFill patternType="none"/></fill>
    <fill><patternFill patternType="gray125"/></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FF304A6B"/></patternFill></fill>
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
    <xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1"><alignment vertical="center"/></xf>
    <xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>
    <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center" wrapText="1"/></xf>
    <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>
  </cellXfs>
</styleSheet>`;
    function buildReportPdf(items, organization, state, quarterlyStore) {
        if (!window.jspdf?.jsPDF) {
            throw new Error(
                "Pustaka PDF belum termuat di peramban. Silakan muat ulang halaman.",
            );
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
        const today = new Date().toISOString().slice(0, 10);
        const todayFormatted = date(today);

        let periodText = "Semua Periode";
        if (state.year && state.quarter) {
            periodText = `Semester ${state.quarter} Tahun ${state.year}`;
        } else if (state.year) {
            periodText = `Tahun ${state.year}`;
        } else if (state.quarter) {
            periodText = `Semester ${state.quarter}`;
        }
        if (state.status) {
            periodText += ` · Status: ${state.status}`;
        }

        const docTitle = "DAFTAR LAPORAN SEMESTER LKS";
        const orgTitle = organization?.title || "Lembaga Kesejahteraan Sosial";

        pdf.setProperties({
            title: `${docTitle} - ${orgTitle}`,
            subject: docTitle,
            author: "SiDaya - Dinas Sosial Kota Banjarbaru",
            creator: "SiDaya LKS Workspace",
        });

        const setFont = (size = 8, style = "normal", color = [30, 41, 59]) => {
            pdf.setFont(fontName, style);
            pdf.setFontSize(size);
            pdf.setTextColor(...color);
        };

        const left = 14;
        const contentWidth = 269;
        const pageBottom = 192;

        const columns = [
            { id: "no", header: "NO", width: 10, align: "center" },
            { id: "period", header: "PERIODE", width: 28, align: "center" },
            { id: "id", header: "ID LAPORAN", width: 36, align: "center" },
            { id: "title", header: "JUDUL / PERIHAL LAPORAN", width: 62, align: "left" },
            { id: "date", header: "TANGGAL", width: 26, align: "center" },
            { id: "files", header: "BERKAS", width: 20, align: "center" },
            { id: "status", header: "STATUS", width: 24, align: "center" },
            { id: "note", header: "CATATAN PENGIRIMAN", width: 63, align: "left" },
        ];

        let y = 14;

        const drawTableHeader = () => {
            const headerHeight = 7.5;
            pdf.setFillColor(48, 74, 107);
            pdf.rect(left, y, contentWidth, headerHeight, "F");

            setFont(7.5, "bold", [255, 255, 255]);
            let currentX = left;
            columns.forEach((col) => {
                const textY = y + 5;
                if (col.align === "center") {
                    pdf.text(col.header, currentX + col.width / 2, textY, { align: "center" });
                } else if (col.align === "right") {
                    pdf.text(col.header, currentX + col.width - 2, textY, { align: "right" });
                } else {
                    pdf.text(col.header, currentX + 2, textY);
                }
                currentX += col.width;
            });
            y += headerHeight;
        };

        const drawDocHeader = () => {
            setFont(9, "bold", [71, 85, 105]);
            pdf.text("PEMERINTAH KOTA BANJARBARU — DINAS SOSIAL", left, y);
            y += 4.5;

            setFont(13, "bold", [15, 23, 42]);
            pdf.text(docTitle, left, y);
            y += 5;

            setFont(10, "bold", [48, 74, 107]);
            pdf.text(orgTitle, left, y);

            setFont(7.5, "normal", [100, 116, 139]);
            pdf.text(
                `Sistem Informasi Pemberdayaan Sosial (SiDaya) | Tanggal Cetak: ${todayFormatted}`,
                left + contentWidth,
                y,
                { align: "right" },
            );
            y += 3.5;

            pdf.setDrawColor(48, 74, 107);
            pdf.setLineWidth(0.6);
            pdf.line(left, y, left + contentWidth, y);
            y += 0.8;
            pdf.setDrawColor(203, 213, 225);
            pdf.setLineWidth(0.2);
            pdf.line(left, y, left + contentWidth, y);
            y += 4;

            setFont(8, "bold", [51, 65, 85]);
            pdf.text(
                `Total Data: ${items.length} Laporan  |  Periode: ${periodText}`,
                left,
                y,
            );
            y += 3.5;
        };

        const drawRunningHeader = () => {
            setFont(7.5, "bold", [100, 116, 139]);
            pdf.text(
                `SiDaya · DINAS SOSIAL KOTA BANJARBARU — ${docTitle} (${orgTitle})`,
                left,
                11,
            );
            pdf.text(
                `Tanggal Cetak: ${todayFormatted}`,
                left + contentWidth,
                11,
                { align: "right" },
            );
            pdf.setDrawColor(226, 232, 240);
            pdf.setLineWidth(0.2);
            pdf.line(left, 13, left + contentWidth, 13);
            y = 15;
        };

        drawDocHeader();
        drawTableHeader();

        items.forEach((item, index) => {
            const p = quarterlyStore.period(item.year, item.quarter, "lks");
            const filesCount = (item.attachments || []).length;
            const dateStr = item.submittedAt
                ? date(item.submittedAt)
                : (item.updated ? date(item.updated) : "—");

            const rowValues = [
                String(index + 1),
                cleanPdfText(p.label),
                cleanPdfText(item.id),
                cleanPdfText(item.title || "Laporan Semester"),
                cleanPdfText(dateStr),
                cleanPdfText(`${filesCount} berkas`),
                cleanPdfText(item.status || "Draf"),
                cleanPdfText(item.hasDraftChanges ? "Ada perubahan belum dikirim" : item.status === "Draf" ? "Belum dikirim" : "Sudah masuk ke Admin"),
            ];

            setFont(7, "normal");
            const cellLines = columns.map((col, cIdx) => {
                const textVal = rowValues[cIdx];
                return pdf.splitTextToSize(textVal, col.width - 3);
            });

            const maxLines = Math.max(...cellLines.map((lines) => lines.length), 1);
            const lineHeight = 3.4;
            const rowHeight = Math.max(6.5, maxLines * lineHeight + 2.5);

            if (y + rowHeight > pageBottom) {
                pdf.addPage();
                drawRunningHeader();
                drawTableHeader();
            }

            const bg = index % 2 === 1;
            if (bg) {
                pdf.setFillColor(248, 250, 252);
                pdf.rect(left, y, contentWidth, rowHeight, "F");
            }

            pdf.setDrawColor(226, 232, 240);
            pdf.setLineWidth(0.15);
            pdf.rect(left, y, contentWidth, rowHeight, "S");

            columns.forEach((col, cIdx) => {
                const lines = cellLines[cIdx];
                let startTextY = y + 4;
                let currentX = left + columns.slice(0, cIdx).reduce((sum, c) => sum + c.width, 0);

                lines.forEach((line) => {
                    if (col.align === "center") {
                        pdf.text(line, currentX + col.width / 2, startTextY, { align: "center" });
                    } else if (col.align === "right") {
                        pdf.text(line, currentX + col.width - 1.5, startTextY, { align: "right" });
                    } else {
                        pdf.text(line, currentX + 1.5, startTextY);
                    }
                    startTextY += lineHeight;
                });
            });

            y += rowHeight;
        });

        const totalPages = pdf.getNumberOfPages();
        for (let p = 1; p <= totalPages; p++) {
            pdf.setPage(p);
            pdf.setDrawColor(203, 213, 225);
            pdf.setLineWidth(0.2);
            pdf.line(left, 198, left + contentWidth, 198);

            setFont(6.5, "normal", [100, 116, 139]);
            pdf.text(
                `Dokumen resmi SiDaya — Dinas Sosial Kota Banjarbaru | ${orgTitle}`,
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
    function buildReportXlsx(items, organization, state, quarterlyStore) {
        const today = new Date().toISOString().slice(0, 10);
        const todayFormatted = date(today);
        const sheetName = "Laporan Semester";
        const docTitle = "DAFTAR LAPORAN SEMESTER LEMBAGA KESEJAHTERAAN SOSIAL (LKS)";
        const orgTitle = organization?.title || "Lembaga Kesejahteraan Sosial";

        let periodText = "Semua Periode";
        if (state.year && state.quarter) {
            periodText = `Semester ${state.quarter} Tahun ${state.year}`;
        } else if (state.year) {
            periodText = `Tahun ${state.year}`;
        } else if (state.quarter) {
            periodText = `Semester ${state.quarter}`;
        }
        if (state.status) {
            periodText += ` · Status: ${state.status}`;
        }

        const headers = [
            "NO",
            "PERIODE",
            "ID LAPORAN",
            "JUDUL / PERIHAL LAPORAN",
            "TANGGAL PENGIRIMAN",
            "BERKAS",
            "STATUS",
            "CATATAN PENGIRIMAN",
        ];
        const colWidths = [6, 18, 24, 38, 20, 15, 18, 40];
        const centerCols = new Set([0, 1, 2, 4, 5, 6]);

        const dataRows = items.map((item, index) => {
            const p = quarterlyStore.period(item.year, item.quarter, "lks");
            const filesCount = (item.attachments || []).length;
            const dateStr = item.submittedAt
                ? date(item.submittedAt)
                : (item.updated ? date(item.updated) : "—");

            return [
                index + 1,
                p.label,
                item.id,
                item.title || "Laporan Semester",
                dateStr,
                `${filesCount} berkas`,
                item.status || "Draf",
                item.hasDraftChanges ? "Ada perubahan belum dikirim" : item.status === "Draf" ? "Belum dikirim" : "Sudah masuk ke Admin",
            ];
        });

        const xmlRows = [];
        xmlRows.push(
            '<row r="1" ht="22" customHeight="1"><c r="A1" s="1" t="inlineStr"><is><t>PEMERINTAH KOTA BANJARBARU - DINAS SOSIAL</t></is></c></row>',
        );
        xmlRows.push(
            `<row r="2" ht="24" customHeight="1"><c r="A2" s="1" t="inlineStr"><is><t>${xml(docTitle)}</t></is></c></row>`,
        );
        xmlRows.push(
            `<row r="3" ht="18" customHeight="1"><c r="A3" s="0" t="inlineStr"><is><t>${xml(`Lembaga: ${orgTitle}  |  Periode: ${periodText}  |  Tanggal Cetak: ${todayFormatted}  |  Total: ${items.length} Laporan`)}</t></is></c></row>`,
        );
        xmlRows.push('<row r="4" ht="10"/>');

        let r5 = '<row r="5" ht="26" customHeight="1">';
        headers.forEach((h, col) => {
            const addr = String.fromCharCode(65 + col) + "5";
            r5 += `<c r="${addr}" s="2" t="inlineStr"><is><t>${xml(h)}</t></is></c>`;
        });
        r5 += "</row>";
        xmlRows.push(r5);

        dataRows.forEach((rowVals, idx) => {
            const rowNum = idx + 6;
            let rStr = `<row r="${rowNum}" ht="22" customHeight="1">`;
            rowVals.forEach((val, col) => {
                const addr = String.fromCharCode(65 + col) + rowNum;
                const style = centerCols.has(col) ? "3" : "4";
                if (col === 0 && typeof val === "number") {
                    rStr += `<c r="${addr}" s="${style}"><v>${val}</v></c>`;
                } else {
                    let textVal = String(val ?? "");
                    if (/^[\s]*[=+\-@]/.test(textVal)) {
                        textVal = "'" + textVal;
                    }
                    rStr += `<c r="${addr}" s="${style}" t="inlineStr"><is><t xml:space="preserve">${xml(textVal)}</t></is></c>`;
                }
            });
            rStr += "</row>";
            xmlRows.push(rStr);
        });

        const lastColLetter = String.fromCharCode(64 + headers.length);
        const mergeList = [
            `A1:${lastColLetter}1`,
            `A2:${lastColLetter}2`,
            `A3:${lastColLetter}3`,
        ];
        const mergesXml = `<mergeCells count="${mergeList.length}">${mergeList.map((m) => `<mergeCell ref="${m}"/>`).join("")}</mergeCells>`;
        const colsXml = `<cols>${colWidths.map((w, i) => `<col min="${i + 1}" max="${i + 1}" width="${w}" customWidth="1"/>`).join("")}</cols>`;

        const sheet1 = `<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <dimension ref="A1:${lastColLetter}${dataRows.length + 6}"/>
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
            "xl/styles.xml": lksReportStylesXml,
            "xl/worksheets/sheet1.xml": sheet1,
        };
        return zipBlob(parts);
    }

    return { buildReportPdf, buildReportXlsx };
})();
