// Menggabungkan sampul, bagian awal, Bab I, Bab II, dan satu daftar pustaka ke satu berkas Word.
const fs = require('fs');
const path = require('path');
const {
  AlignmentType, BorderStyle, ImageRun, LeaderType, LineRuleType, Packer, PageReference, Paragraph, Tab, Table, TableCell,
  TableRow, TabStopType, TextRun, WidthType,
} = require('docx');
const {
  bagianBab, bagianRomawi, bagianTanpaNomor, dokumenBagian, isiTeks, judulHalaman, penanda, pustaka, tabel, tempatGambar, urutkanPustaka,
} = require('./format');
const bab1 = require('./bab-1');
const bab2 = require('./bab-2');
const bab3 = require('./bab-3');
const bab4 = require('./bab-4');

const JUDUL_BESAR = bab1.JUDUL.toUpperCase();
const JUDUL_KALIMAT = bab1.JUDUL;
const PENULIS = 'Muhammad Iqbal';
const NPM = '2310010439';
const LEBAR_TEKS = 7937;
const SATU_SPASI = { line: 240, lineRule: LineRuleType.AUTO, before: 0, after: 0 };
const SATU_SETENGAH = { line: 360, lineRule: LineRuleType.AUTO, before: 0, after: 0 };

const runTeks = (teks, { ukuran = 24, tebal = false, kuning = false } = {}) => new TextRun({
  text: teks, font: 'Times New Roman', size: ukuran, bold: tebal, highlight: kuning ? 'yellow' : undefined,
});

const barisTetap = (teks, sifat = {}, { sebelum = 0, sesudah = 0, putus = false } = {}) => new Paragraph({
  alignment: AlignmentType.CENTER,
  pageBreakBefore: putus,
  keepNext: true,
  spacing: { ...SATU_SPASI, before: sebelum, after: sesudah },
  children: [runTeks(teks, sifat)],
});

function sampul(putus) {
  return [
    barisTetap('LAPORAN PRAKTEK KERJA LAPANGAN', { ukuran: 28, tebal: true }, { putus, sesudah: 360 }),
    barisTetap(JUDUL_BESAR, { ukuran: 28, tebal: true }, { sesudah: 600 }),
    barisTetap('Oleh :', { tebal: true }, { sesudah: 240 }),
    barisTetap(PENULIS.toUpperCase(), { tebal: true }),
    barisTetap(`NPM: ${NPM}`, { tebal: true }, { sesudah: 480 }),
    new Paragraph({
      alignment: AlignmentType.CENTER,
      spacing: { ...SATU_SPASI, after: 480 },
      children: [new ImageRun({
        type: 'png',
        data: fs.readFileSync(path.join(__dirname, '..', 'aset', 'logo-uniska.png')),
        transformation: { width: 189, height: 189 },
      })],
    }),
    barisTetap('PROGRAM STUDI S1 TEKNIK INFORMATIKA', { ukuran: 28, tebal: true }),
    barisTetap('FAKULTAS TEKNOLOGI INFORMASI', { ukuran: 28, tebal: true }),
    barisTetap('UNIVERSITAS ISLAM KALIMANTAN', { ukuran: 28, tebal: true }),
    barisTetap('MUHAMMAD ARSYAD AL BANJARI', { ukuran: 28, tebal: true }),
    barisTetap('BANJARMASIN', { ukuran: 28, tebal: true }),
    barisTetap('2026', { ukuran: 28, tebal: true }),
  ];
}

const judulKeterangan = new Paragraph({
  alignment: AlignmentType.CENTER,
  pageBreakBefore: true,
  spacing: { ...SATU_SETENGAH, after: 480 },
  children: [runTeks('LEMBAR KETERANGAN TELAH MELAKSANAKAN PRAKTEK KERJA LAPANGAN', { ukuran: 28, tebal: true })],
});

const TANPA_GARIS = { style: BorderStyle.NONE, size: 0, color: 'FFFFFF' };
const selTanda = (isi) => new TableCell({
  width: { size: LEBAR_TEKS / 2, type: WidthType.DXA },
  borders: { top: TANPA_GARIS, bottom: TANPA_GARIS, left: TANPA_GARIS, right: TANPA_GARIS },
  children: isi.map((teks) => new Paragraph({ alignment: AlignmentType.CENTER, spacing: SATU_SPASI, children: isiTeks(teks) })),
});

const kiriSatuSpasi = (teks, opsi = {}) => new Paragraph({
  alignment: AlignmentType.JUSTIFIED,
  spacing: { ...SATU_SPASI, after: opsi.sesudah ?? 120 },
  indent: opsi.indent,
  children: isiTeks(teks),
});

