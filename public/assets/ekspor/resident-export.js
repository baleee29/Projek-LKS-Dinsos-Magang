"use strict";

// Export saved, filtered clients using the supplied LKSA form.
window.SidayaLKSResidentExport = (() => {
    const headers = ["NO", "NAMA ANAK", "JENIS KELAMIN", "", "NIK", "NO. KK", "AGAMA", "TEMPAT, TANGGAL LAHIR", "NAMA ORANG TUA", "ALAMAT ASAL", "STATUS", "", "", "PENDIDIKAN SAAT INI", "STATUS BPJS", "", "KETERANGAN", ""];
    const subheaders = ["", "", "L", "P", "", "", "", "", "", "", "YATIM", "PIATU", "DHUAFA", "", "ADA", "TIDAK ADA", "Dalam Panti", "Luar Panti"];
    const text = (value) => String(value ?? "");
    const xml = window.SidayaXlsx.xml;
    const mark = (value) => value === true ? "X" : "";
    const months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    function birthDate(value) {
        const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(text(value));
        if (!match) return "";
        const [, year, month, day] = match;
        const date = new Date(`${value}T12:00:00Z`);
        if (!Number.isFinite(date.getTime()) || date.toISOString().slice(0, 10) !== value) return "";
        return `${Number(day)} ${months[Number(month) - 1]} ${year}`;
    }
    function row(data, index) {
        const parent = data.guardianStatus === "Keluarga/wali diketahui" && /^(ayah|ibu|bapak|orang ?tua)(?: kandung| angkat| tiri)?$/i.test(text(data.guardianRelationship).trim()) ? data.guardianName : "";
        const education = data.educationStatus === "Sedang sekolah" ? [data.educationLevel, data.schoolName].filter(Boolean).join(" · ") || data.educationStatus : data.educationStatus;
        const inside = data.serviceType === "Tinggal di panti" && data.residenceStatus === "Masih menerima layanan";
        const outside = data.residenceStatus === "Sudah keluar" || (data.serviceType === "Layanan nonresidensial" && data.residenceStatus === "Masih menerima layanan");
        return [index + 1, text(data.name), mark(data.gender === "Laki-laki"), mark(data.gender === "Perempuan"), text(data.nik), text(data.familyCardNumber), text(data.religion), [data.birthPlace, data.birthDateUnknown ? "" : birthDate(data.birthDate)].filter(Boolean).join(", "), text(parent), text(data.originAddress), mark(data.orphanFather), mark(data.orphanMother), mark(data.economicallyVulnerable), text(education), mark(data.healthCoverageStatus === "Ada"), mark(data.healthCoverageStatus === "Tidak ada"), mark(inside), mark(outside)];
    }
    function group(items, organizations) {
        const groups = new Map(organizations.filter((org) => org.pillar === "lks").map((org) => [org.id, { id: org.id, title: org.title, rows: [] }]));
        for (const item of items) {
            const orgId = item.organizationId || (organizations.length === 1 ? organizations[0].id : null);
            let target = groups.get(orgId);
            if (!target && organizations.length === 1) target = groups.get(organizations[0].id);
            if (!target) throw new Error("Lembaga salah satu klien belum tersedia. Muat ulang rekap sebelum mengunduh.");
            const clientData = item.data || item;
            target.rows.push(row(clientData, target.rows.length));
        }
        return [...groups.values()];
    }
    function csvCell(value) {
        let content = text(value);
        // Excel must not evaluate source text or round 16-digit identifiers.
        if (/^[\s]*[=+\-@]/.test(content) || /^[\t\r\n]/.test(content) || /^0\d+$|^\d{15,}$/.test(content)) content = "'" + content;
        return '"' + content.replace(/"/g, '""') + '"';
    }
    function csv(groups) {
        const result = [];
        for (const org of groups) {
            const title = (value) => [value, ...Array(17).fill("")];
            if (result.length) result.push(Array(18).fill(""));
            result.push(title("FORMAT ISIAN DATA LKSA"), title("DATA PENGHUNI LKSA"), title(`NAMA LKSA : ${org.title}`), headers, subheaders, ...org.rows);
        }
        return "\uFEFF" + result.map((values) => values.map(csvCell).join(",")).join("\r\n") + "\r\n";
    }
    const columnWidths = [5, 25, 5, 5, 23, 23, 15, 28, 27, 38, 7, 7, 9, 23, 7, 10, 14, 14];
    // Store-only ZIP needs no dependency or transmission of resident data.
    const zip = (parts) => window.SidayaXlsx.zip(parts, "Ukuran ekspor terlalu besar. Pilih satu lembaga.");
    const stylesXml = `<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="2">
    <font><sz val="11"/><name val="Calibri"/></font>
    <font><b/><sz val="11"/><name val="Calibri"/></font>
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
  <cellXfs count="5">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
    <xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0"/>
    <xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>
    <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
  </cellXfs>
</styleSheet>`;

    function worksheet(org) {
        const rows = org.rows.length ? org.rows : [Array(18).fill("")];
        const xmlRows = [];

        xmlRows.push('<row r="1" ht="24" customHeight="1"><c r="A1" s="1" t="inlineStr"><is><t>FORMAT ISIAN DATA LKSA</t></is></c></row>');
        xmlRows.push('<row r="2" ht="20" customHeight="1"><c r="A2" s="1" t="inlineStr"><is><t>DATA PENGHUNI LKSA</t></is></c></row>');
        xmlRows.push(`<row r="3" ht="20" customHeight="1"><c r="A3" s="1" t="inlineStr"><is><t>${xml("NAMA LKSA : " + org.title)}</t></is></c></row>`);
        xmlRows.push('<row r="4"/>');

        let r5 = '<row r="5" ht="28" customHeight="1">';
        headers.forEach((h, col) => {
            const addr = String.fromCharCode(65 + col) + "5";
            r5 += `<c r="${addr}" s="2" t="inlineStr"><is><t>${xml(h)}</t></is></c>`;
        });
        r5 += '</row>';
        xmlRows.push(r5);

        let r6 = '<row r="6" ht="22" customHeight="1">';
        subheaders.forEach((sh, col) => {
            const addr = String.fromCharCode(65 + col) + "6";
            r6 += `<c r="${addr}" s="2" t="inlineStr"><is><t>${xml(sh)}</t></is></c>`;
        });
        r6 += '</row>';
        xmlRows.push(r6);

        const centerCols = new Set([0, 2, 3, 6, 10, 11, 12, 14, 15, 16, 17]);
        rows.forEach((rowVals, idx) => {
            const rowNum = idx + 7;
            let rStr = `<row r="${rowNum}" ht="22" customHeight="1">`;
            rowVals.forEach((val, col) => {
                const addr = String.fromCharCode(65 + col) + rowNum;
                const style = centerCols.has(col) ? "3" : "4";
                if (col === 0 && typeof val === "number") {
                    rStr += `<c r="${addr}" s="${style}"><v>${val}</v></c>`;
                } else {
                    rStr += `<c r="${addr}" s="${style}" t="inlineStr"><is><t xml:space="preserve">${xml(val)}</t></is></c>`;
                }
            });
            rStr += '</row>';
            xmlRows.push(rStr);
        });

        const colsXml = `<cols>${columnWidths.map((w, i) => `<col min="${i + 1}" max="${i + 1}" width="${w}" customWidth="1"/>`).join("")}</cols>`;

        const mergeList = [
            "A1:R1", "A2:R2", "A3:R3",
            "A5:A6", "B5:B6", "C5:D5", "E5:E6", "F5:F6", "G5:G6", "H5:H6", "I5:I6", "J5:J6",
            "K5:M5", "N5:N6", "O5:P5", "Q5:R5"
        ];
        const mergesXml = `<mergeCells count="${mergeList.length}">${mergeList.map((m) => `<mergeCell ref="${m}"/>`).join("")}</mergeCells>`;

        return `<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <dimension ref="A1:R${rows.length + 6}"/>
  <sheetViews><sheetView workbookViewId="0"/></sheetViews>
  <sheetFormatPr defaultRowHeight="15"/>
  ${colsXml}
  <sheetData>
    ${xmlRows.join("\n    ")}
  </sheetData>
  ${mergesXml}
  <pageMargins left="0.25" right="0.25" top="0.35" bottom="0.35" header="0.15" footer="0.15"/>
  <pageSetup paperSize="8" orientation="landscape" fitToWidth="1" fitToHeight="0"/>
</worksheet>`;
    }

    function xlsx(groups) {
        if (!groups.length) throw new Error("Tidak ada lembaga sesuai filter untuk diekspor.");
        const names = new Set(), sheetNames = [];
        const sheets = groups.map((org, index) => {
            const base = text(org.title).replace(/[\\/*?:\[\]\u0000-\u001F]/g, " ").replace(/^'+|'+$/g, "").trim().slice(0, 31) || "LKS";
            let name = base, suffix = 1;
            while (names.has(name.toLocaleLowerCase("id-ID"))) { const tail = ` (${++suffix})`; name = base.slice(0, 31 - tail.length) + tail; }
            names.add(name.toLocaleLowerCase("id-ID"));
            sheetNames.push(name);
            return `<sheet name="${xml(name)}" sheetId="${index + 1}" r:id="rIdSheet${index + 1}"/>`;
        }).join("");

        const parts = {
            "[Content_Types].xml": `<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
  ${groups.map((_, i) => `<Override PartName="/xl/worksheets/sheet${i + 1}.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>`).join("")}
</Types>`,
            "_rels/.rels": `<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>`,
            "xl/_rels/workbook.xml.rels": `<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rIdStyles" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
  ${groups.map((_, i) => `<Relationship Id="rIdSheet${i + 1}" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet${i + 1}.xml"/>`).join("")}
</Relationships>`,
            "xl/workbook.xml": `<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>${sheets}</sheets>
</workbook>`,
            "xl/styles.xml": stylesXml
        };

        groups.forEach((org, index) => {
            parts[`xl/worksheets/sheet${index + 1}.xml`] = worksheet(org);
        });

        return zip(parts);
    }

    const cleanPdfText = (value) =>
        String(value ?? "")
            .replace(/[\u0000-\u0008\u000b\u000c\u000e-\u001f]/g, "")
            .replace(/[\u2010-\u2015]/g, "-")
            .replace(/\u00a0/g, " ")
            .trim();

    function buildLksPdf(groups, todayFormatted) {
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

        pdf.setProperties({
            title: "Rekap Penghuni LKSA - Dinas Sosial Kota Banjarbaru",
            subject: "Format Isian Data LKSA - Rekap Penghuni LKSA",
            author: "SiDaya - Dinas Sosial Kota Banjarbaru",
            creator: "SiDaya LKS Workspace",
        });

        const setFont = (size = 8, style = "normal", color = [30, 41, 59]) => {
            pdf.setFont(fontName, style);
            pdf.setFontSize(size);
            pdf.setTextColor(...color);
        };

        const left = 8.5;
        const contentWidth = 280;
        const pageBottom = 192;

        const cols = [
            { id: 0, width: 8, align: "center" },   // NO
            { id: 1, width: 26, align: "left" },    // NAMA ANAK
            { id: 2, width: 7, align: "center" },   // JK L
            { id: 3, width: 7, align: "center" },   // JK P
            { id: 4, width: 26, align: "center" },  // NIK
            { id: 5, width: 26, align: "center" },  // NO KK
            { id: 6, width: 14, align: "center" },  // AGAMA
            { id: 7, width: 26, align: "left" },    // TTL
            { id: 8, width: 25, align: "left" },    // ORTU
            { id: 9, width: 28, align: "left" },    // ALAMAT
            { id: 10, width: 8, align: "center" },  // YATIM
            { id: 11, width: 8, align: "center" },  // PIATU
            { id: 12, width: 9, align: "center" },  // DHUAFA
            { id: 13, width: 22, align: "left" },   // PENDIDIKAN
            { id: 14, width: 8, align: "center" },  // BPJS ADA
            { id: 15, width: 10, align: "center" }, // BPJS TDK
            { id: 16, width: 12, align: "center" }, // KET Dalam Panti
            { id: 17, width: 12, align: "center" }, // KET Luar Panti
        ];

        let y = 12;

        const activeGroups = groups.filter((g) => g.rows && g.rows.length > 0);
        const groupsToRender = activeGroups.length ? activeGroups : groups;

        groupsToRender.forEach((org, groupIndex) => {
            if (groupIndex > 0) {
                pdf.addPage();
                y = 12;
            }

            const drawDocHeader = () => {
                setFont(9, "bold", [71, 85, 105]);
                pdf.text("PEMERINTAH KOTA BANJARBARU", left, y);
                y += 4.2;

                setFont(13, "bold", [15, 23, 42]);
                pdf.text("DINAS SOSIAL", left, y);
                y += 4.8;

                setFont(10, "bold", [30, 41, 59]);
                pdf.text("FORMAT ISIAN DATA LKSA — DATA PENGHUNI LKSA", left, y);

                setFont(7.5, "normal", [100, 116, 139]);
                pdf.text(`Sistem Informasi Pemberdayaan Sosial (SiDaya) | Tanggal Cetak: ${todayFormatted}`, left + contentWidth, y, { align: "right" });
                y += 4;

                setFont(9, "bold", [72, 98, 132]);
                pdf.text(`NAMA LKSA : ${org.title.toUpperCase()}`, left, y);
                setFont(7.5, "bold", [51, 65, 85]);
                pdf.text(`Total: ${org.rows.length} Klien / Penghuni`, left + contentWidth, y, { align: "right" });
                y += 3;

                pdf.setDrawColor(30, 41, 59);
                pdf.setLineWidth(0.6);
                pdf.line(left, y, left + contentWidth, y);
                y += 0.8;
                pdf.setDrawColor(203, 213, 225);
                pdf.setLineWidth(0.2);
                pdf.line(left, y, left + contentWidth, y);
                y += 3.5;
            };

            const drawRunningHeader = () => {
                setFont(7.5, "bold", [100, 116, 139]);
                pdf.text(`SiDaya · DATA PENGHUNI LKSA — ${org.title.toUpperCase()}`, left, 10);
                pdf.text(`Tanggal Cetak: ${todayFormatted}`, left + contentWidth, 10, { align: "right" });
                pdf.setDrawColor(226, 232, 240);
                pdf.setLineWidth(0.2);
                pdf.line(left, 11.8, left + contentWidth, 11.8);
                y = 13.5;
            };

            const drawTableHeader = () => {
                const h1 = 5.2;
                const h2 = 4.8;
                const totalH = h1 + h2;

                pdf.setFillColor(30, 41, 59);
                pdf.rect(left, y, contentWidth, h1 + h2, "F");

                pdf.setDrawColor(51, 65, 85);
                pdf.setLineWidth(0.2);

                setFont(6.5, "bold", [255, 255, 255]);

                let curX = left;

                // Col 0: NO
                pdf.rect(curX, y, cols[0].width, h1 + h2, "S");
                pdf.text("NO", curX + cols[0].width / 2, y + 6, { align: "center" });
                curX += cols[0].width;

                // Col 1: NAMA ANAK
                pdf.rect(curX, y, cols[1].width, h1 + h2, "S");
                pdf.text("NAMA ANAK", curX + cols[1].width / 2, y + 6, { align: "center" });
                curX += cols[1].width;

                // Col 2-3: JENIS KELAMIN
                const jkWidth = cols[2].width + cols[3].width;
                pdf.rect(curX, y, jkWidth, h1, "S");
                pdf.text("JENIS KELAMIN", curX + jkWidth / 2, y + 3.7, { align: "center" });
                pdf.rect(curX, y + h1, cols[2].width, h2, "S");
                pdf.text("L", curX + cols[2].width / 2, y + h1 + 3.4, { align: "center" });
                pdf.rect(curX + cols[2].width, y + h1, cols[3].width, h2, "S");
                pdf.text("P", curX + cols[2].width + cols[3].width / 2, y + h1 + 3.4, { align: "center" });
                curX += jkWidth;

                // Col 4: NIK
                pdf.rect(curX, y, cols[4].width, h1 + h2, "S");
                pdf.text("NIK", curX + cols[4].width / 2, y + 6, { align: "center" });
                curX += cols[4].width;

                // Col 5: NO. KK
                pdf.rect(curX, y, cols[5].width, h1 + h2, "S");
                pdf.text("NO. KK", curX + cols[5].width / 2, y + 6, { align: "center" });
                curX += cols[5].width;

                // Col 6: AGAMA
                pdf.rect(curX, y, cols[6].width, h1 + h2, "S");
                pdf.text("AGAMA", curX + cols[6].width / 2, y + 6, { align: "center" });
                curX += cols[6].width;

                // Col 7: TEMPAT, TGL LAHIR
                pdf.rect(curX, y, cols[7].width, h1 + h2, "S");
                pdf.text("TEMPAT, TGL LAHIR", curX + cols[7].width / 2, y + 6, { align: "center" });
                curX += cols[7].width;

                // Col 8: NAMA ORANG TUA
                pdf.rect(curX, y, cols[8].width, h1 + h2, "S");
                pdf.text("NAMA ORANG TUA", curX + cols[8].width / 2, y + 6, { align: "center" });
                curX += cols[8].width;

                // Col 9: ALAMAT ASAL
                pdf.rect(curX, y, cols[9].width, h1 + h2, "S");
                pdf.text("ALAMAT ASAL", curX + cols[9].width / 2, y + 6, { align: "center" });
                curX += cols[9].width;

                // Col 10-12: STATUS
                const stWidth = cols[10].width + cols[11].width + cols[12].width;
                pdf.rect(curX, y, stWidth, h1, "S");
                pdf.text("STATUS", curX + stWidth / 2, y + 3.7, { align: "center" });
                pdf.rect(curX, y + h1, cols[10].width, h2, "S");
                pdf.text("YATIM", curX + cols[10].width / 2, y + h1 + 3.4, { align: "center" });
                pdf.rect(curX + cols[10].width, y + h1, cols[11].width, h2, "S");
                pdf.text("PIATU", curX + cols[10].width + cols[11].width / 2, y + h1 + 3.4, { align: "center" });
                pdf.rect(curX + cols[10].width + cols[11].width, y + h1, cols[12].width, h2, "S");
                pdf.text("DHUAFA", curX + cols[10].width + cols[11].width + cols[12].width / 2, y + h1 + 3.4, { align: "center" });
                curX += stWidth;

                // Col 13: PENDIDIKAN SAAT INI
                pdf.rect(curX, y, cols[13].width, h1 + h2, "S");
                pdf.text("PENDIDIKAN", curX + cols[13].width / 2, y + 4.5, { align: "center" });
                pdf.text("SAAT INI", curX + cols[13].width / 2, y + 7.8, { align: "center" });
                curX += cols[13].width;

                // Col 14-15: STATUS BPJS
                const bpjsWidth = cols[14].width + cols[15].width;
                pdf.rect(curX, y, bpjsWidth, h1, "S");
                pdf.text("STATUS BPJS", curX + bpjsWidth / 2, y + 3.7, { align: "center" });
                pdf.rect(curX, y + h1, cols[14].width, h2, "S");
                pdf.text("ADA", curX + cols[14].width / 2, y + h1 + 3.4, { align: "center" });
                pdf.rect(curX + cols[14].width, y + h1, cols[15].width, h2, "S");
                pdf.text("TDK ADA", curX + cols[14].width + cols[15].width / 2, y + h1 + 3.4, { align: "center" });
                curX += bpjsWidth;

                // Col 16-17: KETERANGAN
                const ketWidth = cols[16].width + cols[17].width;
                pdf.rect(curX, y, ketWidth, h1, "S");
                pdf.text("KETERANGAN", curX + ketWidth / 2, y + 3.7, { align: "center" });
                pdf.rect(curX, y + h1, cols[16].width, h2, "S");
                pdf.text("Dalam Panti", curX + cols[16].width / 2, y + h1 + 3.4, { align: "center" });
                pdf.rect(curX + cols[16].width, y + h1, cols[17].width, h2, "S");
                pdf.text("Luar Panti", curX + cols[16].width + cols[17].width / 2, y + h1 + 3.4, { align: "center" });
                curX += ketWidth;

                y += totalH;
            };

            drawDocHeader();
            drawTableHeader();

            org.rows.forEach((rVals, rowIndex) => {
                setFont(6.5, "normal");

                const cellLines = cols.map((col, cIdx) => {
                    const rawVal = cleanPdfText(rVals[cIdx] || "");
                    return pdf.splitTextToSize(rawVal, col.width - 2);
                });

                const maxLines = Math.max(...cellLines.map((lines) => lines.length), 1);
                const lineHeight = 3.2;
                const rowHeight = Math.max(5.8, maxLines * lineHeight + 2);

                if (y + rowHeight > pageBottom) {
                    pdf.addPage();
                    drawRunningHeader();
                    drawTableHeader();
                }

                if (rowIndex % 2 === 1) {
                    pdf.setFillColor(248, 250, 252);
                    pdf.rect(left, y, contentWidth, rowHeight, "F");
                }

                pdf.setDrawColor(226, 232, 240);
                pdf.setLineWidth(0.15);
                pdf.rect(left, y, contentWidth, rowHeight, "S");

                let curX = left;
                cols.forEach((col, cIdx) => {
                    if (cIdx > 0) {
                        pdf.line(curX, y, curX, y + rowHeight);
                    }

                    const lines = cellLines[cIdx];
                    const totalTextH = lines.length * lineHeight;
                    let textY = y + (rowHeight - totalTextH) / 2 + 2.2;

                    setFont(6.5, cIdx === 1 ? "bold" : "normal", [30, 41, 59]);

                    lines.forEach((line) => {
                        if (col.align === "center") {
                            pdf.text(line, curX + col.width / 2, textY, { align: "center" });
                        } else if (col.align === "right") {
                            pdf.text(line, curX + col.width - 1.2, textY, { align: "right" });
                        } else {
                            pdf.text(line, curX + 1.2, textY);
                        }
                        textY += lineHeight;
                    });

                    curX += col.width;
                });

                y += rowHeight;
            });
        });

        const totalPages = pdf.getNumberOfPages();
        for (let p = 1; p <= totalPages; p++) {
            pdf.setPage(p);
            pdf.setDrawColor(203, 213, 225);
            pdf.setLineWidth(0.2);
            pdf.line(left, 198, left + contentWidth, 198);

            setFont(6.5, "normal", [100, 116, 139]);
            pdf.text(
                "Dokumen resmi SiDaya — Dinas Sosial Kota Banjarbaru | Rekap Penghuni LKSA",
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

    async function download(format, items, organizations, today) {
        const groups = group(items, organizations);
        if (!groups.length) throw new Error("Tidak ada lembaga sesuai filter untuk diekspor.");
        let blob;
        let ext = format === "xlsx" || format === "excel" ? "xlsx" : format === "pdf" ? "pdf" : "csv";
        if (ext === "pdf") {
            const todayFormatted = birthDate(today) || today;
            await window.SidayaPdf?.ready();
            blob = buildLksPdf(groups, todayFormatted);
        } else if (ext === "csv") {
            blob = new Blob([csv(groups)], { type: "text/csv;charset=utf-8" });
        } else if (ext === "xlsx") {
            blob = xlsx(groups);
        } else {
            throw new Error("Format unduhan tidak dikenali.");
        }
        const url = URL.createObjectURL(blob);
        const anchor = document.createElement("a");
        anchor.href = url;
        const orgTitle = groups.length === 1 ? "_" + text(groups[0].title).replace(/[^a-zA-Z0-9_-]/g, "_").replace(/_+/g, "_") : "";
        anchor.download = `Data_Klien_LKSA${orgTitle}_${text(today).replace(/[^0-9-]/g, "")}.${ext}`;
        document.body.append(anchor);
        anchor.click();
        anchor.remove();
        setTimeout(() => URL.revokeObjectURL(url), 1000);
    }
    return { headers, subheaders, row, group, csv, xlsx, download };
})();
