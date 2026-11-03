// Ekspor PDF dan Excel. Server mengirim judul, kolom, dan baris; berkas dibuat di browser.
import './xlsx-writer.js';
import { send } from './api.js';
import { notify } from './toast.js';

const JSPDF = '/assets/vendor/jspdf/jspdf.umd.min.js';
let loading;

function loadPdf() {
    if (window.jspdf?.jsPDF) return Promise.resolve();
    loading ??= new Promise((resolve, reject) => {
        const node = document.createElement('script');
        node.src = JSPDF;
        node.onload = resolve;
        node.onerror = () => {
            loading = null;
            node.remove();
            reject(new Error('Pustaka PDF tidak dapat dimuat. Coba lagi.'));
        };
        document.head.append(node);
    });
    return loading;
}

function save(blob, filename) {
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    document.body.append(link);
    link.click();
    link.remove();
    setTimeout(() => URL.revokeObjectURL(url), 1000);
}

function column(index) {
    let name = '';
    for (let n = index + 1; n > 0; n = Math.floor((n - 1) / 26)) name = String.fromCharCode(65 + ((n - 1) % 26)) + name;
    return name;
}

function buildXlsx({ title, subtitle, columns, rows }) {
    const { xml, zip } = window.SidayaXlsx;
    const cell = (value, col, row, style = 0) => {
        const ref = `${column(col)}${row}`;
        if (typeof value === 'number') return `<c r="${ref}" s="${style}"><v>${value}</v></c>`;
        return `<c r="${ref}" s="${style}" t="inlineStr"><is><t xml:space="preserve">${xml(value)}</t></is></c>`;
    };
    const lines = [[title, 1], [subtitle, 0]].filter(([text]) => text);
    const header = lines.length + 2;
    const sheetRows = [
        ...lines.map(([text, style], index) => `<row r="${index + 1}">${cell(text, 0, index + 1, style)}</row>`),
        `<row r="${header}">${columns.map((name, col) => cell(name, col, header, 2)).join('')}</row>`,
        ...rows.map((row, index) => `<row r="${header + index + 1}">${row.map((value, col) => cell(value, col, header + index + 1)).join('')}</row>`),
    ];
    const widths = columns.map((name, col) => Math.min(48, Math.max(10, name.length + 2, ...rows.map((row) => String(row[col] ?? '').length + 2))));
    return zip({
        '[Content_Types].xml': '<?xml version="1.0" encoding="UTF-8"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/></Types>',
        '_rels/.rels': '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>',
        'xl/workbook.xml': '<?xml version="1.0" encoding="UTF-8"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Data" sheetId="1" r:id="rId1"/></sheets></workbook>',
        'xl/_rels/workbook.xml.rels': '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>',
        'xl/styles.xml': '<?xml version="1.0" encoding="UTF-8"?><styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><name val="Calibri"/></font></fonts><fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FFE4EDF5"/></patternFill></fill></fills><borders count="1"><border/></borders><cellStyleXfs count="1"><xf/></cellStyleXfs><cellXfs count="3"><xf/><xf fontId="1" applyFont="1"/><xf fontId="1" fillId="2" applyFont="1" applyFill="1"/></cellXfs></styleSheet>',
        'xl/worksheets/sheet1.xml': `<?xml version="1.0" encoding="UTF-8"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><cols>${widths.map((width, index) => `<col min="${index + 1}" max="${index + 1}" width="${width}" customWidth="1"/>`).join('')}</cols><sheetData>${sheetRows.join('')}</sheetData></worksheet>`,
    }, 'Data terlalu besar untuk berkas Excel.');
}

// jsPDF bawaan hanya memuat Latin-1, jadi tanda baca lain diganti padanannya.
const clean = (value) => String(value ?? '').replace(/[–—]/g, '-').replace(/[‘’]/g, "'").replace(/[“”]/g, '"').replace(/…/g, '...').replace(/[^\x09\x0A\x20-\x7E\xA0-\xFF]/g, '');

function buildPdf({ title, subtitle, columns, rows }) {
    const doc = new window.jspdf.jsPDF({ orientation: columns.length > 4 ? 'landscape' : 'portrait', unit: 'pt', format: 'a4' });
    const margin = 36;
    const width = doc.internal.pageSize.getWidth() - margin * 2;
    const bottom = doc.internal.pageSize.getHeight() - margin;
    const weights = columns.map((name, col) => Math.min(30, Math.max(6, name.length, ...rows.slice(0, 200).map((row) => String(row[col] ?? '').length))));
    const total = weights.reduce((sum, value) => sum + value, 0);
    const widths = weights.map((value) => (value / total) * width);
    const lineHeight = 11;
    let y = margin;

    doc.setFont('helvetica', 'bold').setFontSize(14).text(clean(title), margin, y);
    y += 16;
    if (subtitle) {
        doc.setFont('helvetica', 'normal').setFontSize(9).text(clean(subtitle), margin, y);
        y += 14;
    }
    const drawRow = (cells, header) => {
        doc.setFont('helvetica', header ? 'bold' : 'normal').setFontSize(8);
        const wrapped = cells.map((value, col) => doc.splitTextToSize(clean(value) || '-', widths[col] - 6));
        const height = Math.max(...wrapped.map((lines) => lines.length)) * lineHeight + 6;
        if (y + height > bottom) {
            doc.addPage();
            y = margin;
            if (!header) drawRow(columns, true);
            doc.setFont('helvetica', 'normal').setFontSize(8);
        }
        let x = margin;
        wrapped.forEach((lines, col) => {
            if (header) doc.setFillColor(228, 237, 245).rect(x, y, widths[col], height, 'F');
            doc.setDrawColor(200, 210, 220).rect(x, y, widths[col], height);
            doc.text(lines, x + 3, y + lineHeight);
            x += widths[col];
        });
        y += height;
    };
    drawRow(columns, true);
    rows.forEach((row) => drawRow(row, false));
    return doc.output('blob');
}

export async function runExport(button) {
    const format = button.dataset.exportFormat;
    const url = new URL(button.dataset.exportUrl, window.location.origin);
    new URLSearchParams(window.location.search).forEach((value, key) => {
        if (key !== 'page' && !url.searchParams.has(key)) url.searchParams.set(key, value);
    });
    button.disabled = true;
    try {
        const data = await send(url.toString(), { method: 'GET' });
        if (format === 'pdf') await loadPdf();
        save(format === 'pdf' ? buildPdf(data) : buildXlsx(data), `${data.filename}.${format === 'pdf' ? 'pdf' : 'xlsx'}`);
    } catch (error) {
        notify(error.message || 'Berkas gagal dibuat.');
    } finally {
        button.disabled = false;
    }
}