const tengah = (teks, sesudah = 0) => new Paragraph({
  alignment: AlignmentType.CENTER,
  spacing: { ...SATU_SPASI, after: sesudah },
  children: isiTeks(teks),
});

const lembarPengesahan = [
  judulHalaman('LEMBAR PENGESAHAN'),
  new Paragraph({
    alignment: AlignmentType.CENTER,
    spacing: { ...SATU_SPASI, after: 360 },
    children: [runTeks(JUDUL_BESAR, { tebal: true })],
  }),
  kiriSatuSpasi('Laporan Praktek Kerja Lapangan ini telah disidangkan pada :', { sesudah: 200 }),
  kiriSatuSpasi('Hari, tanggal\t: {{[nanti diisi: hari, tanggal bulan tahun sidang]}}', { indent: { left: 1700, hanging: 1700 } }),
  kiriSatuSpasi('Tempat\t: Fakultas Teknologi Informasi Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin', { indent: { left: 1700, hanging: 1700 }, sesudah: 360 }),
  tengah('Menyetujui/mengesahkan :'),
  tengah('Banjarmasin, {{[nanti diisi: bulan]}} 2026', 240),
  new Table({
    width: { size: LEBAR_TEKS, type: WidthType.DXA },
    columnWidths: [LEBAR_TEKS / 2, LEBAR_TEKS / 2],
    rows: [new TableRow({
      children: [
        selTanda(['Ketua Program Studi,', '', '', '', '{{[nanti diisi: nama dan gelar]}}', 'NIP/NIDN. {{[nanti diisi]}}']),
        selTanda(['Dosen Pembimbing,', '', '', '', '{{[nanti diisi: nama dan gelar]}}', 'NIDN. {{[nanti diisi]}}']),
      ],
    })],
  }),
  new Paragraph({ spacing: { ...SATU_SPASI, after: 200 }, children: [] }),
  tengah('Mengetahui :'),
  tengah('Dekan Fakultas Teknologi Informasi'),
  tengah('Universitas Islam Kalimantan', 840),
  tengah('{{[nanti diisi: nama dan gelar]}}'),
  tengah('NIP. {{[nanti diisi]}}'),
];

const satuSetengah = (teks) => new Paragraph({
  alignment: AlignmentType.JUSTIFIED,
  indent: { firstLine: 720 },
  spacing: SATU_SETENGAH,
  children: isiTeks(teks),
});

const tandaKanan = (teks) => new Paragraph({
  alignment: AlignmentType.LEFT,
  indent: { left: 4300 },
  spacing: SATU_SPASI,
  children: isiTeks(teks),
});

// Teks pernyataan mengikuti contoh baku Pedoman PKL, jadi kalimatnya tidak diubah.
const halamanPernyataan = [
  judulHalaman('PERNYATAAN'),
  satuSetengah(`Dengan ini saya menyatakan bahwa Praktek Kerja Lapangan dengan judul "${JUDUL_KALIMAT}" ini beserta seluruh isinya adalah benar-benar karya saya sendiri, dan saya tidak melakukan penjiplakan atau pengutipan dengan cara-cara yang tidak sesuai dengan etika keilmuan yang berlaku dalam masyarakat keilmuan. Atas pernyataan ini, saya siap menanggung risiko / sanksi yang dijatuhkan kepada saya apabila kemudian ditemukan adanya pelanggaran terhadap etika keilmuan dalam karya saya ini, atau ada klaim dari pihak lain terhadap keaslian karya saya ini.`),
  new Paragraph({ spacing: { ...SATU_SPASI, after: 360 }, children: [] }),
  tandaKanan('Banjarmasin, {{[nanti diisi: tanggal bulan]}} 2026'),
  tandaKanan('Yang membuat pernyataan,'),
  new Paragraph({ spacing: { ...SATU_SPASI, after: 960 }, children: [] }),
  tandaKanan(PENULIS),
  tandaKanan(`NPM. ${NPM}`),
];

const butirTerima = (teks) => new Paragraph({
  alignment: AlignmentType.JUSTIFIED,
  indent: { left: 360, hanging: 360 },
  spacing: SATU_SETENGAH,
  children: isiTeks(teks),
});

