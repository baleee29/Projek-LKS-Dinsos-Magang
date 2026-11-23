// Format halaman dan paragraf menurut Pedoman PKL FTI UNISKA Revisi 2.0.
// Penanda isi teks: *miring*, **tebal**, {{kuning}}. Penanda boleh bertumpuk di dalam {{ }}.
const fs = require('fs');
const {
  AlignmentType, Bookmark, BorderStyle, Document, Footer, Header, HeadingLevel, ImageRun, LineRuleType, NumberFormat,
  PageNumber, Paragraph, Tab, Table, TableCell, TableLayoutType, TableRow, TabStopType, TextRun, WidthType,
} = require('docx');

// Nama penanda Word untuk rujukan nomor halaman pada daftar isi, gambar, dan tabel.
const penanda = (teks) => `b_${teks.toLowerCase().replace(/[^a-z0-9]+/g, '_')}`.slice(0, 40);

const FONT = 'Times New Roman';
const CM = 567;
const TAB = 720;
const SPASI_DUA = { line: 480, lineRule: LineRuleType.AUTO, before: 0, after: 0 };
const SPASI_SATU = { line: 240, lineRule: LineRuleType.AUTO, before: 0, after: 0 };

function potongMiringTebal(teks, sifat) {
  const hasil = [];
  const pola = /(\*\*[^*]+\*\*|\*[^*]+\*)/g;
  let awal = 0;
  for (const cocok of teks.matchAll(pola)) {
    if (cocok.index > awal) hasil.push(new TextRun({ text: teks.slice(awal, cocok.index), font: FONT, ...sifat }));
    const isi = cocok[0];
    if (isi.startsWith('**')) hasil.push(new TextRun({ text: isi.slice(2, -2), font: FONT, bold: true, ...sifat }));
    else hasil.push(new TextRun({ text: isi.slice(1, -1), font: FONT, italics: true, ...sifat }));
    awal = cocok.index + isi.length;
  }
  if (awal < teks.length) hasil.push(new TextRun({ text: teks.slice(awal), font: FONT, ...sifat }));
  return hasil;
}

function isiTeks(teks) {
  const hasil = [];
  teks.split(/(\{\{[\s\S]+?\}\})/g).forEach((bagian) => {
    if (!bagian) return;
    if (bagian.startsWith('{{')) hasil.push(...potongMiringTebal(bagian.slice(2, -2), { highlight: 'yellow' }));
    else hasil.push(...potongMiringTebal(bagian, {}));
  });
  return hasil;
}

const paragraf = (teks) => new Paragraph({
  style: 'Isi',
  alignment: AlignmentType.JUSTIFIED,
  indent: { firstLine: TAB },
  children: isiTeks(teks),
});

// Penjelasan di bawah butir sistematika: rata kiri tanpa menjorok baris pertama.
const uraian = (teks) => new Paragraph({
  style: 'Isi',
  alignment: AlignmentType.JUSTIFIED,
  indent: { left: TAB },
  children: isiTeks(teks),
});

const judulBab = (nomor, judul) => new Paragraph({
  heading: HeadingLevel.HEADING_1,
  children: [new Bookmark({
    id: penanda(nomor),
    children: [
      new TextRun({ text: nomor, font: FONT }),
      new TextRun({ text: judul, font: FONT, break: 1 }),
    ],
  })],
});

// Judul subbab: nomor, tab, lalu judul yang sejajar dengan awal paragraf (menjorok 1 tab).
const isiJudul = (teks) => {
  const [, nomor, judul] = teks.match(/^(\d+(?:\.\d+)+)\s+(.*)$/);
  return [new TextRun({ text: nomor }), new TextRun({ children: [new Tab()] }), ...isiTeks(judul)];
};
const judulSub = (heading) => (teks) => new Paragraph({
  heading,
  indent: { left: TAB, hanging: TAB },
  tabStops: [{ type: TabStopType.LEFT, position: TAB }],
  children: [new Bookmark({ id: penanda(teks), children: isiJudul(teks) })],
});
const subBab = judulSub(HeadingLevel.HEADING_2);
const subSubBab = judulSub(HeadingLevel.HEADING_3);

const butirBab = (teks) => new Paragraph({
  style: 'Isi',
  keepNext: true,
  children: [new TextRun({ text: teks, font: FONT, bold: true })],
});

// Daftar pustaka: 1 spasi per baris, 1,5 spasi antarpustaka, baris kedua menjorok 1 tab.
const pustaka = (teks) => {
  const hasil = new Paragraph({
    style: 'Isi',
    alignment: AlignmentType.LEFT,
    spacing: { ...SPASI_SATU, after: 180 },
    indent: { left: TAB, hanging: TAB },
    children: isiTeks(teks),
  });
  hasil.teksPustaka = teks;
  return hasil;
};

