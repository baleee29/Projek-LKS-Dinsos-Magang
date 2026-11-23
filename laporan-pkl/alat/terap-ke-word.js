// Memindahkan perubahan Bab II sampai Daftar Pustaka dari hasil gabung.js ke berkas Word yang sudah disunting pengguna.
// Pakai: node terap-ke-word.js <word.docx> <lama.docx> <baru.docx> <keluaran.docx> [--cek]
// lama.docx = hasil gabung.js sebelum naskah diubah, baru.docx = sesudahnya. Bagian Word di luar rentang tidak disentuh.
const fs = require('fs');
const { strFromU8, strToU8, unzipSync, zipSync } = require('fflate');

const [word, lama, baru, keluaran] = process.argv.slice(2);
const AWAL = 'BAB IIHASIL PELAKSANAAN PKL';
const AKHIR = 'LAMPIRAN-LAMPIRAN';

function anakBody(xml) {
  const a = xml.indexOf('>', xml.indexOf('<w:body')) + 1;
  const z = xml.lastIndexOf('</w:body>');
  const isi = xml.slice(a, z);
  const hasil = [];
  let i = 0;
  while (i < isi.length) {
    const m = /^<([\w:]+)([^>]*?)(\/?)>/.exec(isi.slice(i, i + 4000));
    if (!m) throw new Error(`XML tak terduga di ${i}: ${isi.slice(i, i + 60)}`);
    let j = i + m[0].length;
    if (!m[3]) {
      const pola = new RegExp(`<${m[1]}(?=[ >/])[^>]*?(/?)>|</${m[1]}>`, 'g');
      pola.lastIndex = j;
      let dalam = 1;
      while (dalam > 0) {
        const t = pola.exec(isi);
        if (!t) throw new Error(`Tag ${m[1]} tidak tertutup`);
        if (t[0].startsWith('</')) dalam -= 1; else if (!t[1]) dalam += 1;
        j = pola.lastIndex;
      }
    }
    hasil.push(isi.slice(i, j));
    i = j;
  }
  return { kepala: xml.slice(0, a), anak: hasil, ekor: xml.slice(z) };
}

const teks = (el) => (el.match(/<w:t(?: [^>]*)?>[^<]*<\/w:t>/g) || []).map((t) => t.replace(/<[^>]+>/g, '')).join('')
  .replace(/&quot;/g, '"').replace(/&apos;/g, "'").replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&amp;/g, '&');
const kunci = (el) => `${el.startsWith('<w:tbl') ? 'T' : 'P'}${el.includes('<w:drawing') ? 'G' : ''}${el.includes('<w:sectPr') ? 'S' : ''}${el.includes('<w:highlight') ? 'K' : ''}|${teks(el).replace(/\s+/g, ' ').trim()}`;

function rentang(anak) {
  const k = anak.map(kunci);
  const a = k.findLastIndex((x) => x.endsWith(`|${AWAL}`));
  const z = k.findLastIndex((x) => x.endsWith(`|${AKHIR}`));
  if (a < 0 || z < a) throw new Error('Batas rentang tidak ditemukan');
  return [a, z];
}

// Deret operasi dari LCS: sama, hapus (hanya di lama), tambah (hanya di baru).
function banding(x, y) {
  const n = x.length; const m = y.length;
  const d = Array.from({ length: n + 1 }, () => new Int32Array(m + 1));
  for (let i = n - 1; i >= 0; i -= 1) for (let j = m - 1; j >= 0; j -= 1) d[i][j] = x[i] === y[j] ? d[i + 1][j + 1] + 1 : Math.max(d[i + 1][j], d[i][j + 1]);
  const op = []; let i = 0; let j = 0;
  while (i < n && j < m) {
    if (x[i] === y[j]) { op.push(['sama', i, j]); i += 1; j += 1; } else if (d[i + 1][j] >= d[i][j + 1]) { op.push(['hapus', i]); i += 1; } else { op.push(['tambah', j]); j += 1; }
  }
  while (i < n) { op.push(['hapus', i]); i += 1; }
  while (j < m) { op.push(['tambah', j]); j += 1; }
  return op;
}