const kataPengantar = [
  judulHalaman('KATA PENGANTAR'),
  satuSetengah(`Puji syukur penulis panjatkan kepada Tuhan Yang Maha Esa, karena penulis dapat menyelesaikan Laporan Praktek Kerja Lapangan berjudul "${JUDUL_KALIMAT}". Shalawat dan salam penulis sampaikan kepada Nabi Muhammad SAW beserta keluarga beliau. Penulis berterima kasih kepada kedua orang tua atas doa dan dukungan mereka.`),
  satuSetengah('Penulis menyusun laporan ini untuk memenuhi mata kuliah Praktek Kerja Lapangan pada Program Studi S1 Teknik Informatika, Fakultas Teknologi Informasi, Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin. Laporan ini berisi analisis, perancangan, pembangunan, dan pengujian SiDaya. Penulis juga menyampaikan terima kasih kepada:'),
  butirTerima('1. {{[nanti diisi: nama dan gelar]}}, selaku Dekan Fakultas Teknologi Informasi;'),
  butirTerima('2. {{[nanti diisi: nama dan gelar]}}, selaku Ketua Program Studi S1 Teknik Informatika;'),
  butirTerima('3. {{[nanti diisi: nama dan gelar]}}, selaku Dosen Pembimbing;'),
  butirTerima('4. {{[nanti diisi: nama dan gelar]}}, selaku Kepala Dinas Sosial Kota Banjarbaru;'),
  butirTerima('5. Mas Bagus Ratna Indra D.SR., S.E., {{[nanti diisi: jabatan]}}, selaku pembimbing lapangan; dan'),
  butirTerima('6. {{[nanti diisi: pihak lain yang membantu, sebutkan satu per satu]}}.'),
  satuSetengah('Penulis menerima kritik dan saran untuk perbaikan laporan ini. Penulis berharap Dinas Sosial Kota Banjarbaru dapat memakai laporan ini sebagai dokumentasi saat memelihara SiDaya. Laporan ini juga dapat membantu mahasiswa yang menyusun laporan sejenis.'),
  new Paragraph({ spacing: { ...SATU_SPASI, after: 240 }, children: [] }),
  tandaKanan('Banjarmasin, {{[nanti diisi: tanggal bulan]}} 2026'),
  new Paragraph({ spacing: { ...SATU_SPASI, after: 720 }, children: [] }),
  tandaKanan(PENULIS),
];

// Baris daftar: nomor halaman otomatis lewat penanda, atau titik kuning bila bagiannya belum ditulis.
const barisDaftar = (teks, tingkat = 0, rujukan = teks) => new Paragraph({
  alignment: AlignmentType.LEFT,
  indent: { left: tingkat * 360 },
  tabStops: [{ type: TabStopType.RIGHT, position: LEBAR_TEKS, leader: LeaderType.DOT }],
  spacing: SATU_SETENGAH,
  // Contoh Pedoman PKL: judul utama tebal, subbab dan nomor halaman tidak tebal.
  children: [
    ...(tingkat === 0 ? [runTeks(teks, { tebal: true })] : isiTeks(teks)),
    new TextRun({ children: [new Tab()], font: 'Times New Roman', size: 24 }),
    rujukan ? new PageReference(penanda(rujukan)) : runTeks('[nanti diisi]', { kuning: true }),
  ],
});

const labelHalaman = (tebal = false) => new Paragraph({
  alignment: AlignmentType.RIGHT,
  spacing: SATU_SETENGAH,
  children: [runTeks('Halaman', { tebal })],
});

const SUB_BAB_1 = ['1.1 Latar Belakang', '1.2 Alasan Pemilihan Judul', '1.3 Ruang Lingkup', '1.4 Tujuan dan Manfaat'];
const SUB_BAB_2 = [
  [1, '2.1 Lokasi/Tempat PKL'], [2, '2.1.1 Profil dan Alamat Instansi'], [2, '2.1.2 Visi dan Misi'],
  [2, '2.1.3 Struktur Organisasi'], [2, '2.1.4 Tugas Bidang Pemberdayaan Sosial'], [2, '2.1.5 Kegiatan Selama PKL'],
  [1, '2.2 Pengumpulan Data'], [2, '2.2.1 Observasi'], [2, '2.2.2 Wawancara'], [2, '2.2.3 Studi Dokumentasi'],
  [2, '2.2.4 Studi Kepustakaan'], [1, '2.3 Analisis Sistem / Prosedur yang Sedang Berjalan'],
  [2, '2.3.1 Prosedur Pendaftaran LKS'], [2, '2.3.2 Prosedur Pendataan Pengurus dan Klien'],
  [2, '2.3.3 Prosedur Pelaporan dan Surat'], [2, '2.3.4 Analisis Hak Akses'], [1, '2.4 Temuan Permasalahan'],
  [1, '2.5 Solusi Pemecahan Masalah'],
];
const SUB_BAB_3 = [
  [1, '3.1 Usulan Sistem Baru'], [2, '3.1.1 Gambaran Sistem Usulan'], [2, '3.1.2 Usulan *Hardware* dan *Software*'],
  [1, '3.2 Perancangan Model Sistem'], [2, '3.2.1 *Use Case Diagram*'], [2, '3.2.2 *Activity Diagram*'],
  [2, '3.2.3 *Sequence Diagram*'], [2, '3.2.4 *Class Diagram*'], [1, '3.3 Perancangan Database'], [2, '3.3.1 Tabel-Tabel'],
  [2, '3.3.2 Kamus Data'], [2, '3.3.3 Tabel Relasi'], [1, '3.4 Perancangan Antarmuka Sistem'],
  [2, '3.4.1 Perancangan Antarmuka Masukan Sistem'], [2, '3.4.2 Perancangan Antarmuka Keluaran Sistem'],
  [1, '3.5 Hasil Tampilan Program/Aplikasi'], [2, '3.5.1 Tampilan Antarmuka Masukan Sistem'],
  [2, '3.5.2 Tampilan Antarmuka Keluaran Sistem'], [2, '3.5.3 Pengujian'],
];