const judulHalaman = (teks) => {
  const hasil = new Paragraph({
    heading: HeadingLevel.HEADING_1,
    pageBreakBefore: true,
    children: [new Bookmark({ id: penanda(teks), children: [new TextRun({ text: teks, font: FONT })] })],
  });
  hasil.teksHalaman = teks;
  return hasil;
};

const butirNomor = (teks) => new Paragraph({
  style: 'Isi',
  alignment: AlignmentType.JUSTIFIED,
  indent: { left: 360, hanging: 360 },
  children: isiTeks(teks),
});

// Penjelasan di bawah butir a, b, c: sejajar dengan teks butir.
const uraianButir = (teks) => new Paragraph({
  style: 'Isi',
  alignment: AlignmentType.JUSTIFIED,
  indent: { left: 360 },
  children: isiTeks(teks),
});

// Lebar area teks A4 setelah margin kiri 4 cm dan kanan 3 cm: 14 cm atau 7937 DXA.
const PX_PER_CM = 37.795;

function gambar(berkas, lebarCm, tinggiMaksCm = 19) {
  const data = fs.readFileSync(berkas);
  const lebarAsli = data.readUInt32BE(16);
  const tinggiAsli = data.readUInt32BE(20);
  const skala = Math.min(lebarCm / lebarAsli, tinggiMaksCm / tinggiAsli);
  return new Paragraph({
    alignment: AlignmentType.CENTER,
    keepNext: true,
    spacing: { ...SPASI_SATU, before: 120 },
    children: [new ImageRun({
      type: 'png',
      data,
      transformation: {
        width: Math.round(lebarAsli * skala * PX_PER_CM),
        height: Math.round(tinggiAsli * skala * PX_PER_CM),
      },
    })],
  });
}

const tempatGambar = (teks) => {
  const garis = { style: BorderStyle.DASHED, size: 6, color: '000000', space: 40 };
  return new Paragraph({
    alignment: AlignmentType.CENTER,
    keepNext: true,
    border: { top: garis, bottom: garis, left: garis, right: garis },
    spacing: { line: 360, lineRule: LineRuleType.AUTO, before: 240, after: 120 },
    children: isiTeks(`{{${teks}}}`),
  });
};

// Keterangan gambar di bawah gambar dan keterangan tabel di atas tabel, rata tengah, berjarak 1 spasi.
const nomorKeterangan = (teks) => teks.match(/^(Gambar|Tabel) \d+\.\d+/)[0];
// Label nomor tebal, judul keterangan biasa.
const isiKeterangan = (teks) => {
  const nomor = nomorKeterangan(teks);
  return [new TextRun({ text: nomor, font: FONT, bold: true }), ...isiTeks(teks.slice(nomor.length))];
};

const keteranganGambar = (teks) => {
  const hasil = new Paragraph({
    alignment: AlignmentType.CENTER,
    spacing: { ...SPASI_SATU, before: 240, after: 240 },
    children: [new Bookmark({ id: penanda(nomorKeterangan(teks)), children: isiKeterangan(teks) })],
  });
  hasil.teksKeterangan = teks;
  return hasil;
};

const keteranganTabel = (teks) => {
  const hasil = new Paragraph({
    alignment: AlignmentType.CENTER,
    keepNext: true,
    spacing: { ...SPASI_SATU, before: 240, after: 240 },
    children: [new Bookmark({ id: penanda(nomorKeterangan(teks)), children: isiKeterangan(teks) })],
  });
  hasil.teksKeterangan = teks;
  return hasil;
};

function tabel(lebar, baris) {
  // Tabel pendek tetap utuh dalam satu halaman; tabel panjang boleh berlanjut dengan kepala berulang.
  const utuh = baris.length <= 20;
  const sel = (teks, lebarSel, kepala, terakhir) => new TableCell({
    width: { size: lebarSel, type: WidthType.DXA },
    margins: { top: 50, bottom: 50, left: 90, right: 90 },
    children: [new Paragraph({
      alignment: kepala ? AlignmentType.CENTER : AlignmentType.LEFT,
      keepNext: utuh && !terakhir,
      spacing: SPASI_SATU,
      children: kepala ? [new TextRun({ text: teks.replace(/\*/g, ''), font: FONT, bold: true })] : isiTeks(teks),
    })],
  });
  return new Table({
    width: { size: lebar.reduce((a, b) => a + b, 0), type: WidthType.DXA },
    columnWidths: lebar,
    layout: TableLayoutType.FIXED,
    rows: baris.map((isi, nomor) => new TableRow({
      tableHeader: nomor === 0,
      cantSplit: true,
      children: isi.map((teks, kolom) => sel(teks, lebar[kolom], nomor === 0, nomor === baris.length - 1)),
    })),
  });
}