(async () => {
  const buka = async (p) => { const z = unzipSync(new Uint8Array(fs.readFileSync(p))); return [z, strFromU8(z['word/document.xml'])]; };
  const [zipWord, xmlWord] = await buka(word);
  const [, xmlLama] = await buka(lama);
  const [, xmlBaru] = await buka(baru);
  const W = anakBody(xmlWord); const L = anakBody(xmlLama); const B = anakBody(xmlBaru);
  const [wa, wz] = rentang(W.anak); const [la, lz] = rentang(L.anak); const [ba, bz] = rentang(B.anak);
  const kw = W.anak.slice(wa, wz).map(kunci); const kl = L.anak.slice(la, lz).map(kunci); const kb = B.anak.slice(ba, bz).map(kunci);
  // Word boleh berbeda dari lama.docx pada unsur yang tidak diubah; unsur itu dibiarkan apa adanya.
  const keWord = new Map();
  banding(kl, kw).forEach((o) => { if (o[0] === 'sama') keWord.set(o[1], o[2]); });
  const op = banding(kl, kb);
  const buangWord = new Set(); const sisip = new Map(); let tunda = []; let batas = null;
  // Batas bagian (sectPr) milik paragraf yang diganti dipindahkan ke paragraf pengganti terakhir.
  const ambilTunda = () => {
    if (batas) {
      if (!tunda.length) throw new Error('Paragraf pembawa batas bagian dihapus tanpa pengganti');
      const akhir = tunda[tunda.length - 1];
      if (!akhir.includes('</w:pPr>')) throw new Error('Paragraf pengganti tidak punya pPr');
      tunda[tunda.length - 1] = akhir.replace('</w:pPr>', () => `${batas}</w:pPr>`);
      batas = null;
    }
    const t = tunda; tunda = []; return t;
  };
  let tambah = 0; let buang = 0;
  op.forEach((o) => {
    if (o[0] === 'tambah') {
      // Batas bagian buatan gabung.js merujuk kepala dan kaki halaman berkas lain, jadi dibuang; milik Word yang dipakai.
      const el = B.anak[ba + o[1]].replace(/<w:sectPr[\s\S]*<\/w:sectPr>/, '');
      if (/<w:drawing|<w:sectPr|r:embed|r:id=/.test(el)) throw new Error(`Unsur baru memuat gambar atau batas bagian: ${kunci(el).slice(0, 100)}`);
      tunda.push(el); tambah += 1;
    } else {
      const w = keWord.get(o[1]);
      if (o[0] === 'hapus') {
        if (w === undefined) throw new Error(`Unsur yang diubah tidak ditemukan di Word: ${kl[o[1]].slice(0, 100)}`);
        const lamaW = W.anak[wa + w];
        if (/<w:drawing/.test(lamaW)) throw new Error(`Unsur yang dihapus memuat gambar: ${kw[w].slice(0, 100)}`);
        if (tunda.length || batas) sisip.set(w, [...(sisip.get(w) || []), ...ambilTunda()]);
        const sp = /<w:sectPr[\s\S]*<\/w:sectPr>/.exec(lamaW);
        if (sp) batas = sp[0];
        buangWord.add(w); buang += 1;
      } else if (tunda.length || batas) {
        if (w === undefined) throw new Error(`Tempat sisipan tidak ditemukan di Word: ${kl[o[1]].slice(0, 100)}`);
        sisip.set(w, [...(sisip.get(w) || []), ...ambilTunda()]);
      }
    }
  });
  const hasil = [];
  W.anak.slice(wa, wz).forEach((el, w) => { hasil.push(...(sisip.get(w) || [])); if (!buangWord.has(w)) hasil.push(el); });
  hasil.push(...ambilTunda());
  console.log(`rentang Word ${wz - wa} unsur; dihapus ${buang}, ditambah ${tambah}`);
  if (process.argv.includes('--cek')) return;
  const xml = W.kepala + [...W.anak.slice(0, wa), ...hasil, ...W.anak.slice(wz)].join('') + W.ekor;
  zipWord['word/document.xml'] = strToU8(xml);
  fs.writeFileSync(keluaran, Buffer.from(zipSync(zipWord, { level: 6 })));
  console.log(keluaran);
})().catch((e) => { console.error(e.message); process.exit(1); });
