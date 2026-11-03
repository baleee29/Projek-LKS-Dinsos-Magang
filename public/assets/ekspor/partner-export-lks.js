"use strict";

// Excel and PDF export of the Admin LKS directory.
window.SidayaAdminPartnerExportLks = (() => {
    const { zipBlob, stylesXml, xml, indonesianDate, cleanPdfText } = window.SidayaAdminPartnerExportCommon;

    function buildLksXlsx(dataRows, todayFormatted) {
        const headers = [
            "NO",
            "NAMA LEMBAGA (LKS)",
            "KETUA / PENGURUS",
            "NO. HP / WHATSAPP",
            "KECAMATAN",
            "KELURAHAN",
            "ALAMAT LENGKAP",
            "LEGALITAS / SK",
            "REKENING BANK",
            "SDM TERCATAT",
            "STATUS",
        ];
        const columnWidths = [6, 34, 24, 20, 20, 20, 36, 26, 30, 16, 14];
        const colsXml = `<cols>${columnWidths.map((w, i) => `<col min="${i + 1}" max="${i + 1}" width="${w}" customWidth="1"/>`).join("")}</cols>`;
        const xmlRows = [];
        xmlRows.push(
            '<row r="1" ht="24" customHeight="1"><c r="A1" s="1" t="inlineStr"><is><t>DAFTAR LEMBAGA KESEJAHTERAAN SOSIAL (LKS)</t></is></c></row>',
        );
        xmlRows.push(
            '<row r="2" ht="20" customHeight="1"><c r="A2" s="1" t="inlineStr"><is><t>DINAS SOSIAL KOTA BANJARBARU</t></is></c></row>',
        );
        xmlRows.push(
            `<row r="3" ht="18" customHeight="1"><c r="A3" s="0" t="inlineStr"><is><t>${xml("Tanggal Cetak: " + todayFormatted)}</t></is></c></row>`,
        );
        xmlRows.push('<row r="4"/>');
        let r5 = '<row r="5" ht="26" customHeight="1">';
        headers.forEach((h, col) => {
            const addr = String.fromCharCode(65 + col) + "5";
            r5 += `<c r="${addr}" s="2" t="inlineStr"><is><t>${xml(h)}</t></is></c>`;
        });
        r5 += "</row>";
        xmlRows.push(r5);
        const centerCols = new Set([0, 3, 9, 10]);
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
        const mergeList = ["A1:K1", "A2:K2", "A3:K3"];
        const mergesXml = `<mergeCells count="${mergeList.length}">${mergeList.map((m) => `<mergeCell ref="${m}"/>`).join("")}</mergeCells>`;
        const sheet1 = `<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <dimension ref="A1:K${dataRows.length + 5}"/>
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
  <sheets><sheet name="Daftar LKS" sheetId="1" r:id="rIdSheet1"/></sheets>
</workbook>`,
            "xl/styles.xml": stylesXml,
            "xl/worksheets/sheet1.xml": sheet1,
        };
        return zipBlob(parts);
    }

    function buildLksPdf(items, todayFormatted, members = []) {
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
                for (const [style, data] of Object.entries(
                    window.SidayaReportFonts,
                )) {
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

        pdf.setProperties({
            title: "Daftar LKS - Dinas Sosial Kota Banjarbaru",
            subject:
                "Daftar Lembaga Kesejahteraan Sosial (LKS) Kota Banjarbaru",
            author: "SiDaya - Dinas Sosial Kota Banjarbaru",
            creator: "SiDaya Admin Workspace",
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
            {
                id: "name",
                header: "NAMA LEMBAGA (LKS)",
                width: 48,
                align: "left",
            },
            {
                id: "chair",
                header: "KETUA / PENGURUS",
                width: 36,
                align: "left",
            },
            { id: "phone", header: "NO. WHATSAPP", width: 28, align: "center" },
            { id: "district", header: "KECAMATAN", width: 28, align: "left" },
            { id: "village", header: "KELURAHAN", width: 26, align: "left" },
            { id: "legal", header: "LEGALITAS / SK", width: 35, align: "left" },
            { id: "bank", header: "REKENING BANK", width: 40, align: "left" },
            { id: "sdm", header: "SDM", width: 18, align: "center" },
        ];

        let y = 14;

        const drawTableHeader = () => {
            const headerHeight = 7.5;
            pdf.setFillColor(30, 41, 59);
            pdf.rect(left, y, contentWidth, headerHeight, "F");

            setFont(7.5, "bold", [255, 255, 255]);
            let currentX = left;
            columns.forEach((col) => {
                const textY = y + 5;
                if (col.align === "center") {
                    pdf.text(col.header, currentX + col.width / 2, textY, {
                        align: "center",
                    });
                } else if (col.align === "right") {
                    pdf.text(col.header, currentX + col.width - 2, textY, {
                        align: "right",
                    });
                } else {
                    pdf.text(col.header, currentX + 2, textY);
                }
                currentX += col.width;
            });
            y += headerHeight;
        };

        const drawDocHeader = () => {
            setFont(9, "bold", [71, 85, 105]);
            pdf.text("PEMERINTAH KOTA BANJARBARU", left, y);
            y += 4.5;

            setFont(14, "bold", [15, 23, 42]);
            pdf.text("DINAS SOSIAL", left, y);
            y += 5;

            setFont(10.5, "bold", [30, 41, 59]);
            pdf.text("DAFTAR LEMBAGA KESEJAHTERAAN SOSIAL (LKS)", left, y);

            setFont(7.5, "normal", [100, 116, 139]);
            pdf.text(
                `Sistem Informasi Pemberdayaan Sosial (SiDaya) | Tanggal Cetak: ${todayFormatted}`,
                left + contentWidth,
                y,
                { align: "right" },
            );
            y += 3.5;

            pdf.setDrawColor(30, 41, 59);
            pdf.setLineWidth(0.6);
            pdf.line(left, y, left + contentWidth, y);
            y += 0.8;
            pdf.setDrawColor(203, 213, 225);
            pdf.setLineWidth(0.2);
            pdf.line(left, y, left + contentWidth, y);
            y += 4;

            setFont(8, "bold", [51, 65, 85]);
            pdf.text(
                `Total Data: ${items.length} Lembaga Kesejahteraan Sosial (LKS)`,
                left,
                y,
            );
            y += 3.5;
        };

        const drawRunningHeader = () => {
            setFont(7.5, "bold", [100, 116, 139]);
            pdf.text(
                "SiDaya · DINAS SOSIAL KOTA BANJARBARU — DAFTAR LKS",
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
            const noStr = String(index + 1);
            const nameStr = cleanPdfText(item.title || item.name || "-");
            const chairStr = cleanPdfText(item.chair || "-");
            const phoneStr = cleanPdfText(
                item.contact || item.whatsapp || item.phone || "-",
            );
            const districtStr = cleanPdfText(item.district || "-");
            const villageStr = cleanPdfText(item.village || "-");
            const legalStr = cleanPdfText(item.legal || "-");
            const bankStr = cleanPdfText(
                item.bankAccount || item.rekening || "-",
            );
            const sdmStr =
                String(
                    members.filter((m) => m.organizationId === item.id).length,
                ) + " orang";

            const rowValues = [
                noStr,
                nameStr,
                chairStr,
                phoneStr,
                districtStr,
                villageStr,
                legalStr,
                bankStr,
                sdmStr,
            ];

            setFont(7, "normal");
            const cellLines = columns.map((col, cIdx) => {
                const text = rowValues[cIdx];
                return pdf.splitTextToSize(text, col.width - 3);
            });

            const maxLines = Math.max(
                ...cellLines.map((lines) => lines.length),
                1,
            );
            const lineHeight = 3.4;
            const rowHeight = Math.max(6.5, maxLines * lineHeight + 2.5);

            if (y + rowHeight > pageBottom) {
                pdf.addPage();
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
            columns.forEach((col, cIdx) => {
                if (cIdx > 0) {
                    pdf.line(currentX, y, currentX, y + rowHeight);
                }

                const lines = cellLines[cIdx];
                const totalTextHeight = lines.length * lineHeight;
                let startTextY = y + (rowHeight - totalTextHeight) / 2 + 2.5;

                setFont(7, cIdx === 1 ? "bold" : "normal", [30, 41, 59]);

                lines.forEach((line) => {
                    if (col.align === "center") {
                        pdf.text(line, currentX + col.width / 2, startTextY, {
                            align: "center",
                        });
                    } else if (col.align === "right") {
                        pdf.text(line, currentX + col.width - 1.5, startTextY, {
                            align: "right",
                        });
                    } else {
                        pdf.text(line, currentX + 1.5, startTextY);
                    }
                    startTextY += lineHeight;
                });

                currentX += col.width;
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
                "Dokumen resmi SiDaya — Dinas Sosial Kota Banjarbaru",
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

    function exportLks(format, items, members = [], notify) {
        if (!items || !items.length) {
            notify?.("Tidak ada data LKS yang dapat diunduh.");
            return;
        }
        const today = new Date().toISOString().slice(0, 10);
        const todayFormatted = indonesianDate(today);
        let blob;
        const ext = format === "xlsx" || format === "excel" ? "xlsx" : "pdf";

        if (ext === "pdf") {
            blob = buildLksPdf(items, todayFormatted, members);
        } else {
            const dataRows = items.map((item, index) => {
                const sdmCount = members.filter(
                    (m) => m.organizationId === item.id,
                ).length;
                return [
                    index + 1,
                    item.title || item.name || "Nama LKS belum diisi",
                    item.chair || "",
                    item.contact || item.whatsapp || item.phone || "",
                    item.district || "",
                    item.village || "",
                    item.address || "",
                    item.legal || "",
                    item.bankAccount || item.rekening || "",
                    `${sdmCount} orang`,
                    item.status || "Aktif",
                ];
            });
            blob = buildLksXlsx(dataRows, todayFormatted);
        }
        const url = URL.createObjectURL(blob);
        const anchor = document.createElement("a");
        anchor.href = url;
        anchor.download = `Daftar_LKS_Banjarbaru_${today}.${ext}`;
        document.body.append(anchor);
        anchor.click();
        anchor.remove();
        setTimeout(() => URL.revokeObjectURL(url), 1000);
        const label = ext === "xlsx" ? "Excel" : "PDF";
        notify?.(`Daftar LKS berhasil diunduh (${label}).`);
    }

    return { exportLks };
})();
