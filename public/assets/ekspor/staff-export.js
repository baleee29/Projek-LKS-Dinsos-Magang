"use strict";

// Excel and PDF export of LKS staff and Karang Taruna member rosters.
window.SidayaLKSStaffExport = (() => {
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
        if (!value) return "";
        const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(text(value).trim());
        if (!match) return text(value);
        const [, year, month, day] = match;
        const monthNum = Number(month);
        if (monthNum >= 1 && monthNum <= 12) {
            return `${Number(day)} ${months[monthNum - 1]} ${year}`;
        }
        return text(value);
    }

    const zipBlob = (parts) => window.SidayaXlsx.zip(parts, "Ukuran ekspor terlalu besar.");

    const staffStylesXml = `<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
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

    function buildStaffXlsx(members, org, todayFormatted) {
        const isKt = org?.pillar === "karang-taruna";
        const isPsm = org?.pillar === "psm";
        const docTitle = isKt
            ? "DAFTAR PENGURUS & ANGGOTA KARANG TARUNA"
            : (isPsm ? "DAFTAR ANGGOTA PSM" : "DAFTAR PENGURUS & SUMBER DAYA MANUSIA (SDM)");
        const orgLabel = isKt ? "KELOMPOK KARANG TARUNA" : (isPsm ? "ORGANISASI PSM" : "LEMBAGA (LKS)");
        const defaultRole = isKt ? "Anggota KT" : (isPsm ? "Anggota PSM" : "Personel LKS");
        const countLabel = isKt ? "Pengurus dan anggota" : (isPsm ? "Anggota" : "Personel");
        const sheetName = isKt ? "Pengurus & Anggota KT" : (isPsm ? "Data Anggota PSM" : "Pengurus & SDM");

        const headers = [
            "NO", "NAMA LENGKAP", "NIK", "JABATAN / PERAN", "PEKERJAAN", "KATEGORI",
            "JENIS KELAMIN", "TEMPAT LAHIR", "TANGGAL LAHIR", "NO. HP / WHATSAPP",
            "ALAMAT KTP", "TANGGAL BERGABUNG", "STATUS", "KETERANGAN"
        ];
        const columnWidths = [6, 32, 22, 24, 20, 20, 16, 20, 18, 20, 36, 18, 16, 30];
        const colsXml = `<cols>${columnWidths.map((w, i) => `<col min="${i + 1}" max="${i + 1}" width="${w}" customWidth="1"/>`).join("")}</cols>`;

        const dataRows = members.map((member, index) => {
            const name = member.title || member.name || "Belum diisi";
            const nik = member.nik || "-";
            const position = member.position || member.role || defaultRole;
            const occupation = member.occupation || "-";
            const category = member.category || (isKt ? "Pengurus" : "Pengurus");
            const gender = member.gender || "-";
            const birthPlace = member.birthPlace || "-";
            const birthDateFormatted = member.birthDate ? indonesianDate(member.birthDate) : "-";
            const phone = member.contact || member.whatsapp || member.phone || "-";
            const address = member.ktpAddress || member.address || "-";
            const joinedFormatted = member.joined ? indonesianDate(member.joined) : "-";
            const status = member.isChair ? "Penanggung Jawab" : (member.status || "Aktif");
            const notes = member.notes || "-";

            return [
                index + 1,
                name,
                nik,
                position,
                occupation,
                category,
                gender,
                birthPlace,
                birthDateFormatted,
                phone,
                address,
                joinedFormatted,
                status,
                notes
            ];
        });

        const xmlRows = [];
        xmlRows.push(`<row r="1" ht="26" customHeight="1"><c r="A1" s="2" t="inlineStr"><is><t>${xml(docTitle)}</t></is></c></row>`);
        xmlRows.push('<row r="2" ht="20" customHeight="1"><c r="A2" s="1" t="inlineStr"><is><t>DINAS SOSIAL KOTA BANJARBARU</t></is></c></row>');
        xmlRows.push(`<row r="3" ht="20" customHeight="1"><c r="A3" s="1" t="inlineStr"><is><t>${xml("NAMA " + orgLabel + " : " + (org?.title || "Organisasi"))}</t></is></c></row>`);
        xmlRows.push(`<row r="4" ht="18" customHeight="1"><c r="A4" s="0" t="inlineStr"><is><t>${xml("Wilayah: " + (org?.village || "-") + ", " + (org?.district || "-") + " | Kontak: " + (org?.contact || "-"))}</t></is></c></row>`);
        xmlRows.push(`<row r="5" ht="18" customHeight="1"><c r="A5" s="0" t="inlineStr"><is><t>${xml("Tanggal Cetak: " + todayFormatted + " | Total Data: " + members.length + " " + countLabel + " terdaftar")}</t></is></c></row>`);
        xmlRows.push('<row r="6"/>');

        let r7 = '<row r="7" ht="26" customHeight="1">';
        headers.forEach((h, col) => {
            const addr = String.fromCharCode(65 + col) + "7";
            r7 += `<c r="${addr}" s="3" t="inlineStr"><is><t>${xml(h)}</t></is></c>`;
        });
        r7 += '</row>';
        xmlRows.push(r7);

        const centerCols = new Set([0, 2, 6, 8, 9, 11, 12]);
        dataRows.forEach((rowVals, idx) => {
            const rowNum = idx + 8;
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

        const mergeList = ["A1:N1", "A2:N2", "A3:N3", "A4:N4", "A5:N5"];
        const mergesXml = `<mergeCells count="${mergeList.length}">${mergeList.map((m) => `<mergeCell ref="${m}"/>`).join("")}</mergeCells>`;

        const sheet1 = `<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <dimension ref="A1:N${dataRows.length + 7}"/>
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
            "xl/styles.xml": staffStylesXml,
            "xl/worksheets/sheet1.xml": sheet1
        };
        return zipBlob(parts);
    }

    function buildStaffPdf(members, org, todayFormatted) {
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

        const isKt = org?.pillar === "karang-taruna";
        const isPsm = org?.pillar === "psm";
        const docTitle = isKt
            ? "DAFTAR PENGURUS & ANGGOTA KARANG TARUNA"
            : (isPsm ? "DAFTAR ANGGOTA PSM" : "DAFTAR PENGURUS & SUMBER DAYA MANUSIA (SDM)");
        const orgLabel = isKt ? "Kelompok Karang Taruna" : (isPsm ? "Organisasi PSM" : "Lembaga");
        const defaultRole = isKt ? "Anggota KT" : (isPsm ? "Anggota PSM" : "Personel LKS");
        const countLabel = isKt ? "Pengurus dan anggota" : (isPsm ? "Anggota" : "Personel");

        pdf.setProperties({
            title: `${docTitle} - ${org?.title || "Organisasi"}`,
            subject: isKt ? "Data Pengurus dan Anggota Karang Taruna" : (isPsm ? "Data Anggota PSM" : "Data Pengurus dan SDM LKS"),
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
            { label: "NO", width: 8, align: "center" },
            { label: "NAMA LENGKAP", width: 36, align: "left" },
            { label: "NIK", width: 26, align: "center" },
            { label: "JABATAN", width: 26, align: "left" },
            { label: "PEKERJAAN", width: 22, align: "left" },
            { label: "KATEGORI", width: 22, align: "left" },
            { label: "JK", width: 12, align: "center" },
            { label: "TEMPAT, TGL LAHIR", width: 30, align: "left" },
            { label: "NO. HP / WA", width: 23, align: "center" },
            { label: "BERGABUNG", width: 22, align: "center" },
            { label: "KETERANGAN", width: 50, align: "left" },
        ];

        let y = 12;

        const drawDocHeader = () => {
            setFont(9, "bold", [71, 85, 105]);
            pdf.text("PEMERINTAH KOTA BANJARBARU · DINAS SOSIAL", left, y);
            y += 4.5;

            setFont(12, "bold", [15, 23, 42]);
            pdf.text(docTitle, left, y);
            y += 4.5;

            setFont(8.5, "bold", [30, 41, 59]);
            pdf.text(`${orgLabel}: ${cleanPdfText(org?.title || "Organisasi")}`, left, y);
            y += 3.8;

            setFont(7.5, "normal", [100, 116, 139]);
            const loc = [org?.village, org?.district].filter(Boolean).join(", ");
            pdf.text(
                `Wilayah: ${cleanPdfText(loc || "-")} | Kontak: ${cleanPdfText(org?.contact || "-")} | Penanggung Jawab: ${cleanPdfText(org?.chair || "-")}`,
                left,
                y,
            );
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
            pdf.text(`Total Data: ${members.length} ${countLabel} terdaftar | Tanggal Cetak: ${todayFormatted}`, left, y);
            y += 3.5;
        };

        const drawRunningHeader = () => {
            setFont(7.5, "bold", [100, 116, 139]);
            pdf.text(`SiDaya · DINAS SOSIAL KOTA BANJARBARU — ${cleanPdfText(org?.title || "ORGANISASI")}`, left, 11);
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

        members.forEach((item, index) => {
            const noStr = String(index + 1);
            const nameStr = cleanPdfText(item.title || item.name || "-") + (item.isChair ? " (PJ)" : "");
            const nikStr = cleanPdfText(item.nik || "-");
            const roleStr = cleanPdfText(item.position || item.role || item.category || defaultRole);
            const occupationStr = cleanPdfText(item.occupation || "-");
            const categoryStr = cleanPdfText(item.category || "-");
            const jkStr = item.gender === "Laki-laki" ? "L" : item.gender === "Perempuan" ? "P" : cleanPdfText(item.gender || "-");

            let ttlStr = "-";
            const bp = cleanPdfText(item.birthPlace || "");
            const bd = item.birthDate ? indonesianDate(item.birthDate) : "";
            if (bp && bd) ttlStr = `${bp}, ${bd}`;
            else if (bp) ttlStr = bp;
            else if (bd) ttlStr = bd;

            const phoneStr = cleanPdfText(item.contact || item.whatsapp || item.phone || "-");
            const joinedStr = item.joined ? indonesianDate(item.joined) : "-";
            const notesStr = cleanPdfText(item.notes || "-");

            const rowValues = [
                noStr,
                nameStr,
                nikStr,
                roleStr,
                occupationStr,
                categoryStr,
                jkStr,
                ttlStr,
                phoneStr,
                joinedStr,
                notesStr
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
                        pdf.text(line, currentX + col.width / 2, startTextY, { align: "center" });
                    } else if (col.align === "right") {
                        pdf.text(line, currentX + col.width - 1.5, startTextY, { align: "right" });
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
                `Dokumen resmi SiDaya — Dinas Sosial Kota Banjarbaru | ${cleanPdfText(org?.title || "Organisasi")}`,
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

    async function download(format, members, org, today) {
        const isKt = org?.pillar === "karang-taruna";
        const isPsm = org?.pillar === "psm";
        const label = isKt ? "anggota Karang Taruna" : (isPsm ? "anggota PSM" : "Pengurus & SDM");
        if (!members || !members.length) {
            throw new Error(`Belum ada data ${label} untuk diunduh.`);
        }
        const todayStr = today || new Date().toISOString().slice(0, 10);
        const todayFormatted = indonesianDate(todayStr);
        const ext = format === "xlsx" || format === "excel" ? "xlsx" : "pdf";
        let blob;

        if (ext === "pdf") {
            await window.SidayaPdf?.ready();
            blob = buildStaffPdf(members, org, todayFormatted);
        } else {
            blob = buildStaffXlsx(members, org, todayFormatted);
        }

        const safeTitle = (org?.title || (isKt ? "Karang_Taruna" : (isPsm ? "PSM" : "LKS"))).replace(/[^a-zA-Z0-9_-]/g, "_").replace(/_+/g, "_");
        const prefix = isKt ? "Data_Anggota_KT" : (isPsm ? "Data_Anggota_PSM" : "Data_Pengurus_SDM");
        const filename = `${prefix}_${safeTitle}_${todayStr}.${ext}`;

        const url = URL.createObjectURL(blob);
        const anchor = document.createElement("a");
        anchor.href = url;
        anchor.download = filename;
        document.body.append(anchor);
        anchor.click();
        anchor.remove();
        setTimeout(() => URL.revokeObjectURL(url), 1000);
    }

    return { download, buildStaffXlsx, buildStaffPdf };
})();