const daftarIsi = [
  judulHalaman('DAFTAR ISI'),
  labelHalaman(true),
  ...['LEMBAR PENGESAHAN', 'PERNYATAAN', 'KATA PENGANTAR', 'DAFTAR ISI', 'DAFTAR GAMBAR', 'DAFTAR TABEL'].map((t) => barisDaftar(t)),
  barisDaftar('BAB I PENDAHULUAN', 0, 'BAB I'),
  ...SUB_BAB_1.map((t) => barisDaftar(t, 1)),
  barisDaftar('1.4.1 Tujuan', 2),
  barisDaftar('1.4.2 Manfaat', 2),
  barisDaftar('1.5 Sistematika Penulisan', 1),
  barisDaftar('BAB II HASIL PELAKSANAAN PKL', 0, 'BAB II'),
  ...SUB_BAB_2.map(([tingkat, t]) => barisDaftar(t, tingkat)),
  barisDaftar('BAB III ANALISIS DAN PEMECAHAN MASALAH', 0, 'BAB III'),
  ...SUB_BAB_3.map(([tingkat, t]) => barisDaftar(t, tingkat)),
  barisDaftar('BAB IV PENUTUP', 0, 'BAB IV'),
  barisDaftar('4.1 Kesimpulan', 1),
  barisDaftar('4.2 Saran', 1),
  barisDaftar('DAFTAR PUSTAKA'),
  barisDaftar('LAMPIRAN-LAMPIRAN'),
  barisDaftar('1. Surat Pengantar', 1, 'LAMPIRAN 1'),
  barisDaftar('2. Surat Balasan dari Dinas Sosial Kota Banjarbaru', 1, 'LAMPIRAN 2'),
  barisDaftar('3. Daftar Hadir dan Agenda Kegiatan PKL', 1, 'LAMPIRAN 3'),
  barisDaftar('4. Scan Kartu Bimbingan PKL', 1, 'LAMPIRAN 4'),
  barisDaftar('5. Scan Sertifikat Seminar/Praktikum atau Pelatihan di Bidang IT', 1, 'LAMPIRAN 5'),
];

const judulLampiran = (nomor, judul) => {
  const hasil = judulHalaman(`LAMPIRAN ${nomor}`);
  return [hasil, new Paragraph({
    alignment: AlignmentType.CENTER,
    spacing: { ...SATU_SPASI, after: 360 },
    children: [runTeks(judul, { tebal: true })],
  })];
};

const lampiran = [
  judulHalaman('LAMPIRAN-LAMPIRAN'),
  ...judulLampiran(1, 'Surat Pengantar'),
  tempatGambar('[nanti diisi: ganti kotak ini dengan scan Surat Pengantar PKL]'),
  ...judulLampiran(2, 'Surat Balasan dari Dinas Sosial Kota Banjarbaru'),
  tempatGambar('[nanti diisi: ganti kotak ini dengan scan surat balasan]'),
  ...judulLampiran(3, 'Daftar Hadir dan Agenda Kegiatan Praktek Kerja Lapangan'),
  kiriSatuSpasi(`NPM : ${NPM}`, { sesudah: 40 }),
  kiriSatuSpasi(`Nama : ${PENULIS}`, { sesudah: 40 }),
  kiriSatuSpasi('Prodi : S1 Teknik Informatika', { sesudah: 40 }),
  kiriSatuSpasi('Fakultas : Teknologi Informasi', { sesudah: 40 }),
  kiriSatuSpasi('Nama Pembimbing Lapangan : Mas Bagus Ratna Indra D.SR., S.E.', { sesudah: 40 }),
  kiriSatuSpasi(`Judul Praktek Kerja Lapangan : ${JUDUL_KALIMAT}`, { sesudah: 200 }),
  tabel([700, 2100, 3437, 1700], [
    ['No', 'Hari/Tanggal', 'Kegiatan', 'Paraf Pembimbing Lapangan'],
    ...Array.from({ length: 30 }, (_, i) => [String(i + 1), '{{[nanti diisi]}}', '{{[nanti diisi]}}', '']),
  ]),
  ...judulLampiran(4, 'Scan Kartu Bimbingan PKL'),
  tempatGambar('[nanti diisi: ganti kotak ini dengan scan kartu bimbingan, minimal 6 kali bimbingan]'),
  ...judulLampiran(5, 'Scan Sertifikat Seminar/Praktikum atau Pelatihan di Bidang IT'),
  tempatGambar('[nanti diisi: ganti kotak ini dengan scan sertifikat]'),
];