const jarakSetelahTabel = () => new Paragraph({ spacing: { ...SPASI_SATU, after: 120 }, children: [] });

const nomorHalaman = (alignment) => new Paragraph({
  alignment,
  spacing: SPASI_SATU,
  children: [new TextRun({ children: [PageNumber.CURRENT], font: FONT, size: 24 })],
});

const GAYA = {
  default: {
    document: { run: { font: FONT, size: 24 } },
    heading1: {
      run: { font: FONT, size: 28, bold: true, color: '000000' },
      paragraph: { alignment: AlignmentType.CENTER, spacing: { before: 0, after: 480, line: 360, lineRule: LineRuleType.AUTO }, keepNext: true },
    },
    heading2: {
      run: { font: FONT, size: 24, bold: true, color: '000000' },
      paragraph: { spacing: { ...SPASI_DUA }, keepNext: true, tabStops: [{ type: TabStopType.LEFT, position: TAB }] },
    },
    heading3: {
      run: { font: FONT, size: 24, bold: true, color: '000000' },
      paragraph: { spacing: { ...SPASI_DUA }, keepNext: true },
    },
  },
  paragraphStyles: [{
    id: 'Isi',
    name: 'Isi',
    basedOn: 'Normal',
    run: { font: FONT, size: 24 },
    paragraph: { spacing: SPASI_DUA, widowControl: true },
  }],
};

const HALAMAN = {
  size: { width: 11906, height: 16838 },
  margin: { left: 4 * CM, top: 4 * CM, right: 3 * CM, bottom: 3 * CM, header: 1.5 * CM, footer: 1.5 * CM },
};

const kosong = () => new Paragraph({ spacing: SPASI_SATU, children: [] });

// Bab: nomor di tengah bawah pada halaman awal, lalu di pojok kanan atas. Tanpa halamanAwal, nomor melanjutkan bagian sebelumnya.
const bagianBab = (isi, halamanAwal) => ({
  properties: {
    titlePage: true,
    page: { ...HALAMAN, pageNumbers: { start: halamanAwal, formatType: NumberFormat.DECIMAL } },
  },
  headers: {
    default: new Header({ children: [nomorHalaman(AlignmentType.RIGHT)] }),
    first: new Header({ children: [kosong()] }),
  },
  footers: {
    default: new Footer({ children: [kosong()] }),
    first: new Footer({ children: [nomorHalaman(AlignmentType.CENTER)] }),
  },
  children: isi,
});

const bagianTanpaNomor = (isi) => ({
  properties: { page: HALAMAN },
  headers: { default: new Header({ children: [kosong()] }) },
  footers: { default: new Footer({ children: [kosong()] }) },
  children: isi,
});

// Bagian awal: angka romawi kecil di tengah bawah.
const bagianRomawi = (isi) => ({
  properties: { page: { ...HALAMAN, pageNumbers: { start: 1, formatType: NumberFormat.LOWER_ROMAN } } },
  headers: { default: new Header({ children: [kosong()] }) },
  footers: { default: new Footer({ children: [nomorHalaman(AlignmentType.CENTER)] }) },
  children: isi,
});

const dokumenBagian = (judul, sections) => new Document({ title: judul, creator: 'Penulis', styles: GAYA, sections });

const dokumen = ({ judul, halamanAwal = 1, isi }) => dokumenBagian(judul, [bagianBab(isi, halamanAwal)]);

// APA: untuk penulis yang sama, sumber tanpa tahun (t.t.) mendahului sumber bertahun.
const kunciUrut = (teks) => teks.replace(/\(t\.t\./, '(0000');
const urutkanPustaka = (daftar) => [...daftar].sort((a, b) => kunciUrut(a).localeCompare(kunciUrut(b), 'id'));

module.exports = {
  urutkanPustaka,
  bagianBab, bagianRomawi, bagianTanpaNomor, butirBab, butirNomor, dokumen, dokumenBagian, gambar, isiTeks,
  jarakSetelahTabel, judulBab, judulHalaman, keteranganGambar, keteranganTabel, paragraf, penanda, pustaka, subBab,
  subSubBab, tabel, tempatGambar, uraian, uraianButir,
};
