// Memeriksa naskah terhadap aturan stop-slop, kecocokan sitasi, dan rujukan gambar serta tabel.
const fs = require('fs');
const path = require('path');

const BERKAS = ['bab-1.js', 'bab-2.js', 'bab-3.js', 'black-box.js', 'bab-4.js'];
const sumber = BERKAS.map((b) => fs.readFileSync(path.join(__dirname, b), 'utf8')).join('\n');

const paragrafNaskah = [...sumber.matchAll(/^\s*(?:paragraf|uraian|butirNomor)\((['`])(.*)\1\),?$/gm)]
  .map((m) => m[2].replace(/\\'/g, "'").replace(/\{\{\[nanti diisi[^\]]*\]\}\}/g, '').replace(/\{\{|\}\}|\*/g, ''));
const kalimat = paragrafNaskah.flatMap((p) => p.match(/[^.!?]+(?:\.(?=\s|$)|[!?])/g) || []).map((k) => k.trim());

const POLA = {
  'garis panjang': /[—–]/,
  'kata penguat atau pengisi': /\b(sangat|benar-benar|sungguh|tentu|tentunya|jelas|secara|pada dasarnya|sebenarnya|tampaknya|cukup|sekadar|hanya saja|tentu saja)\b/i,
  'pembuka kosong': /\b(perlu dicatat|hal ini|dengan demikian|oleh karena itu|di era|seiring|dalam rangka|adapun|berdasarkan hal tersebut)\b/i,
  'kontras berpola': /\b(bukan\b.*\bmelainkan|tidak hanya\b.*\b(tetapi|melainkan)|bukan hanya)\b/i,
  'lazim mutlak': /\b(semua|seluruh|selalu|setiap|tidak pernah)\b/i,
  'bentuk pasif di-': /\b(di(?!nas\b|nas|sabilitas|gital|a\b|mana|ri\b|sini|sana)[a-z]{3,}(kan|i)?)\b/i,
  'rujukan meta': /\b(menunjukkan|memuat|merangkum|menjelaskan|merinci|bab ini|subbab ini|pada bab)\b/i,
};

const hasil = {};
Object.entries(POLA).forEach(([nama, pola]) => { hasil[nama] = kalimat.filter((k) => pola.test(k)); });

const awalPenulis = kalimat.filter((k) => /^Penulis\b/.test(k)).length;
let beruntun = 0;
paragrafNaskah.forEach((p) => {
  const k = p.match(/[^.!?]+(?:\.(?=\s|$)|[!?])/g) || [];
  for (let i = 1; i < k.length; i += 1) if (/^\s*Penulis\b/.test(k[i]) && /^\s*Penulis\b/.test(k[i - 1])) beruntun += 1;
});
const pembuka = {};
kalimat.forEach((k) => { const a = k.split(' ')[0]; pembuka[a] = (pembuka[a] || 0) + 1; });

console.log(`paragraf ${paragrafNaskah.length}, kalimat ${kalimat.length}`);
console.log(`kalimat berawalan Penulis: ${awalPenulis} (${Math.round((awalPenulis / kalimat.length) * 100)}%), beruntun: ${beruntun}`);
console.log('pembuka tersering:', Object.entries(pembuka).sort((a, b) => b[1] - a[1]).slice(0, 8).map(([a, n]) => `${a}=${n}`).join(', '));
Object.entries(hasil).forEach(([nama, daftar]) => {
  console.log(`\n[${nama}] ${daftar.length}`);
  if (process.argv.includes('--rinci')) daftar.forEach((k) => console.log(`  - ${k.slice(0, 130)}`));
});

// Sitasi: tiap nama yang disitasi harus ada di daftar pustaka, dan sebaliknya.
const pustaka = [...sumber.matchAll(/pustaka\('((?:[^'\\]|\\.)*)'\)/g)].map((m) => m[1].replace(/\\'/g, "'"));
const entri = [...new Set(pustaka)];
const teksNaskah = paragrafNaskah.join(' ');
const namaEntri = (e) => e.split(/[,.(]/)[0].trim();
const tidakDisitasi = entri.filter((e) => {
  const nama = namaEntri(e);
  const tahun = (e.match(/\((\d{4}|t\.t\.-[a-e])/) || [])[1];
  return !teksNaskah.includes(nama.split(' ').pop()) && !teksNaskah.includes(nama) ? true : (tahun && !teksNaskah.includes(tahun));
});
console.log(`\n[daftar pustaka] ${entri.length} entri; tidak ditemukan di naskah: ${tidakDisitasi.map(namaEntri).join('; ') || 'tidak ada'}`);
// Format sitasi: (Hasanah dkk. 2023), (Anggi dan Ilhadi 2026), beberapa sumber dipisah titik koma.
const formatLama = [...teksNaskah.matchAll(/[A-Z][\w.'-]+(?: dkk\.| dan [A-Z][\w'-]+)? \(\d{4}[a-z]?\)|\([^()]*, (?:\d{4}|t\.t\.)[^()]*\)/g)].map((m) => m[0]);
console.log(`[sitasi] format lama: ${formatLama.join('; ') || 'tidak ada'}`);
const sitasi = [...teksNaskah.matchAll(/([A-Z][\w.'-]+(?: [A-Z][\w'-]+)*)(?: dkk\.| dan [A-Z][\w'-]+)? (\d{4})[a-z]?(?=[;)])/g)].map((m) => `${m[1]} ${m[2]}`);
const tanpaEntri = [...new Set(sitasi)].filter((s) => {
  const [nama, tahun] = [s.slice(0, -5).replace(/^[A-Z]\. /, ''), s.slice(-4)];
  return !entri.some((e) => e.includes(nama.split(' ').pop()) && e.includes(`(${tahun}`));
});
console.log(`[sitasi] tanpa entri pustaka: ${tanpaEntri.join('; ') || 'tidak ada'}`);

// Gambar dan tabel: tiap keterangan harus dirujuk di naskah.
const keterangan = [...sumber.matchAll(/keterangan(?:Gambar|Tabel)\(['`]((?:Gambar|Tabel) \d+\.[\d$]+)/g)].map((m) => m[1]);
const belumDirujuk = keterangan.filter((k) => !k.includes('$') && !teksNaskah.includes(k));
console.log(`[rujukan] keterangan ${keterangan.length}; belum dirujuk paragraf: ${belumDirujuk.join(', ') || 'tidak ada'}`);