const barisKeterangan = (nomor, judul) => new Paragraph({
  alignment: AlignmentType.LEFT,
  tabStops: [{ type: TabStopType.RIGHT, position: LEBAR_TEKS, leader: LeaderType.DOT }],
  spacing: SATU_SETENGAH,
  children: [
    runTeks(nomor, { tebal: true }),
    ...isiTeks(` ${judul}`),
    new TextRun({ children: [new Tab()], font: 'Times New Roman', size: 24 }),
    new PageReference(penanda(nomor)),
  ],
});

const catatanKuning = (teks) => new Paragraph({
  alignment: AlignmentType.LEFT,
  spacing: { ...SATU_SETENGAH, before: 120 },
  children: isiTeks(`{{${teks}}}`),
});

// Daftar gambar dan tabel dibaca dari keterangan pada naskah, jadi selalu mengikuti isi bab.
const keterangan = (jenis) => [...bab2.isi, ...bab3.isi]
  .filter((p) => (p.teksKeterangan || '').startsWith(jenis))
  .map((p) => {
    const [, nomor, judul] = p.teksKeterangan.match(/^(\S+ \d+\.\d+) (.*)$/);
    return barisKeterangan(nomor, judul);
  });

const daftarGambar = [judulHalaman('DAFTAR GAMBAR'), labelHalaman(true), ...keterangan('Gambar')];
const daftarTabel = [judulHalaman('DAFTAR TABEL'), labelHalaman(true), ...keterangan('Tabel')];

// Tiap bab punya daftar pustaka sendiri; berkas gabungan menyatukannya tanpa entri ganda.
const bukanPustaka = (p) => !p.teksPustaka && !(p.teksHalaman || '').startsWith('DAFTAR PUSTAKA');
const semuaPustaka = [...bab1.isi, ...bab2.isi, ...bab3.isi, ...bab4.isi].filter((p) => p.teksPustaka).map((p) => p.teksPustaka);
const pustakaUrut = urutkanPustaka(new Set(semuaPustaka));

const sections = [
  bagianTanpaNomor([
    ...sampul(false),
    ...sampul(true),
    judulKeterangan,
    tempatGambar('[nanti diisi: ganti kotak ini dengan scan surat keterangan dari Dinas Sosial Kota Banjarbaru, mulai tanggal ... sampai dengan tanggal ...]'),
  ]),
  bagianRomawi([...lembarPengesahan, ...halamanPernyataan, ...kataPengantar, ...daftarIsi, ...daftarGambar, ...daftarTabel]),
  bagianBab(bab1.isi.filter(bukanPustaka), 1),
  bagianBab(bab2.isi.filter(bukanPustaka)),
  bagianBab(bab3.isi.filter(bukanPustaka)),
  bagianBab(bab4.isi.filter(bukanPustaka)),
  bagianBab([judulHalaman('DAFTAR PUSTAKA'), ...pustakaUrut.map(pustaka)]),
  bagianBab(lampiran),
];

const tujuan = path.join(__dirname, '..', 'Laporan-PKL-Lengkap.docx');
if (fs.existsSync(tujuan) && !process.argv.includes('--timpa')) {
  console.error('Laporan-PKL-Lengkap.docx sudah ada. Isian kuning yang sudah diketik bisa hilang. Tambahkan --timpa bila memang ingin menimpanya.');
  process.exit(1);
}
Packer.toBuffer(dokumenBagian('Laporan PKL SiDaya', sections)).then((isiBerkas) => {
  fs.writeFileSync(tujuan, isiBerkas);
  console.log(tujuan, `pustaka: ${pustakaUrut.length}`);
});
